<?php
/**
 * FlightPart class
 * Class with one flight. Without transits
 * @author oleg
 *
 */
class FlightPart {
    public $departureCityId;
    public $iArrivalCityId;
    public $sAirlineCode;
    public $sTransportAirlineCode;
    public $sDepartureAirportId;
    public $arrival_airport_id;
    public $aircraft_name;
    public $sAircraftCode;
    
    public $distance;
    public $duration;
    public $timestampBegin;
    public $timestampEnd;
    public $datetimeBegin;
    public $datetimeEnd;
    public $departure_terminal_code;
    public $arrival_terminal_code;
    public $weekDays;
    public $sCode;
    public $aTriffs = array();
    
    public function __construct( $oParams ) {
        $this->iDepartureCityId = $oParams->departure_city->id;
        $this->iArrivalCityId = $oParams->arrival_city->id;
        $this->timestampBegin = strtotime( $oParams->datetime_begin );
        $this->timestampEnd = strtotime( $oParams->datetime_end );
        $this->datetimeBegin = $oParams->datetime_begin;
        $this->datetimeEnd = $oParams->datetime_end;
        $this->sCode = $oParams->code;
        $this->duration = $oParams->duration;
        $this->departure_terminal_code = $oParams->departure_terminal_code;
        $this->arrival_terminal_code = $oParams->arrival_terminal_code;
        $this->sAircraftCode = $oParams->aircraft_code;
        //$this->aircraft_name = $oParams->aircraft_name;
        $this->sTransportAirlineCode = $oParams->transport_airline->code;
        $this->sAirlineCode = $oParams->airline->code;
        $this->sDepartureAirportId = $oParams->departure_airport->id;
        $this->arrival_airport_id = $oParams->arrival_airport->id;
    
    }

}