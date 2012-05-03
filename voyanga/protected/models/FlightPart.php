<?php
/**
 * FlightPart class
 * Class with one flight. Without transits
 * @author oleg
 *
 */
class FlightPart {
    public $departureCityId;
    public $arrivalCityId;
    public $airlineCode;
    public $transportAirlineCode;
    public $departureAirportId;
    public $arrivalAirportId;
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
    
    public function __construct( $oParams ) {
        $this->departureCityId = $oParams->departure_city->id;
        $this->arrivalCityId = $oParams->arrival_city->id;
        $this->timestampBegin = strtotime( $oParams->datetime_begin );
        $this->timestampEnd = strtotime( $oParams->datetime_end );
        $this->datetimeBegin = $oParams->datetime_begin;
        $this->datetimeEnd = $oParams->datetime_end;
        $this->code = $oParams->code;
        $this->duration = $oParams->duration;
        $this->departureTerminalCode = $oParams->departure_terminal_code;
        $this->arrivalTerminalCode = $oParams->arrival_terminal_code;
        $this->aircraftCode = $oParams->aircraft_code;
        //$this->aircraft_name = $oParams->aircraft_name;
        $this->transportAirlineCode = $oParams->transport_airline->code;
        $this->airlineCode = $oParams->airline->code;
        $this->departureAirportId = $oParams->departure_airport->id;
        $this->arrivalAirportId = $oParams->arrival_airport->id;
    
    }

}