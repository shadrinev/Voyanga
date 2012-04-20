<?php
/**
 * GDSAdapter class
 * Frontend layer GDS adapter
 * @author oleg
 *
 */
class GDSAdapter extends CApplicationComponent {
    public function FlightSearch( $aParams ) {
        $sData = file_get_contents( $_SERVER["DOCUMENT_ROOT"] . '/flightsearch.json' );
        $sStatus = 'ok';
        $sDescription = '';
        if ( $sData ){
            $sJdata = json_decode( file_get_contents( $_SERVER["DOCUMENT_ROOT"] . '/flightsearch.json' ) );
            if ( !$sJdata->section ){
                $sStatus = 'error';
                $sDescription = 'Error input parameters';
            }
        }else{
            $sStatus = 'error';
            $sDescription = 'Cant connect to remote GDS Adapter';
        }
        if ( $sStatus == 'error' ){
            throw new CException( Yii::t( 'application', 'Problem in FlightSearch request. Reason: {description}', array(
                    '{description}' => $sDescription ) ) );
            return FALSE;
        }else
            return $sJdata;
    }
    
    public function FlightBooking() {

    }
    
    public function FlightTariffRules() {

    }
    
    public function FlightTicketing() {

    }
    
    public function FlightVoid() {

    }
}