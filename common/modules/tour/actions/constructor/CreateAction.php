<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:00
 */
class CreateAction extends CAction
{
    public function run()
    {
        if ($res = Yii::app()->user->getState('trip.tour.form'))
            $model = @unserialize($res);
        else
            $model = new TourBuilderForm();
        if (isset($_POST['TourBuilderForm']))
        {
            $model->attributes = $_POST['TourBuilderForm'];
            $model->trips = array();
            if (isset($_POST['TripForm']))
            {
                $validTrips = true;
                foreach ($_POST['TripForm'] as $i=>$attributes)
                {
                    $trip = new TripForm();
                    $trip->attributes = $attributes;
                    $validTrips = $validTrips and $trip->validate();
                    if ($validTrips)
                        $model->trips[] = $trip;
                }
                if ($validTrips and $model->validate())
                {
                    Yii::app()->user->setState('trip.tour.form', serialize($model));
                    Yii::app()->shoppingCart->clear();
                    ConstructorBuilder::build($model);
                    $this->controller->redirect('showTrip');
                }
            }
        }
        $this->controller->render('create', array('model'=>$model));
    }
}
