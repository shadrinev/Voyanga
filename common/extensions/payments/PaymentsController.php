<?php

class PaymentsTestController extends CExtController
{
    /**
     * Handles success callbacks/redirects
     */
    public function actionIndex()
    {
        Yii::import("common.extensions.payments.models.Bill");

        $this->renderPartial('test');
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