<?php
/**
 * Flight class
 * Class with one element of marchroute
 * @author oleg
 *
 */
class Flight extends CComponent
{
    public $flightParts = array();
    public $transits = array();
    public $departureCityId;
    public $arrivalCityId;
    public $departure_date;
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
                $this->departure_date = $oPart->datetimeBegin;
            } else
            {
                $oLastPart = &$this->flightParts[count($this->flightParts) - 1];
                $aTransit = array();
                $aTransit['time_for_transit'] = $oPart->timestampBegin - $oLastPart->timestampEnd;
                $aTransit['city_id'] = $oPart->departureCityId;
                $this->arrivalCityId = $oPart->arrivalCityId;
                $this->transits[] = $aTransit;
                $this->flightParts[] = $oPart;
                $this->fullDuration += $aTransit['time_for_transit'];
            }
            $this->fullDuration += $oPart->duration;
        } else
        {
            throw new CException(Yii::t('application', 'Required param type FlightPart'));
        }
    }

    public function getDepartureCity()
    {
        if (!$this->departureCity)
        {
            $this->departureCity = City::model()->findByPk($this->departureCityId);
            if (!$this->departureCity) throw new CException(Yii::t('application', 'Departure city not found. City with id {city_id} not set in db.', array(
                '{city_id}' => $this->departureCityId)));
        }
    }

    public function getArrivalCity()
    {
        if (!$this->arrivalCity)
        {
            $this->arrivalCity = City::model()->findByPk($this->arrivalCityId);
            if (!$this->arrivalCity) throw new CException(Yii::t('application', 'Arrival city not found. City with id {city_id} not set in db.', array(
                '{city_id}' => $this->arrivalCityId)));
        }
    }

}