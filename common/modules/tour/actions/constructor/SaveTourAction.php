<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:00
 */
class SaveTourAction extends CAction
{
    public function run($isTab=false)
    {
        $tourForm = Yii::app()->user->getState('tourForm');
        $eventId = $tourForm->eventId;
        $event = Event::model()->findByPk($eventId);
        $tripStorage = new TripStorage();
        $order = $tripStorage->saveOrder('Тур для события '.$event->title);
        $event->orderId = $order->id;
        $event->update(array('orderId'));
        $this->controller->redirect($this->controller->createUrl('showEventTrip'));
    }
}
