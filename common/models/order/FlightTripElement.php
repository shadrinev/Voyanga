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
    private $groupId;
    private $passengerPassports;

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
            return $this->flightVoyage->price;
        }
        return 0;
    }

    public function getGroupId()
    {
        if($this->flightVoyage)
            return $this->flightVoyage->getId();
        if (!$this->groupId)
            $this->groupId = uniqid();
        return $this->groupId;
    }

    public function setGroupId($val)
    {
        $this->groupId = $val;
    }

    public function saveToOrderDb()
    {
        if ($this->flightVoyage)
            return $this->flightVoyage->saveToOrderDb($this->groupId);
        else
        {
            //we have only search params now
            $order = new OrderFlightVoyage();
            $order->groupId = $this->groupId;
            $order->departureCity = $this->departureCity;
            $order->arrivalCity = $this->arrivalCity;
            $order->departureDate = $this->departureDate;
            if ($order->save())
                return $order;
        }
        return false;
    }

    public function getDepartureCityModel()
    {
        return City::model()->findByPk($this->departureCity);
    }

    public function getArrivalCityModel()
    {
        return City::model()->findByPk($this->arrivalCity);
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
            return $this->flightVoyage->getTime($this->departureCity);
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
        return $this->passengerPassports;
    }

    public function setPassports($adults, $children=array(), $infants=array())
    {
        $this->passengerPassports = new PassengerPassportForm();
        $this->passengerPassports->adultsPassports = $adults;
        $this->passengerPassports->childrenPassports = $children;
        $this->passengerPassports->infantPassports = $infants;
    }

    public function getId()
    {
        if ($this->flightVoyage)
            return $this->flightVoyage->getId();
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

    public function prepareForFrontend()
    {
        return FlightTripElementFrontendProcessor::prepareInfoForTab($this);
    }

    public function addGroupedInfo($preparedFlight)
    {
        return FlightTripElementFrontendProcessor::addGroupedInfoToTab($preparedFlight, $this);
    }

    public function buildTabLabel($current, $previous)
    {
        return FlightTripElementFrontendProcessor::buildTabLabel($current, $previous);
    }

    public function createTripElementWorkflow()
    {
        return new FlightTripElementWorkflow($this);
    }

    static public function getUrlToAllVariants($flightTripElements)
    {
        $search = array(
            'adt' => $flightTripElements[0]->adultCount,
            'chd' => $flightTripElements[0]->childCount,
            'inf' => $flightTripElements[0]->infantCount,
        );
        foreach ($flightTripElements as $flightTripElement)
        {
            $destination = array(
                'departure' => $flightTripElement->getDepartureCityModel()->code,
                'arrival' => $flightTripElement->getArrivalCityModel()->code,
                'date' => $flightTripElement->departureDate,
            );
            $search['destinations'][] = $destination;
        }
        $fullUrl = $flightTripElements[0]->buildApiUrl($search);
        return $fullUrl;
    }

    private function buildApiUrl($params)
    {
        $url = Yii::app()->params['app.api.flightSearchUrl'];
        $fullUrl = $url . '?' . http_build_query($params);
        return $fullUrl;
    }
    
    public function fillFromSearchParams(FlightSearchParams $searchParams, $isBack = false)
    {
        $this->searchParams = $searchParams;
        $ind = $isBack ? 1 : 0;
        $route = $searchParams->routes[$ind];
        $departureDate = date('Y-m-d', strtotime($route->departureDate));
        $departureCity = $route->departureCityId;
        $arrivalCity = $route->arrivalCityId;
        $this->departureDate = $departureDate;
        $this->departureCity = $departureCity;
        $this->arrivalCity = $arrivalCity;
        $this->adultCount = $searchParams->adultCount;
        $this->childCount = $searchParams->childCount;
        $this->infantCount = $searchParams->infantCount;
    }
}
