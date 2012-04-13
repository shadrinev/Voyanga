<?php
/**
 * FlightVoyage class
 * Class with full flight marchroute
 * @author oleg
 *
 */
class FlightVoyage {
    public $price;
    public $taxes;
    public $flight_key;
    public $commission;
    public $aFlights;
    public $oAdultPassengerInfo;
    public $oChildPassengerInfo;
    public $oInfantPassengerInfo;
    public $iBestMask = 0;
    
    public function __construct( $oParams ) {
        $this->price = $oParams->full_sum;
        $this->taxes = $oParams->commission_price;
        $this->flight_key = $oParams->flight_key;
        $this->commission = $oParams->commission_price;
        $this->aFlights = array();
        $iInd = 0;
        $lastArrTime = 0;
        $lastCityToId = 0;
        $bStart = true;
        if ( $oParams->parts ){
            foreach( $oParams->parts as $iGroupId => $aParts ){
                $iIndPart = 0;
                $this->aFlights[$iGroupId] = new Flight();
               
                foreach( $aParts as $oPartParams ){
                    $oPart = new FlightPart( $oPartParams );
                    $this->aFlights[$iGroupId]->addPart( $oPart );
                
                }
            }
        }else
            throw new CException( Yii::t( 'application', 'Required param $oParams->parts not set.' ) );
    
    }
    
    public function getFullDuration() {
        $iFullDuration = 0;
        foreach( $this->aFlights as $oFlight ){
            $iFullDuration += $oFlight->fullDuration;
        }
        return $iFullDuration;
    }

}