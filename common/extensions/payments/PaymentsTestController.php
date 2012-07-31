<?php

class PaymentsTestController extends CExtController
{
    public function actionIndex()
    {
        $this->renderPartial('test');
    }

    public function actionResult()
    {
        echo "YAY <br />";
        $orderId = Yii::app()->request->getQuery('Order_ID', False);
        if($orderId===false)
        {
            throw new Exception("HANDLE ME MOFO");
        }
        var_dump(Yii::app()->payments->getDataByOrderId($orderId));
    }

}