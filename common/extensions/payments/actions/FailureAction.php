<?php
Yii::import("common.extensions.payments.actions.SuccessAction");

class FailureAction extends SuccessAction
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
        //! FIXME LOG TRANSACTION
        //! FIXME handle it better for great good
        if($bill->transactionId && ($params['TransactionID']!=$bill->transactionId))
            throw new Exception("Bill already have transaction id");
        $bill->transactionId = $params['TransactionID'];
        if($channel->booker instanceof FlightBooker) {
            $booker  = new FlightBookerComponent();
            $booker->setFlightBookerFromId($channel->booker->id);
        } else {
            $booker  = new HotelBookerComponent();
            $booker->setHotelBookerFromId($channel->booker->id);
        }
        if($bill->channel == 'gds_galileo'){
            $bill->channel = 'ltr';
            if(!$this->isWaitingForPayment($booker))
                throw new Exception("Cant resume payment when booker status is " . $this->getStatus($booker));
            $bill->save();
            $this->rebill($orderId);
        }
            echo 'Ok';
    }
}
