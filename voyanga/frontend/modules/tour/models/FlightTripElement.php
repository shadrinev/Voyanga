<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 25.07.12
 * Time: 11:50
 */
class FlightTripElement extends TripElement
{
    public $type = self::TYPE_FLIGHT;

    private $_id;

    public $departureDate;
    public $departureCity;
    public $arrivalCity;

    /** @var FlightVoyage */
    public $flightVoyage;

    public function attributeNames()
    {
        return array(
            'departureDate',
            'departureCity',
            'arrivalCity',
        );
    }

    public function getPrice()
    {
        if ($this->flightVoyage)
        {
            return $this->flightVoyage->rubPrice;
        }
        return 0;
    }
}
