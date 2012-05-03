<?php
/**
 * City class
 * Contain information about city
 * @author oleg
 *
 */
class City extends CActiveRecord {
    public $id;
    public $sCode;
    private $_country;
    public $country_id;
    public $iPosition;
    public $sLocalRu;
    public $sLocalEn;
    
    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }
    
    public function tableName() {
        return 'city';
    }
    
    public function __get( $name ) {
        if ( $name === 'country' ){
            if ( !$this->_country ){
                if ( $this->country_id ){
                    $this->_country = Country::model()->findByPk( $this->country_id );
                    return $this->_country;
                }else{
                    return NULL;
                }
            }else
                return $this->_country;
        }else{
            return parent::__get( $name );
        }
    }
}