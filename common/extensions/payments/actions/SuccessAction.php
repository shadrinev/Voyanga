<?php
Yii::import("common.extensions.payments.Exceptions", true);
Yii::import("common.extensions.payments.models.Bill");

class SuccessAction extends CAction
{
    protected $keys = Array("DateTime", "TransactionID", "OrderId", "Amount", "Currency", "SecurityKey", "RebillAnchor"); 
    protected $failure = false;
    protected $logEntry;

    public function run()
    {
        if($this->failure)
            $method = 'FailureCallback';
        else
            $method = 'SuccessCallback';
        $this->logEntry = PaymentLog::forMethod($method);
        $this->logEntry->request = '{"callback":1}';
        $this->logEntry->response = json_encode($_REQUEST);
        if(isset($_REQUEST['TransactionID']))
            $this->logEntry->transactionId = $_REQUEST['TransactionID'];
        if(isset($_REQUEST['OrderId']))
            $this->logEntry->orderId = $_REQUEST['OrderId'];
        $this->logEntry->save();

        foreach($this->keys as $key)
        {
            if(!isset($_REQUEST[$key]))
            {
                $e = new RequestError("$key not found.");
                $this->handleException($e);
                return;
            }
            $params[$key]=$_REQUEST[$key];
        }

        $parts = explode('-', $params['OrderId']);
        if(count($parts)<2) {
            $e = new RequestError("Wrong OrderId format: " . $params['OrderId']);
            $this->handleException($e);
            return;
        }
        list($orderId, $billId) = $parts;

        $bill = Bill::model()->findByPk($billId);
        $channel = $bill->getChannel();
        $sign = $channel->getSignature($params);
        if($sign!=$params['SecurityKey'])
        {
            $e = new  SignatureError("Signature mismatch actual: ". $params['SecurityKey'] . ". Expected: " . $sign . ".");
            $this->handleException($e);
//            return;
        }

       $booker = $channel->booker;
       if($booker instanceof FlightBooker) {
            $booker  = new FlightBookerComponent();
            $booker->setFlightBookerFromId($channel->booker->id);
        }         // Hoteles are allways wrapped into metabooker
        //FIXME logme
#        if($this->getStatus($booker)=='paid')
#            return;
        if($this->getStatus($booker)=='paymentInProgress')
            return;
        $this->logEntry->startProfile();
        $this->handle($bill, $booker, $channel, $orderId);
        $this->logEntry->finishProfile();
        $this->logEntry->save();
    }
    protected function handle($bill, $booker, $channel, $orderId)
    {
        //! FIXME handle it better for great good
        // This could lead to data loss in current implementation
        // and thus not allowed
        if($bill->transactionId && ($_REQUEST['TransactionID']!=$bill->transactionId)) {
            //! Fixme not sure if we need to log this
            $e = new RequestError("Bill #" . $bill->id . " already have transaction id");
            $this->handleException($e);
#            return;
        }
        $bill->transactionId = $_REQUEST['TransactionID'];

        if(!$this->isWaitingForPayment($booker)) {
           $e = new WrongOrderStateError("Wrong status" . $this->getStatus($booker));
           $this->handleException($e);
           return;
        }
        $payments = Yii::app()->payments;
        $booker->status('paymentInProgress');
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
            $booker->status('paymentInProgress');
            if($channel->rebill($_REQUEST['RebillAnchor']))
            {
                $booker->status('paid');
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
        $this->scheduleTicketing($orderId);
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
                    $this->handleException($e);
                }
            } elseif($this->getStatus($booker)=='waitingForPayment') {
                $booker->status('paymentCanceledError');
            } elseif($this->getStatus($booker)!='paymentError') {
                $e = new WrongOrderStateError("Wrong status" . $this->getStatus($booker));
                $this->handleException($e);
            }
        }
    }

    protected function isWaitingForPayment($booker)
    {
        if($this->getStatus($booker)=='waitingForPayment')
            return true;
        if($this->getStatus($booker)=='paid')
            return true;

        # FIXME FIXME FIXME
        return false;
    }

    //! FIXME moved to payments, refactor
    //! helper function returns last segment of 2 segment statuses
    protected function getStatus($booker)
    {
        $status = $booker->getStatus();
        $parts = explode("/", $status);
        if(count($parts)==2)
            return $parts[1];
        return $parts[0];
    }

    protected function scheduleTicketing($orderId) {
        $order = Yii::app()->order;
        $order->initByOrderBookingId($orderId);
        $payments = Yii::app()->payments;
        $bookers = $payments->preProcessBookers($order->getBookers());
        foreach($bookers as $booker){
//            if(!$this->getStatus($booker)=='paid')
//                return false;
        }
        $res = Yii::app()->cron->add(time() + 75, 'orderticketing', 'cron', array('orderId'=>$orderId));
    }

    protected function handleException($e) {
        $this->logEntry->errorDescription = "Exception " . get_class($e) . ": " .  $e->getMessage();
        $this->logEntry->save();
        Yii::app()->RSentryException->logException($e);
    }
}
