<?php
Yii::import("common.extensions.payments.actions.SuccessAction");

class FailureAction extends SuccessAction
{
    protected $keys = Array("DateTime", "TransactionID", "OrderId", "Amount", "Currency", "SecurityKey");
    protected $failure = true;

    protected function handle($bill, $booker, $channel, $orderId)
    {
        $haveRebillAnchor = isset($_REQUEST["RebillAnchor"]);
        //! It is failed transaction it is safe to save transaction id
        // Have to check if this could happen in production
        if(!$haveRebillAnchor)
            $bill->transactionId = $_REQUEST['TransactionID'];

       if($bill->getChannel()->getName() == 'gds_galileo'){
            $bill->channel = 'ltr';
            $bill->save();
            if(!$this->isWaitingForPayment($booker)) {
                $e = new WrongOrderStateError("Wrong status" . $this->getStatus($booker));
                $this->handleException($e);
                return;
            }
            $this->rebill($orderId);
       } else {
           $booker->status('paymentError');
       }
    }
}
