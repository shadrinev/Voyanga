<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 25.07.12
 * Time: 11:50
 */
class FlightTripElement extends TripElement
{
    /** @var FlightVoyage */
    public $flightVoyage;

    public $departureDate;
    public $departureCity;
    public $arrivalCity;
    public $adultCount;
    public $childCount;
    public $infantCount;
    public $flightBookerId;

    private $_id;

    public function rules()
    {
        return array(
            array('departureDate, departureCity, arrivalCity, groupId, adultCount, childCount, infantCount, flightBookerId', 'safe'),
        );
    }


    public function attributeNames()
    {
        return array(
            'departureDate',
            'departureCity',
            'arrivalCity',
            'adultCount',
            'childCount',
            'infantCount',
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

    public function getGroupId()
    {
        if($this->flightVoyage)
            return $this->flightVoyage->getId();
        return $this->groupId;
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
        for($i = 0;$i<$count;$i++)
        {
            $fake->adultsPassports[] = FlightAdultPassportForm::fillWithRandomData();
        }
        return $fake;
    }

    public function getId()
    {
        //if ($this->flightVoyage)
        //    return $this->flightVoyage->getId();
        return $this->_id;
    }

    public function setId($value)
    {
        $this->_id = $value;
    }

    public function isLinked()
    {
        return $this->flightVoyage !== null;
    }

    public function getWeight()
    {
        return 1;
    }

    public function getType()
    {
        return 'Flight';
    }
}
