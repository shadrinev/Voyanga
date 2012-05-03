<?php
/**
 * Country class
 * Contain information about country
 * @author oleg
 *
 */
class Country extends CActiveRecord {
    public $id;
    public $sCode;
    public $iPosition;
    public $sLocalRu;
    public $sLocalEn;
    
    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }
    
    public function tableName() {
        return 'country';
    }
}