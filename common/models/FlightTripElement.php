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

    public function saveToOrderDb()
    {
        if ($this->flightVoyage)
            return $this->flightVoyage->saveToOrderDb();
        else
        {
            //we have only search params now
            $order = new OrderFlightVoyage();
            $order->departureCity = $this->getDepartureCity()->id;
            $order->arrivalCity = $this->getArrivalCity()->id;
            $order->departureDate = $this->getDepartureDate();
            if ($order->save())
                return $order;
        }
        return false;
    }

    public function getIsValid()
    {
        if ($this->flightVoyage)
            return $this->flightVoyage->getIsValid();
        else
            return true;
    }

    public function getIsPayable()
    {
        if ($this->flightVoyage)
            return $this->flightVoyage->getIsPayable();
        else
            return true;
    }

    public function saveReference($order)
    {
        if ($this->flightVoyage)
            return $this->flightVoyage->saveReference($order);
        else
            return true;
    }

    public function getTime()
    {
        if ($this->flightVoyage)
            return $this->flightVoyage->getTime();
        else
            return strtotime($this->departureDate);
    }

    public function getJsonObject()
    {
        return json_encode($this->attributes);
    }

    public function getPassports()
    {
        // TODO: Implement getPassports() method.
        $fake = new PassengerPassportForm();
        $adult1 = new FlightAdultPassportForm();
        $adult1->genderId = FlightAdultPassportForm::GENDER_MALE;
        $adult1->firstName = 'Иванов';
        $adult1->lastName = 'Иван';

        $adult2 = new FlightAdultPassportForm();
        $adult2->genderId = FlightAdultPassportForm::GENDER_MALE;
        $adult2->firstName = 'Семёнов';
        $adult2->lastName = 'Семён';

        $fake->adultsPassports = array($adult1, $adult2);
        return $fake;
    }
}
