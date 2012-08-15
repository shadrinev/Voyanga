<?php

class PaymentsController extends CExtController
{
    /**
     * Handles success callbacks/redirects
     */
    public function actionIndex()
    {
        Yii::import("common.extensions.payments.models.Bill");
        $keys = Array("DateTime", "TransactionId", "OrderId", "Amount", "Currency", "SecurityKey");
        $params = Array();
        foreach($keys as $key)
        {
            if(!isset($_REQUEST[$key]))
            {
                throw new Exception("Wrong arguments passed to callback");
            }
            $params[$key]=$_REQUEST[$key];
        }
        $bill = Bill::model()->findByPk($params['OrderId']);
        $sign = Yii::app()->payments->getSignatureFor($bill->channel, $params);
        if($sign!=$params['SecurityKey'])
        {
            throw new Exception("Signature mismatch");
        }
        //! FIXME handle it better for great good
        if($bill->transactionId)
            throw new Exception("Bill already have transaction id");
        $bill->transactionId = $params['TransactionId'];
        $bill->save();
    }

    public function actionResult()
    {
        echo "YAY <br />";
        $orderId = Yii::app()->request->getQuery('Order_ID', False);
        //! FIXME
        if($orderId===false)
        {
            throw new Exception("HANDLE ME MOFO");
        }
        var_dump(Yii::app()->payments->getDataByBillId($orderId));
    }

}