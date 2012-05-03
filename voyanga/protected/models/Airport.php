<?php
/**
 * Airport class
 * Information about airport
 * @author oleg
 *
 */
class Airport extends CActiveRecord {
    public $id;
    public $code;
    public $position;
    private $city;
    public $city_id;
    public $local_ru;
    public $local_en;
    private static $aAirports = array();
    
    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }
    
    public static function getAirportByCode( $sCode ) {
        if ( isset( Airport::$aAirports[$sCode] ) ) {
            return Airport::$aAirports[$sCode];
        } else {
            Yii::beginProfile('laodAirportFromDB');
            $oAirport = Airport::model()->findByAttributes( array(
                    'code' => $sCode ) );
            if ( $oAirport ) {
                $city = $oAirport->city;
                Airport::$aAirports[$oAirport->code] = $oAirport;
                Yii::endProfile('laodAirportFromDB');
                return Airport::$aAirports[$sCode];
            } else {
                throw new CException( Yii::t( 'application', 'Airport with code {code} not found', array(
                        '{code}' => $sCode ) ) );
            }
            
        }
    }
    
    public function tableName() {
        return 'airport';
    }
    
    public function __get( $name ) {
        if ( $name === 'city' ) {
            if ( !$this->city ) {
                if ( $this->city_id ) {
                    $this->city = City::model()->findByPk( $this->city_id );
                    if(!$this->city){
                        throw new CException( Yii::t( 'application', 'City with id {city_id} not found',array('{city_id}'=>$this->city_id) ) );
                    }
                    return $this->city;
                } else {
                    throw new CException( Yii::t( 'application', 'Property city_id in object Airport not set' ) );
                    return NULL;
                }
            } else {
                return $this->city;
            }
        } else {
            return parent::__get( $name );
        }
    }
}