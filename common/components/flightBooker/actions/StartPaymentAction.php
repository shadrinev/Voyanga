<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 02.07.12
 * Time: 11:13
 * To change this template use File | Settings | File Templates.
 */
class StartPaymentAction extends StageAction
{

    public function execute()
    {
        $payments = Yii::app()->payments;
        $booker = Yii::app()->flightBooker->getCurrent();
        $bill = $payments->getBillForBooker($booker);
        if($bill->transactionId)
        {
            Yii::app()->payments->updateBillStatus($bill);
            // We have transactionId for this bill, check its status and go
            // to next step if everything is ok
            if($bill->status == Bill::STATUS_PREAUTH)
            {
                Yii::app()->flightBooker->status('ticketing');
                return true;
            }
        }
        $params = $payments->getParamsForBillAndBooker($bill, $booker);
        $context = Array('paymentUrl'=>$bill->paymentUrl
                         ,'params'=>$params);
        $this->getController()->render('flightBooker.views.payment_form', $context);
        return;
   }
}
