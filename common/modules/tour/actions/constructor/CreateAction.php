<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:00
 */
class CreateAction extends CAction
{
    public function run($isTab=false)
    {
/*        if ($res = Yii::app()->user->getState('trip.tour.form'))
            $model = @unserialize($res);
        else*/
        $model = new TourBuilderForm();
        if (isset($_POST['TourBuilderForm']))
        {
            unset($model);
            $model = new TourBuilderForm();
            $model->attributes = $_POST['TourBuilderForm'];
            $model->trips = array();
            $model->rooms = array();
            if (isset($_POST['TripForm']))
            {
                $validTrips = true;
                $validRooms = true;
                $validStartCity = true;
                foreach ($_POST['TripForm'] as $i=>$attributes)
                {
                    $trip = new TripForm();
                    $trip->attributes = $attributes;
                    $validTrips = $validTrips and $trip->validate();
                    if ($validTrips)
                        $model->trips[] = $trip;
                }
                foreach ($_POST['HotelRoomForm'] as $i=>$attributes)
                {
                    $room = new HotelRoomForm();
                    $room->attributes = $attributes;
                    $validRooms = $validRooms and $room->validate();
                    if ($validRooms)
                        $model->rooms[] = $room;
                }
                $model->startCities = array();
                foreach ($_POST['EventStartCityForm'] as $i=>$attributes)
                {
                    $startCity = new EventStartCityForm();
                    $startCity->attributes = $attributes;
                    $validStartCity = $validStartCity and $startCity->validate();
                    if ($validStartCity)
                        $model->startCities[] = $startCity;
                }
                if ($validTrips and $validRooms and $model->validate())
                {
                    Yii::app()->user->setState('trip.tour.form', serialize($model));
                    Yii::app()->shoppingCart->clear();
                    if ($model->isLinkedToEvent)
                    {
                        Yii::app()->user->setState('tourForm', $model);
                        Yii::app()->user->setState('startCities', $model->startCities);
                        Yii::app()->user->setState('startCitiesIndex', 0);
                        $this->controller->redirect($this->controller->createUrl('showEventTrip'));
                    }
                    else
                    {
                        ConstructorBuilder::buildAndPutToCart($model);
                        $this->controller->redirect($this->controller->createUrl('showTrip'));
                    }
                }
            }
        }
        if ($isTab)
            $this->controller->renderPartial('create', array('model'=>$model));
        else
            $this->controller->render('create', array('model'=>$model));
    }
}
