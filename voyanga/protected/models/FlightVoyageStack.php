<?php
/**
 * FlightVoyageStack class
 * Class with array of FlightVoyage
 * @author oleg
 *
 */
class FlightVoyageStack {
    public $aFlightVoyages = array();
    public $aFilterValues = array();
    public static $iToTop;
    
    public $iBestMask = 0; // bitwise mask 0b001 - Best price, 0b010 - best recommended, 0b100 best speed
    

    public function __construct( $aParams = NULL ) {
        if ( $aParams ) {
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
            $this->iBestPriceTime = 0;
            
            if ( $aParams['aFlights'] ) {
                
                $bParamsNeedInit = true;
                foreach( $aParams['aFlights'] as $oFlightParams ) {
                    $oFlightVoyage = new FlightVoyage( $oFlightParams );
                    $bNeedSave = TRUE;
                    if ( $bNeedSave ) {
                        //If Voyage don't filtered, add to stack
                        $this->aFlightVoyages[] = $oFlightVoyage;
                        
                        $iFullDuration = $oFlightVoyage->getFullDuration();
                        if ( $bParamsNeedInit ) {
                            //initializing best params
                            $bParamsNeedInit = false;
                            $this->iBestPrice = $oFlightVoyage->price;
                            $this->iBestTime = $iFullDuration;
                            $this->iBestTimeInd = count( $this->aFlightVoyages ) - 1;
                            $this->iBestPriceInd = $this->iBestTimeInd;
                        }
                        if ( $this->iBestPrice > $oFlightVoyage->price ) {
                            //update best price params 
                            $this->iBestPrice = $oFlightVoyage->price;
                            $this->iBestPriceInd = count( $this->aFlightVoyages ) - 1;
                        }
                        if ( $this->iBestTime > $iFullDuration ) {
                            //update best time params
                            $this->iBestTime = $iFullDuration;
                            $this->iBestTimeInd = count( $this->aFlightVoyages ) - 1;
                        }
                    }
                }
                
                $bParamsNeedInit = true;
                //find best pricetime params
                foreach( $this->aFlightVoyages as $iInd => $oFlightVoyage ) {
                    $iFullDuration = $oFlightVoyage->getFullDuration();
                    $iParamsFactor = intval( ( $oFlightVoyage->price / $this->iBestPrice ) * Yii::app()->params['flight_price_factor'] ) + intval( ( $iFullDuration / $this->iBestTime ) * Yii::app()->params['flight_time_factor'] );
                    
                    if ( $bParamsNeedInit ) {
                        $bParamsNeedInit = false;
                        $this->iBestPriceTime = $iParamsFactor;
                    }
                    if ( $this->iBestPriceTime > $iParamsFactor ) {
                        $this->iBestPriceTime = $iParamsFactor;
                        $this->iBestPriceTimeInd = $iInd;
                    }
                }
            }
        }
    }
    
    /**
     * addFlightVoyage
     * Add FlightVoyage object to this FlightVoyageStack
     * @param FlightVoyage $oFlightVoyage
     */
    public function addFlightVoyage( FlightVoyage $oFlightVoyage ) {
        $this->aFlightVoyages[] = $oFlightVoyage;
        $this->iBestMask |= $oFlightVoyage->iBestMask;
    }
    
    public function setAttributes( $values ) {
        foreach( $values as $name => $value ) {
            $this->$name = $value;
        }
    }
    
    /**
     * Function for sorting by uksort
     * @param $a
     * @param $b
     */
    private static function compare_array( $a, $b ) {
        if ( $a < $b ) {
            return -1;
        } elseif ( $a > $b ) {
            return 1;
        }
        
        return 0;
    }
    
    /**
     * groypBy method
     * Group internal FlightVoyage elements, and return array of FlightVoyageStack elements
     * @param string $sKey - name key for grouping
     * @param integer $iToTop - push to top group with this value
     * @param integer $iFlightIndex - index of Flight in FlightVoyage
     * @return array of FlightVoyageStack
     */
    public function groupBy( $sKey, $iToTop = NULL, $iFlightIndex = FALSE ) {
        $aVariantsStacks = array();
        
        foreach( $this->aFlightVoyages as $oFlihtVoyage ) {
            switch ( $sKey ) {
                case "price":
                    $sVal = intval( $oFlihtVoyage->price );
                    break;
            }
            
            if ( !isset( $aVariantsStacks[$sVal] ) ) {
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