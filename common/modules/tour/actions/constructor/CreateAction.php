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
        if ($res = Yii::app()->user->getState('trip.tour.form'))
            $model = @unserialize($res);
        else
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
                if ($validTrips and $validRooms and $model->validate())
                {
                    Yii::app()->user->setState('trip.tour.form', serialize($model));
                    Yii::app()->shoppingCart->clear();
                    ConstructorBuilder::buildAndPutToCart($model);
                    if ($model->isLinkedToEvent)
                        $this->controller->redirect($this->controller->createUrl('showEventTrip'));
                    else
                        $this->controller->redirect($this->controller->createUrl('showTrip'));
                }
            }
        }
        if ($isTab)
            $this->controller->renderPartial('create', array('model'=>$model));
        else
            $this->controller->render('create', array('model'=>$model));
    }
}
