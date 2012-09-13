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
        /** @var TourBuilderForm $tourForm */
        $tourForm = Yii::app()->user->getState('tourForm');
        $eventId = $tourForm->eventId;
        $startCities = Yii::app()->user->getState('startCities');
        $currentStartCityIndex = Yii::app()->user->getState('startCitiesIndex') - 1;
        $currentStartCity = City::model()->findByPk($startCities[$currentStartCityIndex]->id);
        $startCityId = $currentStartCity->id;
        $event = Event::model()->findByPk($eventId);
        $tripStorage = new TripStorage();
        $order = $tripStorage->saveOrder($event, $startCityId, 'Тур для события "'.$event->title.'" из '.$currentStartCity->caseGen);
        $eventOrder = new EventOrder();
        $eventOrder->startCityId = $startCityId;
        $eventOrder->orderId = $order->id;
        $eventOrder->eventId = $event->id;
        $eventOrder->save();
        $this->controller->redirect($this->controller->createUrl('showEventTrip'));
    }
}
