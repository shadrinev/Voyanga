<?php
class FlightVoyageStack {
    public $aFlightVoyages = array();
    public $aFilterValues = array();
    public static $iToTop;

    public $iBestMask = 0; // bitwise mask 0b001 - Best price, 0b010 - best recommended, 0b100 best speed
    

    public function __construct( $aParams = NULL ) {
        if ( $aParams ){
            $this->aAirportsFrom = array(
                    array(), 
                    array() );
            $this->aAirportsTo = array(
                    array(), 
                    array() );
            $this->aTimePeriodFrom = array(
                    array(), 
                    array() );
            $this->aTimePeriodTo = array(
                    array(), 
                    array() );
            $this->aAirlines = array();
            $this->aTransits = array();
            $this->iBestTime = 0;
            $this->iBestPrice = 0;
            $this->iBestParams = 0;
            
            if ( $aParams['aFlights'] ){
                foreach( $aParams['aFlights'] as $oFlightParams ){
                    $oFlightVoyage = new FlightVoyage( $oFlightParams );
                    $bNeedSave = TRUE;
                    if ( $bNeedSave ){
                        $this->aFlightVoyages[] = $oFlightVoyage;
                        $iFullDuration = $oFlightVoyage->getFullDuration();
                    }
                }
            }
        }
    }
    
    public function addFlightVoyage( FlightVoyage $oFlightVoyage ) {
        $this->aFlightVoyages[] = $oFlightVoyage;
        $this->iBestMask |= $oFlightVoyage->iBestMask;
    }
    
    /**
     * Function for sorting by uksort
     * @param $a
     * @param $b
     */
    private static function compare_array( $a, $b ) {
        if ( $a < $b ){
            $ret = -1;
        }elseif ( $a > $b ){
            $ret = 1;
        }else{
            $ret = 0;
        }
        return $ret;
    }
    
    public function groupBy( $sKey, $iToTop = NULL, $iFlightIndex = FALSE ) {
        $aVariantsStacks = array();
        
        foreach( $this->aFlightVoyages as $oFlihtVoyage ){
            switch ( $sKey ){
                case "price":
                    $sVal = intval( $oFlihtVoyage->price );
                    break;
            }
            

            if ( !isset( $aVariantsStacks[$sVal] ) ){
                $aVariantsStacks[$sVal] = new FlightVoyageStack();
            
            }
            $aVariantsStacks[$sVal]->addFlightVoyage( $oFlihtVoyage );
        }
        uksort( $aVariantsStacks, 'FlightVoyageStack::compare_array' ); //sort array by key
        reset( $aVariantsStacks );
        $aEach = each( $aVariantsStacks );
        return $aVariantsStacks;
    }
}