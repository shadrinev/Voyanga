<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 09.07.12
 * Time: 17:16
 * To change this template use File | Settings | File Templates.
 */
class SoftStartPayment extends StageAction
{
    public function execute()
    {
        //TODO: need testing possibility go to state startPaymentAction
        $payments = Yii::app()->payments;
        $booker = Yii::app()->hotelBooker->getCurrent();
        $bill = $payments->getBillForHotelBooker($booker);
        if($bill->transactionId)
        {
            Yii::app()->payments->updateBillStatus($bill);
            // We have transactionId for this bill, check its status and go
            // to next step if everything is ok
            if($bill->status == Bill::STATUS_PREAUTH)
            {
                Yii::app()->hotelBooker->status('moneyTransfer');
                return true;
            }
            //! FIXME: DO WE NEED THIS
            if($bill->status == Bill::STATUS_PAID)
            {
                Yii::app()->hotelBooker->status('moneyTransfer');
                Yii::app()->hotelBooker->status('done');
                return;
            }
        }
        $params = $bill->params;
        //! FIXME
        //        $params['ReturnUrl'] = $this->controller->createAbsoluteUrl('/booking/hotel/buy/', Array('key'=>$booker->hotel->id));
        $context = Array('paymentUrl'=>$bill->paymentUrl
                         ,'params'=>$params);
        $this->getController()->render('hotelBooker.views.payment_form', $context);
        return;
        //! FIXME $this->hotel is undefined
        if(($this->hotel->cancelExpiration - time()) > appParams('hotel_payment_time'))
        {
            $res = Yii::app()->cron->add(date(time() + appParams('hotel_payment_time')), 'HotelBooker','ChangeState',array('hotelBookerId'=>$this->hotelBooker->id,'newState'=>'softWaitingForPayment'));

            if($res)
            {
                $this->hotelBooker->saveTaskInfo('paymentTimeLimit',$res);
                return true;
            }
            //! FIXME how should we handle this case
        }
        return false;
    }

}
