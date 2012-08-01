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
        if ($this->flightVoyage)
            return $this->flightVoyage->getJsonObject();
        return $this->attributes;
    }

    public function getPassports()
    {
        // TODO: Implement getPassports() method.
        $fake = new PassengerPassportForm();
        $fake->adultsPassports = array();
        $count = 1;
        if(isset($this->flightVoyage->adultPassengerInfo->count))
        {
            $count = $this->flightVoyage->adultPassengerInfo->count;
        }
        $lastNames = array('Ivanov','Petrov','Kovalev','Mihailov','Romanov','Matveev','Borisov','Sviridov','Fedorov','Nikitin','Grigoriev','Vasilev');
        $firstNames = array('Evgeniy','Kirill','Oleg','Mihail','Dmitriy','Roman','Denis','Artem','Danila','Viktor','Nikolay','Aleksey','Ruslan');

        for($i = 0;$i<$count;$i++)
        {
            $adult1 = new FlightAdultPassportForm();
            $adult1->genderId = FlightAdultPassportForm::GENDER_MALE;
            $randKey = rand(0,(count($firstNames) - 1));
            $randomFirstName = $firstNames[$randKey];
            $randKey = rand(0,(count($lastNames) - 1));
            $randomLastName = $lastNames[$randKey];
            $randomBirthDay = new DateTime('1971-01-01');
            $randKey = rand(0,365*20);
            $interval = new DateInterval('P'.$randKey.'D');
            $randomBirthDay->add($interval);
            $adult1->firstName = $randomFirstName;
            $adult1->lastName = $randomLastName;
            $adult1->birthday = $randomBirthDay->format('d.m.Y');
            $randKey = rand(1000,7000);
            $adult1->series = (string)$randKey;
            $randKey = rand(100000,999999);
            $adult1->number = (string)$randKey;
            $adult1->countryId = 174;
            $fake->adultsPassports[] = $adult1;
        }
        return $fake;
    }

    public function getId()
    {
        if ($this->flightVoyage)
            return $this->flightVoyage->getId();
        return $this->_id;
    }

    public function isLinked()
    {
        return $this->flightVoyage !== null;
    }
}
