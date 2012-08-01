<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:09
 */
class NewAction extends CAction
{
    public function run($isTab=false, $clear=false)
    {
        if ($clear)
            Yii::app()->shoppingCart->clear();
        $flightForm = new FlightForm;
        $hotelForm = new HotelForm;
        if ($isTab)
            $this->controller->renderPartial('new', array('flightForm'=>$flightForm, 'hotelForm'=>$hotelForm,'autosearch'=>false, 'cityName'=>'', 'duration'=>1));
        else
            $this->controller->render('new', array('flightForm'=>$flightForm, 'hotelForm'=>$hotelForm,'autosearch'=>false, 'cityName'=>'', 'duration'=>1));
    }
}
