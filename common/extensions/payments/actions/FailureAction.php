<?php
Yii::import("common.extensions.payments.actions.SuccessAction");

class FailureAction extends SuccessAction
{
    public function run()
    {
        Yii::import("common.extensions.payments.models.Bill");
        $keys = Array("DateTime", "TransactionID", "OrderId", "Amount", "Currency", "SecurityKey");
        $params = Array();
        foreach($keys as $key)
        {
            if(!isset($_REQUEST[$key]))
            {
                throw new Exception("Wrong arguments passed to callback. Expected $key");
            }
            $params[$key]=$_REQUEST[$key];
        }

        $haveRebillAnchor = isset($_REQUEST["RebillAnchor"]);

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
        if(!$haveRebillAnchor)
            $bill->transactionId = $params['TransactionID'];

        $booker = $channel->booker;
        if($channel->booker instanceof FlightBooker) {
            $booker  = new FlightBookerComponent();
            $booker->setFlightBookerFromId($channel->booker->id);
        }

        if($this->getStatus($booker)=='paid')
            return $this->rebill($orderId);

        if($bill->getChannel()->getName() == 'gds_galileo'){
            $bill->channel = 'ltr';
            $bill->save();
            if(!$this->isWaitingForPayment($booker))
                throw new Exception("Cant resume payment when booker status is " . $this->getStatus($booker));
            $this->rebill($orderId);
        }
    }
}
