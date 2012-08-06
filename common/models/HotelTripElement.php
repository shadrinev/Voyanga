<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 25.07.12
 * Time: 11:50
 */
class HotelTripElement extends TripElement
{
    public $type = self::TYPE_FLIGHT;

    private $_id;

    public $city;
    public $checkIn;
    public $checkOut;
    public $adultCount;
    public $childCount;
    public $infantCount;

    /** @var Hotel */
    public $hotel;

    public function attributeNames()
    {
        return array(
            'city',
            'checkIn',
            'checkOut',
            'adultCount',
            'childCount',
            'infantCount',
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
            return strtotime($this->checkIn) + (23*3600 + 59 * 60 + 59);
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
        return json_encode($this->attributes);
    }

    public function getPassports()
    {
        // TODO: Implement getPassports() method.
        $fake = new HotelPassportForm();
        $fake->addRoom(2,0);
        $roomPassport = $fake->roomsPassports[0] = new RoomPassportForm();
        $adult1 = $roomPassport->adultsPassports[0] = new HotelAdultPassportForm();
        $adult2 = $roomPassport->adultsPassports[1] = new HotelAdultPassportForm();

        $adult1->genderId = HotelAdultPassportForm::GENDER_MALE;
        $adult1->firstName = 'Иванов';
        $adult1->lastName = 'Иван';

        $adult2->genderId = HotelAdultPassportForm::GENDER_MALE;
        $adult2->firstName = 'Семёнов';
        $adult2->lastName = 'Семён';

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
}