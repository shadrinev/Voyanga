<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 25.07.12
 * Time: 11:50
 */
class HotelTripElement extends TripElement
{
    private $_id;

    public $city;
    public $checkIn;
    public $checkOut;
    public $rooms;
    public $hotelBookerId;
    private $passports;
    private $groupId;

    public function rules()
    {
        return array(
            array('city, checkIn, checkOut, hotelBookerId', 'safe'),
        );
    }

    /** @var Hotel */
    public $hotel;

    public function attributeNames()
    {
        return array(
            'city',
            'checkIn',
            'checkOut',
        );
    }

    public function saveToOrderDb()
    {
        if ($this->hotel)
            return $this->hotel->saveToOrderDb();
        else
        {
            //we have only search params now
            $order = new OrderHotel();
            $order->cityId = $this->city;
            $order->checkIn = $this->checkIn;
            $order->duration = $this->getDuration();
            if ($order->save())
                return $order;
        }
        return false;
    }

    public function getCityModel()
    {
        return City::model()->findByPk($this->city);
    }

    public function getPrice()
    {
        if ($this->hotel)
        {
            return $this->hotel->rubPrice;
        }
        return 0;
    }

    public function getIsValid()
    {
        if ($this->hotel)
            return $this->hotel->getIsValid();
        else
            return true;
    }

    public function getIsPayable()
    {
        if ($this->hotel)
            return $this->hotel->getIsPayable();
        else
            return true;
    }

    public function saveReference($order)
    {
        if ($this->hotel)
            return $this->hotel->saveReference($order);
        else
            return true;
    }

    public function getTime()
    {
        if ($this->hotel)
            return $this->hotel->getTime();
        else
            return strtotime($this->checkIn);
    }

    public function getDuration()
    {
        $start = strtotime($this->checkIn);
        $end = strtotime($this->checkOut);
        $duration = ($end - $start) / (3600 * 24);
        return $duration;
    }

    public function getJsonObject()
    {
        if ($this->hotel)
            return $this->hotel->getJsonObject();
        return $this->attributes;
    }

    public function getPassports()
    {
        // TODO: Implement getPassports() method.
        if ($this->passports)
            return $this->passports;
        $fake = new HotelPassportForm();
        $fake->addRoom($this->adultCount,0);
        $roomPassport = $fake->roomsPassports[0] = new RoomPassportForm();
        for($i=0; $i<$this->adultCount; $i++)
        {
            $roomPassport->adultsPassports[$i] = new HotelAdultPassportForm();
            $adultForm = FlightAdultPassportForm::fillWithRandomData();
            $roomPassport->adultsPassports[$i]->lastName = $adultForm->lastName;
            $roomPassport->adultsPassports[$i]->firstName = $adultForm->firstName;
            $roomPassport->adultsPassports[$i]->genderId = $adultForm->genderId;
        }
        $this->passports = $fake;
        return $fake;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($value)
    {
        $this->_id = $value;
    }

    public function isLinked()
    {
        return $this->hotel !== null;
    }

    public function getWeight()
    {
        return 2;
    }

    public function getType()
    {
        return 'Hotel';
    }

    public function prepareForFrontend()
    {
        return HotelTripElementFrontendProcessor::prepareInfoForTab($this);
    }

    public function createTripElementWorkflow()
    {
        return new HotelTripElementWorkflow($this);
    }

    public function getUrlToAllVariants()
    {
        $search = array(
            'city' => $this->getCityModel()->code,
            'checkIn' => $this->checkIn,
            'duration' => $this->getDuration(),
        );
        foreach ($this->rooms as $room)
        {
            $newRoom = array(
                'adt' => $room->adultCount,
                'chd' => $room->childCount,
                'chdAge' => $room->childAge,
                'cots' => $room->cots
            );
            $search['rooms'][] = $newRoom;
        }
        $fullUrl = $this->buildApiUrl($search);
        return $fullUrl;
    }

    private function buildApiUrl($params)
    {
        $url = Yii::app()->params['app.api.hotelSearchUrl'];
        $fullUrl = $url . '?' . http_build_query($params);
        return $fullUrl;
    }

    public function getGroupId()
    {
        if($this->hotel)
            return $this->hotel->getId();
        if (!$this->groupId)
            $this->groupId = uniqid();
        return $this->groupId;
    }

    public function fillFromSearchParams(HotelSearchParams $searchParams)
    {
        $this->city = $searchParams->city;
        $this->checkIn = $searchParams->checkIn;
        $this->checkOut = $searchParams->getCheckout();
        $this->rooms = array();
        foreach ($searchParams->rooms as $room)
        {
            $newRoom = array(
                'adt' => $room->adultCount,
                'chd' => $room->childCount,
                'chdAge' => $room->childAge,
                'cots' => $room->cots
            );
            $this->rooms[] = $newRoom;
        }
    }

}