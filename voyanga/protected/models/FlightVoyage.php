<?php
/**
 * FlightVoyage class
 * Class with full flight marchroute
 * @author oleg
 *
 */
class FlightVoyage {
    public $iPrice;
    public $iTaxes;
    public $sFlightKey;
    public $sAirlineCode;
    public $iCommission;
    public $aFlights;
    public $oAdultPassengerInfo;
    public $oChildPassengerInfo;
    public $oInfantPassengerInfo;
    public $iBestMask = 0;
    
    public function __construct( $oParams ) {
        $this->$iPrice = $oParams->full_sum;
        $this->iTaxes = $oParams->commission_price;
        $this->sFlightKey = $oParams->flight_key;
        $this->iCommission = $oParams->commission_price;
        $this->aFlights = array();
        $iInd = 0;
        $lastArrTime = 0;
        $lastCityToId = 0;
        $bStart = true;
        if ( $oParams->parts ) {
            foreach( $oParams->parts as $iGroupId => $aParts ) {
                $iIndPart = 0;
                $this->aFlights[$iGroupId] = new Flight();
                
                foreach( $aParts as $oPartParams ) {
                    $oPart = new FlightPart( $oPartParams );
                    $this->aFlights[$iGroupId]->addPart( $oPart );
                    if(!$this->sAirlineCode) {
                        $this->sAirlineCode = $oPart->airline_id;
                    }
                }
            }
        } else {
            throw new CException( Yii::t( 'application', 'Required param $oParams->parts not set.' ) );
        }
    
    }
    
    public function getFullDuration() {
        $iFullDuration = 0;
        foreach( $this->aFlights as $oFlight ) {
            $iFullDuration += $oFlight->fullDuration;
        }
        return $iFullDuration;
    }
    

}