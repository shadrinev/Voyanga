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

    //temp var for no warning
    private $startCityId;

    /** @var HotelRoomForm[] */
    public $rooms;

    //event related fields
    public $eventId;
    public $startCities=array();
    public $newEventName;

    public function rules()
    {
        return array(
            array(
                'startDate, endDate, eventId, startCityId', 'safe'
            )
        );
    }

    public $startCityModel;

    public function init()
    {
        $this->setStartCityName('Санкт-Петербург');
        $this->setEventStartCities();
        $this->setRooms();
    }

    public function setRooms()
    {
        $element = new HotelRoomForm();
        $element->adultCount = 2;
        $element->childCount = 0;
        $element->cots = 0;
        $element->childAge = 0;
        $this->rooms[] = $element;
    }

    public function getAdultCount()
    {
        $adultCount = 0;
        foreach ($this->rooms as $room)
            $adultCount += $room->adultCount;
        return $adultCount;
    }

    public function getChildCount()
    {
        $childCount = 0;
        foreach ($this->rooms as $room)
            $childCount += $room->childCount;
        return $childCount;
    }

    public function getInfantCount()
    {
        $infantCount = 0;
        foreach ($this->rooms as $room)
            $infantCount += $room->cots;
        return $infantCount;
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
            'startCityId' => 'Начало поездки в городе',
            'eventId' => 'Связать с событием',
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
            $element = new EventStartCityForm();
            $element->id = $startCity->cityId;
            $element->name = $startCity->city->localRu;
            $this->startCities[] = $element;
        }
    }

    public function getIsLinkedToEvent()
    {
        return ($this->eventId != Event::NO_EVENT_ITEM);
    }
}
