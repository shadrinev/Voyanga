<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 25.07.12
 * Time: 13:20
 * To change this template use File | Settings | File Templates.
 */
class TimelineCalendarWidget extends CWidget
{
    public $tourModel;

    private $assetsUrl;
    private $timelineEvents;

    public function init()
    {
        if($this->assetsUrl===null)
            $this->assetsUrl = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets',false,-1,YII_DEBUG);
        Yii::app()->getClientScript()->registerScriptFile($this->assetsUrl.'/'.'jquery-ui-1.8.22.custom.min.js');
        Yii::app()->getClientScript()->registerScriptFile($this->assetsUrl.'/'.'jquery.easing.1.3.js');
        Yii::app()->getClientScript()->registerScriptFile($this->assetsUrl.'/'.'timelineCalendar.js');
        Yii::app()->getClientScript()->registerCssFile($this->assetsUrl.'/'.'timelineCalendar.css');

        $this->timelineEvents = ($this->tourModel == null) ? $this->generateTimelineEventsBasic() : $this->generateTimelineEvents();
    }

    public function run()
    {
        $this->render('template', array('timelineEvents'=>$this->timelineEvents));
    }

    private function generateTimelineEventsBasic()
    {
        // $timelineEvents = "[{dayStart: Date.fromIso('2012-09-21'),dayEnd: Date.fromIso('2012-09-22'),type:'flight',color:'red',description:'Led || Mow'},{dayStart: Date.fromIso('2012-09-21'),dayEnd: Date.fromIso('2012-10-23'),type:'hotel',color:'red',description:'Californication Hotel'},{dayStart: Date.fromIso('2012-10-23'),dayEnd: Date.fromIso('2012-10-23'),type:'flight',color:'red',description:'Mow || Led'}];";
        $tour = array(
            array(
                'dayStart' => '2012-09-21',
                'dayEnd' => '2012-09-22',
                'type' => 'flight',
                'color' => 'red',
                'description' => 'LED || MOW'
            ),
            array(
                'dayStart' => '2012-09-22',
                'dayEnd' => '2012-10-23',
                'type' => 'hotel',
                'color' => 'yellow',
                'description' => 'Hotel'
            ),
        );
        $jsTour = CJavaScript::jsonEncode($tour);
        return $jsTour;
    }

    private function generateTimelineEvents()
    {
        $tour = array();
        $tourFlights = $this->tourModel->flightItems;
        foreach ($tourFlights as $flight)
        {
            $element = array(
                'dayStart' => $flight->departureDate,
                'dayEnd' => $flight->departureDate,
                'type' => 'flight',
                'color' => 'red',
                'description' => $this->generateFlightDescription($flight)
            );
            $tour[] = $element;
        }
        $tourHotels = $this->tourModel->hotelItems;
        foreach ($tourHotels as $hotel)
        {
            $element = array(
                'dayStart' => $hotel->checkIn,
                'dayEnd' => $hotel->checkOut,
                'type' => 'hotel',
                'color' => 'yellow',
                'description' => $this->generateHotelDescription($hotel)
            );
            $tour[] = $element;
        }
        $jsTour = CJavaScript::jsonEncode($tour);
        return $jsTour;
    }

    private function generateFlightDescription(OrderFlightVoyage $flight)
    {
        $departureCityModel = City::model()->findByPk($flight->departureCity);
        $arrivalCityModel = City::model()->findByPk($flight->arrivalCity);
        $description = $departureCityModel->code . ' || ' . $arrivalCityModel->code;
        return $description;
    }

    private function generateHotelDescription(OrderHotel $hotel)
    {
        $city = City::model()->findByPk($hotel->cityId);
        $hotel = @unserialize($hotel->object);
        if (is_object($hotel))
            $description = 'Отель '.$hotel->hotelName . ' в ' . $city->casePre;
        else
            $description = 'Отель в ' . $city->casePre;
        return $description;
    }
}
