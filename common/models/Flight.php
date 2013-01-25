<?php
/**
 * Flight class. Flight with transits.
 * Class with one element of marchroute
 * @author oleg
 *
 */
class Flight extends CComponent
{
    /** @var FlightPart[] */
    public $flightParts = array();
    public $transits = array();
    public $departureCityId;
    public $arrivalCityId;
    public $departureDate;
    public $arrivalDate;
    public $fullDuration = 0;
    private $departureCity;
    private $arrivalCity;

    public function addPart(FlightPart $oPart)
    {
        if ($oPart instanceof FlightPart)
        {
            if (!$this->flightParts)
            {
                $this->flightParts[] = $oPart;
                $this->departureCityId = $oPart->departureCityId;
                $this->arrivalCityId = $oPart->arrivalCityId;
                $this->departureDate = $oPart->datetimeBegin;
                $this->arrivalDate = $oPart->datetimeEnd;
            }
            else
            {
                $oLastPart = & $this->flightParts[count($this->flightParts) - 1];
                $aTransit = array();
                $aTransit['timeForTransit'] = $oPart->timestampBegin - $oLastPart->timestampEnd;
                $aTransit['cityId'] = $oPart->departureCityId;
                $aTransit['city'] = City::getCityByPk($oPart->departureCityId);
                $this->arrivalCityId = $oPart->arrivalCityId;
                $this->transits[] = (object)$aTransit;
                $this->flightParts[] = $oPart;
                $this->fullDuration += $aTransit['timeForTransit'];
            }
            $this->fullDuration += $oPart->duration;
        }
        else
        {
            throw new CException(Yii::t('application', 'Required param type FlightPart'));
        }
    }

    public function getDepartureCity()
    {
        if (!$this->departureCity)
        {
            $this->departureCity = City::getCityByPk($this->departureCityId);
            if (!$this->departureCity) throw new CException(Yii::t('application', 'Departure city not found. City with id {city_id} not set in db.', array(
                '{city_id}' => $this->departureCityId
            )));
        }
        return $this->departureCity;
    }

    public function getArrivalCity()
    {
        if (!$this->arrivalCity)
        {
            $this->arrivalCity = City::getCityByPk($this->arrivalCityId);
            if (!$this->arrivalCity) throw new CException(Yii::t('application', 'Arrival city not found. City with id {city_id} not set in db.', array(
                '{city_id}' => $this->arrivalCityId
            )));
        }
        return $this->arrivalCity;
    }

    public function getArrivalDate()
    {
        $last = end($this->flightParts);
        return $last->timestampEnd;
    }

    public function getDepartureAirportCode()
    {
        $flightPart = reset($this->flightParts);
        return CHtml::value($flightPart, 'departureAirport.code');
    }

    public function getArrivalAirportCode()
    {
        $flightPart = end($this->flightParts);
        return CHtml::value($flightPart, 'arrivalAirport.code');
    }

    public function getJsonObject()
    {
        $ret = array(
            'departureCity' => $this->getDepartureCity()->localRu,
            'departureCityPre' => $this->getDepartureCity()->casePre,
            'arrivalCity' => $this->getArrivalCity()->localRu,
            'arrivalCityPre' => $this->getArrivalCity()->casePre,
            'departureDate' => DateTimeHelper::formatForJs($this->departureDate),
            'arrivalDate' => DateTimeHelper::formatForJs($this->getArrivalDate()),
            'fullDuration' => $this->fullDuration,
            'flightParts' => array()
        );
        foreach ($this->flightParts as $flightPart)
        {
            $ret['flightParts'][] = $flightPart->getJsonObject();
        }
        return $ret;
    }

}