<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 11.07.12
 * Time: 15:49
 * To change this template use File | Settings | File Templates.
 */
class SoftWaitingForPayment extends StageAction
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
            Yii::app()->hotelBooker->status('softStartPayment');
        }
    }

}