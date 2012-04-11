<?php
class FlightPart
{
	public $departure_city_id;
	public $arrival_city_id;
	public $airline_id;
	public $transport_airline_id;
	public $departure_airport_id;
	public $arrival_airport_id;
	public $aircraft_name;
	public $aircraft_code;
	
	public $distance;
	public $duration;
	public $timestampBegin;
	public $timestampEnd;
	public $datetimeBegin;
	public $datetimeEnd;
	public $departure_terminal_code;
	public $arrival_terminal_code;
	public $weekDays;
	public $code;
	public $aTriffs = array();
	
	public function __construct($oParams){
		$this->departure_city_id = $oParams->departure_city->id;
		$this->arrival_city_id = $oParams->arrival_city->id;
		$this->timestampBegin = strtotime($oParams->datetime_begin);
		$this->timestampEnd = strtotime($oParams->datetime_end);
		$this->datetimeBegin = $oParams->datetime_begin;
		$this->datetimeEnd = $oParams->datetime_end;
		$this->code = $oParams->code;
		$this->duration = $oParams->duration;
		$this->departure_terminal_code = $oParams->departure_terminal_code;
		$this->arrival_terminal_code = $oParams->arrival_terminal_code;
		$this->aircraft_code = $oParams->aircraft_code;
		$this->aircraft_name = $oParams->aircraft_name;
		$this->transport_airline_id = $oParams->transport_airline->code;
		$this->airline_id = $oParams->airline->code;
		$this->departure_airport_id = $oParams->departure_airport->id;
		$this->arrival_airport_id = $oParams->arrival_airport->id;
		
	}
	
}