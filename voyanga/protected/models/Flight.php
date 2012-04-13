<?php
/**
 * Flight class
 * Class with one element of marchroute
 * @author oleg
 *
 */
class Flight {
    public $aFlightParts = array();
    public $aTransits = array();
    public $departure_city_id;
    public $arrival_city_id;
    public $fullDuration = 0;
    private $departure_city;
    private $arrival_city;
    
    public function addPart( FlightPart $oPart ) {
        if ( !$this->aFlightParts ){
            $this->aFlightParts[] = $oPart;
            $this->departure_city_id = $oPart->departure_city_id;
            $this->arrival_city_id = $oPart->arrival_city_id;
        }else{
            $oLastPart = &$this->aFlightParts[count( $this->aFlightParts ) - 1];
            $aTransit = array();
            $aTransit['time_for_transit'] = $oPart->timestampBegin - $oLastPart->timestampEnd;
            $aTransit['city_id'] = $oPart->departure_city_id;
            $this->arrival_city_id = $oPart->arrival_city_id;
            $this->aTransits[] = $aTransit;
            $this->aFlightParts[] = $oPart;
            $this->fullDuration += $aTransit['time_for_transit'];
        }
        $this->fullDuration += $oPart->duration;
    }
    
    public function __get( $name ) {
        if ( $name == 'departure_city' || $name == 'arrival_city' ){
            if ( !$this->$name ){
                $this->$name = City::model()->findByPk( $this->{$name . '_id'} );
                if(!$this->$name) throw new CException( Yii::t( 'application', '{var_name} not found. City with id {city_id} not set in db.', array('{var_name}'=>$name,'{city_id}'=>$this->{$name . '_id'}) ) );
            }
            return $this->$name;
        }else{
            return $this->$name;
        }
    }

}