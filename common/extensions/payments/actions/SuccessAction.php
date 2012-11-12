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

        list($orderId, $billId) = explode('-', $params['OrderId']);
        $bill = Bill::model()->findByPk($billId);
        $channel = $bill->getChannel();
        $sign = $channel->getSignature($params);
        if($sign!=$params['SecurityKey'])
        {
            throw new Exception("Signature mismatch");
        }
        //! FIXME handle it better for great good
        if($bill->transactionId)
            throw new Exception("Bill already have transaction id");
        $bill->transactionId = $params['TransactionID'];
        $channel->booker->status('paid');
        $bill->save();
        echo 'Ok';
        // init order
        $order = Yii::app()->order;
        $order->initByOrderBookingId($orderId);
        $payments = Yii::app()->payments;
        $bookers = $order->getBookers();
        foreach($bookers as $booker)
        {
            if($booker->getStatus()=='paid')
                continue;
            $order->isWaitingForPaymentState($booker->getStatus());
            $bill = $payments->getBillForBooker($booker);
            $channel = $bill->getChannel();
            if($channel->rebill($_REQUEST['RebillAnchor']))
            {
                $booker->status('paid');
            }
            else
            {
                throw new Exception("REFUND ALL THE THINGS");
            }
        }
    }
}
