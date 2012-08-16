<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 13.07.12
 * Time: 12:32
 */
class HardStartPayment extends StageAction
{
    public function execute()
    {
        if(!isset($_POST['submit']))
        {
            $hotel = Yii::app()->hotelBooker->getCurrent()->hotel;
            $this->getController()->render('hotelBooker.views.payment', array('model'=>$hotel));
        }
        else
        {
            Yii::app()->hotelBooker->status('ticketing');
        }
    }
}
