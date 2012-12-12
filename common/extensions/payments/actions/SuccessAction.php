<?php
Yii::import("common.extensions.payments.Exceptions", true);
Yii::import("common.extensions.payments.models.Bill");

class SuccessAction extends CAction
{
    protected $keys = Array("DateTime", "TransactionID", "OrderId", "Amount", "Currency", "SecurityKey", "RebillAnchor"); 
    public function run()
    {
        $params = Array();
        foreach($this->keys as $key)
        {
            if(!isset($_REQUEST[$key]))
            {
                $e = new RequestError("$key not found.");
                yii::app()->RSentryException->logException($e);
                return;
            }
            $params[$key]=$_REQUEST[$key];
        }

        $parts = explode('-', $params['OrderId']);
        if(count($parts)<2) {
            $e = new RequestError("Wrong OrderId format: " . $params['OrderId']);
            yii::app()->RSentryException->logException($e);
            return;
        }
        list($orderId, $billId) = $parts;

        $bill = Bill::model()->findByPk($billId);
        $channel = $bill->getChannel();
        $sign = $channel->getSignature($params);
        if($sign!=$params['SecurityKey'])
        {
            $e = new  SignatureError("Signature mismatch actual: ". $params['SecurityKey'] . ". Expected: " . $sign . ".");
            yii::app()->RSentryException->logException($e);
//            return;
        }

       $booker = $channel->booker;
        if($booker instanceof FlightBooker) {
            $booker  = new FlightBookerComponent();
            $booker->setFlightBookerFromId($channel->booker->id);
        }         // Hoteles are allways wrapped into metabooker

        $this->handle($bill, $booker, $channel, $orderId);
    }
    protected function handle($bill, $booker, $channel, $orderId)
    {
        //! FIXME handle it better for great good
        // This could lead to data loss in current implementation
        // and thus not allowed
        if($bill->transactionId && ($_REQUEST['TransactionID']!=$bill->transactionId)) {
            //! Fixme more specific exception?
            $e = new RequestError("Bill #" . $bill->id . " already have transaction id");
            yii::app()->RSentryException->logException($e);
            return;
        }
        $bill->transactionId = $_REQUEST['TransactionID'];

        //FIXME logme
        if($this->getStatus($booker)=='paid')
            return $this->rebill($orderId);

        if(!$this->isWaitingForPayment($booker)) {
           $e = new WrongOrderStateError("Wrong status" . $this->getStatus($booker));
            yii::app()->RSentryException->logException($e);
            return;
        }
        $payments = Yii::app()->payments;
        $booker->status('paid');
        $bill->save();

        $this->rebill($orderId);
    }

    protected function rebill($orderId){
        // init order
        $order = Yii::app()->order;
        $order->initByOrderBookingId($orderId);
        $payments = Yii::app()->payments;
        $bookers = $payments->preProcessBookers($order->getBookers());
        foreach($bookers as $booker)
        {
            if($this->getStatus($booker)=='paid'){
                continue;
            }

            if($this->getStatus($booker)!='waitingForPayment'){
                return $this->refund($order);
            }

//            $order->isWaitingForPaymentState($booker->getStatus());
            $bill = $payments->getBillForBooker($booker);
            $channel =  $bill->getChannel();
            if($channel->rebill($_REQUEST['RebillAnchor']))
            {
//                $payments->notifyNemo($booker, $bill);
                $booker->status('paid');
//                $booker->status('ticketing');
                continue;
            }
            if ($channel->getName() == 'gds_galileo')
            {
                $bill->channel = 'ltr';
                $channel =  $bill->getChannel();
                $bill->save();
                if($channel->rebill($_REQUEST['RebillAnchor'])){
                    $booker->status('paid');
                    continue;
                }
            }
            $booker->status('paymentError');
            return $this->refund($order);
        }
//     throw new Exception("done");
    }

    //! performs refunds of boookers in given order
    protected function refund($order)
    {
        $payments = Yii::app()->payments;
        $bookers = $order->getBookers();

        foreach($bookers as $booker)
        {
            $bill = $payments->getBillForBooker($booker->getCurrent());
            if($this->getStatus($booker)=='paid') {
                if($bill->getChannel()->refund())
                    $booker->status('refundedError');
                else {
                    $e =  new RefundError("For bill" . $bill->id);
                    yii::app()->RSentryException->logException($e);
                }
            } elseif($this->getStatus($booker)=='waitingForPayment') {
                $booker->status('paymentCanceledError');
            } elseif($this->getStatus($booker)!='paymentError') {
                $e = new WrongOrderStateError("Wrong status" . $this->getStatus($booker));
                yii::app()->RSentryException->logException($e);
            }
        }
    }

    protected function isWaitingForPayment($booker)
    {
        if($this->getStatus($booker)=='waitingForPayment')
            return true;
        # FIXME FIXME FIXME
        return false;
    }

    //! helper function returns last segment of 2 segment statuses
    protected function getStatus($booker)
    {
        $status = $booker->getStatus();
        $parts = explode("/", $status);
        if(count($parts)==2)
            return $parts[1];
        return $parts[0];
    }
}
