<?php
class SuccessAction extends CAction
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

        $parts = explode('-', $params['OrderId']);
        $billId = $parts[0];
        $bill = Bill::model()->findByPk($billId);
        $channel = $bill->getChannel();
        $sign = $channel->getSignature($params);
        if($sign!=$params['SecurityKey'])
        {
            throw new Exception("Signature mismatch");
        }
        //! FIXME handle it better for great good
#        if($bill->transactionId)
#            throw new Exception("Bill already have transaction id");
        $bill->transactionId = $params['TransactionID'];
        $bill->save();
        echo 'Ok';
    }
}
