<?php
/**
 * Flight class
 * Class with one element of marchroute
 * @author oleg
 *
 */
class Flight {
    public $flightParts = array();
    public $transits = array();
    public $departureCityId;
    public $arrival_city_id;
    public $departure_date;
    public $fullDuration = 0;
    private $departure_city;
    private $arrival_city;
    
    public function addPart( FlightPart $oPart ) {
        if($oPart instanceof FlightPart){
            if ( !$this->flightParts ) {
                $this->flightParts[] = $oPart;
                $this->departureCityId = $oPart->departureCityId;
                $this->arrival_city_id = $oPart->arrival_city_id;
                $this->departure_date = $oPart->datetimeBegin;
            } else {
                $oLastPart = &$this->flightParts[count( $this->flightParts ) - 1];
                $aTransit = array();
                $aTransit['time_for_transit'] = $oPart->timestampBegin - $oLastPart->timestampEnd;
                $aTransit['city_id'] = $oPart->departureCityId;
                $this->arrival_city_id = $oPart->arrival_city_id;
                $this->transits[] = $aTransit;
                $this->flightParts[] = $oPart;
                $this->fullDuration += $aTransit['time_for_transit'];
            }
            $this->fullDuration += $oPart->duration;
        }else{
             throw new CException( Yii::t( 'application', 'Required param type FlightPart' ) );
        }
    }
    
    public function __get( $name ) {
        if ( $name == 'departure_city' || $name == 'arrival_city' ) {
            if ( !$this->$name ) {
                $this->$name = City::model()->findByPk( $this->{$name . '_id'} );
                if ( !$this->$name ) throw new CException( Yii::t( 'application', '{var_name} not found. City with id {city_id} not set in db.', array(
                        '{var_name}' => $name, 
                        '{city_id}' => $this->{$name . '_id'} ) ) );
            }
            return $this->$name;
        } else {
            return $this->$name;
        }
    }

}