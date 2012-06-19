<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 19.06.12
 * Time: 18:34
 */
class BookerController extends Controller
{
    public function actionFlight()
    {
        $orderFlightVoyage = OrderFlightVoyage::model()->findByPk(21);
        $flightVoyage = unserialize($orderFlightVoyage->object);
        Yii::app()->flightBooker->book($flightVoyage);
    }

    public function actionEnterCredentials()
    {
        echo 'You should enter credentials now!!!';
        echo ' Your booker id = '.Yii::app()->user->getState('flightBookerId');
    }
}
