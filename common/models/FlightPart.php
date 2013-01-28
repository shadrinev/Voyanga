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
    public $aircraftCode;
    public $stopNum;
    public $bookingCodes;

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

    const SERVICE_CLASS_BUSINESS = 'B';
    const SERVICE_CLASS_ECONOM = 'E';

    static public function getBusinessCodes()
    {
        return array("P", "F", "A", "C", "J", "D", "Z", "I");
    }

    public function __construct($oParams)
    {
        if(is_object($oParams)){
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
            $this->stopNum = $oParams->stopNum;
            $this->bookingCodes = $oParams->aBookingCodes;
        }else{
            $this->departureCityId = $oParams['departureCityId'];
            $this->arrivalCityId = $oParams['arrivalCityId'];
            $this->timestampBegin = strtotime($oParams['datetimeBegin']);
            $this->timestampEnd = strtotime($oParams['datetimeEnd']);
            $this->datetimeBegin = $oParams['datetimeBegin'];
            $this->datetimeEnd = $oParams['datetimeEnd'];
            $this->code = $oParams['flightCode'];
            $this->duration = $oParams['duration'];
            //$this->departureTerminalCode = $oParams['departureCityId'];
            //$this->arrivalTerminalCode = $oParams->arrival_terminal_code;
            $this->aircraftCode = $oParams['aircraftCode'];
            //$this->aircraft_name = $oParams->aircraft_name;
            $this->transportAirlineCode = $oParams['transportAirline'];
            //$this->opAirline = $oParams['duration'];
            //$this->markAirline = $oParams['duration'];
            $this->departureAirportId = $oParams['departureAirportId'];
            $this->arrivalAirportId = $oParams['arrivalAirportId'];
            $this->departureAirport = Airport::getAirportByPk($oParams['departureAirportId']);
            $this->arrivalAirport = Airport::getAirportByPk($oParams['arrivalAirportId']);
            $this->stopNum = $oParams['stopNum'];
            $this->bookingCodes = array($oParams['bookingCode']);
        }
    }

    public function getJsonObject()
    {
        $airline = Airline::getAirlineByCode($this->transportAirlineCode);
        $ret = array(
            'transportAirline' => $this->transportAirlineCode,
            'transportAirlineName' => $airline->localRu,
            'transportAirlineNameEn' => $airline->localEn,
            'markAirline' => $this->markAirline->code,
            'markAirlineName' => $this->markAirline->localRu,
            'markAirlineNameEn' => $this->markAirline->localEn,
            'departureCity' => City::getCityByPk($this->departureCityId)->localRu,
            'departureCityPre' => City::getCityByPk($this->departureCityId)->casePre,
            'departureCityId' => $this->departureCityId,
            'arrivalCity' => City::getCityByPk($this->arrivalCityId)->localRu,
            'arrivalCityPre' => City::getCityByPk($this->arrivalCityId)->casePre,
            'arrivalCityId' => $this->arrivalCityId,
            'datetimeBegin' => DateTimeHelper::formatForJs($this->timestampBegin),
            'datetimeEnd' => DateTimeHelper::formatForJs($this->timestampEnd),
            'flightCode' => $this->code,
            'duration' => $this->duration,
            'departureAirport' => $this->departureAirport->localRu,
            'arrivalAirport' => $this->arrivalAirport->localRu,
            'departureAirportId' => $this->departureAirport->id,
            'arrivalAirportId' => $this->arrivalAirport->id,
            'aircraftCode' => $this->aircraftCode,
            'aircraftName' => $this->getAircraftName(),
            'stopNum'=>$this->stopNum,
            'bookingCode'=>$this->bookingCodes[0],
        );
        return $ret;
    }

    public function getAircraftName()
    {
        $aircraft = Aircraft::model()->findByAttributes(array('nIataCode'=>strtolower($this->aircraftCode)));
        if ($aircraft)
            return $aircraft->fullTitle;
        return '';
    }
}