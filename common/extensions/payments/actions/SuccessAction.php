<?php
class SuccessAction extends CAction
{
    public function run()
    {
        Yii::import("common.extensions.payments.models.Bill");
        $keys = Array("DateTime", "TransactionID", "OrderId", "Amount", "Currency", "SecurityKey", "RebillAnchor");
        $params = Array();
        foreach($keys as $key)
        {
            if(!isset($_REQUEST[$key]))
            {
                throw new Exception("Wrong arguments passed to callback. Expected $key");
            }
            $params[$key]=$_REQUEST[$key];
        }

        $parts = explode('-', $params['OrderId']);
        if(count($parts)<2)
            return;
        list($orderId, $billId) = $parts;
        $bill = Bill::model()->findByPk($billId);
        $channel = $bill->getChannel();
        $sign = $channel->getSignature($params);
        if($sign!=$params['SecurityKey'])
        {
            throw new Exception("Signature mismatch");
        }
        //! FIXME handle it better for great good
        if($bill->transactionId && ($params['TransactionID']!=$bill->transactionId))
            throw new Exception("Bill already have transaction id");
        $bill->transactionId = $params['TransactionID'];
        $booker  = new FlightBookerComponent();
        $booker->setFlightBookerFromId($channel->booker->id);
        //FIXME logme
        if($this->getStatus($booker)=='paid')
            return $this->rebill($orderId);

        if($this->getStatus($booker)!='waitingForPayment')
            throw new Exception("Cant resume payment when booker status is " . $this->getStatus($booker));
        $booker->status('paid');
        $bill->save();
        echo 'Ok';
        $this->rebill($orderId);

    }
    private function rebill($orderId){
        // init order
        $order = Yii::app()->order;
        $order->initByOrderBookingId($orderId);
        $payments = Yii::app()->payments;
        $bookers = $order->getBookers();
        foreach($bookers as $booker)
        {
            if($this->getStatus($booker)=='paid'){
                continue;
            }
            if($this->getStatus($booker)!='waitingForPayment'){
                return $this->refund($order);
            }
            
            $order->isWaitingForPaymentState($booker->getStatus());
            $bill = $payments->getBillForBooker($booker->getCurrent());
            $bill->channel = 'ltr';
            $channel =  $bill->getChannel();
            if($channel->rebill($_REQUEST['RebillAnchor']))
            {
                $booker->status('paid');
            }
            else
            {
                $booker->status('paymentError');
                return $this->refund($order);
            }
        }
//     throw new Exception("done");
    }

    //! performs refunds of boookers in given order
    private function refund($order)
    {
        $payments = Yii::app()->payments;
        $bookers = $order->getBookers();

        foreach($bookers as $booker)
        {
            $bill = $payments->getBillForBooker($booker->getCurrent());
            if($this->getStatus($booker)=='paid') {
                if($bill->getChannel()->refund())
                    $booker->status('refundedError');
                else
                    throw new Exception("Refund error");
            } elseif($this->getStatus($booker)=='waitingForPayment') {
                $booker->status('paymentCanceledError');
            } elseif($this->getStatus($booker)!='paymentError') {
                throw new Exception("Wrong status: " . $booker->getStatus());
            }
        }
    }

    //! helper function returns last segment of 2 segment statuses
    private function getStatus($booker)
    {
        $status = $booker->getStatus();
        $parts = explode("/", $status);
        if(count($parts)==2)
            return $parts[1];
        return $parts[0];
    }
}
