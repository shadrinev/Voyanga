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
}
