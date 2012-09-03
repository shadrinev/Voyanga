<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 24.07.12
 * Time: 12:25
 */
class TourBuilderForm extends CFormModel
{
    /** @var TripForm[] */
    public $trips = array();

    public $startDate;
    public $endDate;

    public $adultCount = 2;
    public $childCount = 0;
    public $infantCount = 0;

    //temp var for no warning
    public $startCityId;

    //event related fields
    public $eventId;
    public $eventStartCityIds;
    public $eventStartCityNames;
    public $newEventName;

    public function rules()
    {
        return array(
            array(
                'startDate, endDate, adultCount, childCount, infantCount, startCityId', 'safe'
            )
        );
    }

    public $startCityModel;

    public function init()
    {
        $this->setStartCityName('Санкт-Петербург');
        $this->setEventStartCities();
    }

    public function getStartCityId()
    {
        if ($this->startCityModel)
            return $this->startCityModel->id;
        return null;
    }

    public function setStartCityId($value)
    {
        $this->startCityModel = City::model()->getCityByPk($value);
    }

    public function getStartCityName()
    {
        if ($this->startCityModel)
            return $this->startCityModel->localRu;
        return null;
    }

    public function setStartCityName($value)
    {
        $items = City::model()->guess($value);
        $this->startCityModel = $items[0];
    }

    public function attributeLabels()
    {
        return array(
            'adultCount' => 'Количество взрослых',
            'startCityId' => 'Начало поездки в городе',
            'eventId' => 'Связать с событием',
            'eventStartCityIds' => 'На событие стартуем из города',
            'newEventName' => 'Название нового события',
        );
    }

    public function fillCommonData($params)
    {
        $this->attributes = $params;
    }

    public function fillTripData($params)
    {
        foreach ($params as $i=>$attributes)
        {
            $trip = new TripForm();
            $trip->attributes = $attributes;
            if ($trip->validate())
                $this->trips[] = $trip;
            else
                $this->addError('Trip['.$i.']', 'Incorrect trip element');
        }
    }

    private function setEventStartCities()
    {
        $startCities = EventStartCity::model()->with('city')->findAll();
        foreach ($startCities as $startCity)
        {
            $this->eventStartCityIds[] = $startCity->cityId;
            $this->eventStartCityNames[] = $startCity->city->localRu;
        }
    }
}
