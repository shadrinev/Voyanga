<?php

class Airline extends CActiveRecord {
    public $id;
    public $sCode;
    public $iPosition;
    public $sLocalRu;
    public $sLocalEn;
    private static $aAirlines = array();
    
    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }
    
    public static function getAirlineByCode($sCode){
        if(isset(Airline::$aAirlines[$sCode])){
            return Airline::$aAirlines[$sCode];
        }else{
            $oAirline = Airline::model()->findByAttributes(array('code'=>$sCode));
            if($oAirline){
                Airline::$aAirlines[$oAirline->code] = $oAirline;
                return Airline::$aAirlines[$sCode];
            }else{
                throw new CException( Yii::t( 'application', 'Airline with code {code} not found', array('{code}'=>$sCode)) );
            }
        }
    }
    
    public function tableName() {
        return 'airline';
    }
}