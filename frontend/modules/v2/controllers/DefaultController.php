<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 27.08.12
 * Time: 13:59
 */
class DefaultController extends CController
{
    public function actionIndex()
    {
        Yii::app()->user->setState('orderBookingId', null);
        Yii::app()->user->setState('todayOrderId', null);
        $events = Event::getRandomEvents();
        $eventsJsonObject = array();
        foreach ($events as $event)
            $eventsJsonObject[] = $event->getJsonObject();
        $eventsJsonObject[0]['active'] = true;
        $this->render('frontend.www.themes.v2.views.default.index', array('events'=>$eventsJsonObject));
    }

    public function actionHotelInfo($cacheId, $hotelId)
    {
        $this->render('frontend.www.themes.v2.views.layouts.main');
    }
}
