<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 09.07.12
 * Time: 17:16
 * To change this template use File | Settings | File Templates.
 */
class SoftStartPaymentAction extends StageAction
{
    public function execute()
    {
        //TODO: need testing possibility go to state startPaymentAction

        $hotelBookerComponent = new HotelBookerComponent();
        $timeLimit = 123;
        if(($this->hotel->cancelExpiration - time()) > appParams('hotel_payment_time'))
        {
            $res = Yii::app()->cron->add(date(time() + appParams('hotel_payment_time')), 'HotelBooker','ChangeState',array('hotelBookerId'=>$this->hotelBooker->id,'newState'=>'softWaitingForPayment'));
            if($res)
            {
                $this->hotelBooker->saveTaskInfo('paymentTimeLimit',$res);
                return true;
            }
        }

        return false;
    }

}
