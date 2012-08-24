<?php
/**
 * FlightPart class
 * Class with one flight. Without transits
 * @author oleg
 *
 */
class FlightPart
{
    public $departureCityId;
    public $arrivalCityId;
    public $opAirlineCode;
    public $markAirlineCode;
    public $transportAirlineCode;
    public $departureAirportId;
    public $departureAirport;
    public $arrivalAirportId;
    public $arrivalAirport;
    public $aircraftName;
    public $aircraftCode;

    public $distance;
    public $duration;
    public $timestampBegin;
    public $timestampEnd;
    public $datetimeBegin;
    public $datetimeEnd;
    public $departureTerminalCode;
    public $arrivalTerminalCode;
    public $weekDays;
    public $code;
    public $tariffs = array();

    public function __construct($oParams)
    {
        $this->departureCityId = $oParams->departure_city->id;
        $this->arrivalCityId = $oParams->arrival_city->id;
        $this->timestampBegin = strtotime($oParams->datetime_begin);
        $this->timestampEnd = strtotime($oParams->datetime_end);
        $this->datetimeBegin = $oParams->datetime_begin;
        $this->datetimeEnd = $oParams->datetime_end;
        $this->code = $oParams->code;
        $this->duration = $oParams->duration;
        $this->departureTerminalCode = $oParams->departure_terminal_code;
        $this->arrivalTerminalCode = $oParams->arrival_terminal_code;
        $this->aircraftCode = $oParams->aircraft_code;
        //$this->aircraft_name = $oParams->aircraft_name;
        $this->transportAirlineCode = $oParams->transport_airline->code;
        $this->opAirline = $oParams->opAirline;
        $this->markAirline = $oParams->markAirline;
        $this->departureAirportId = $oParams->departure_airport->id;
        $this->arrivalAirportId = $oParams->arrival_airport->id;
        $this->departureAirport = $oParams->departure_airport;
        $this->arrivalAirport = $oParams->arrival_airport;

    }

    public function getJsonObject()
    {
        $ret = array(
            'transportAirline' => $this->transportAirlineCode,
            'departureCity' => City::getCityByPk($this->departureCityId)->localRu,
            'departureCityPre' => City::getCityByPk($this->departureCityId)->casePre,
            'arrivalCity' => City::getCityByPk($this->arrivalCityId)->localRu,
            'arrivalCityPre' => City::getCityByPk($this->arrivalCityId)->casePre,
            'datetimeBegin' => DateTimeHelper::formatForJs($this->timestampBegin),
            'datetimeEnd' => DateTimeHelper::formatForJs($this->timestampEnd),
            'flightCode' => $this->code,
            'duration' => $this->duration,
            'departureAirport' => $this->departureAirport->localRu,
            'arrivalAirport' => $this->arrivalAirport->localRu,
            'aircraftCode'=>$this->aircraftCode,
        );
        return $ret;

    }

}