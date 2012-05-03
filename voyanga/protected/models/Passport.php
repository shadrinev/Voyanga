<?php
/**
 * Passport class
 * Class for saving and loading passport data
 * @author oleg
 *
 */
class Passport extends CActiveRecord {
    public $id;
    public $first_name;
    public $last_name;
    public $number;
    public $birthday;
    public $series;
    /*
     * documentTypeId values:
     * 1 - Passport RF
     * 2 - Passport other country
     * 3 - Zagran
     */
    public $document_type_id;
    public $expiration;
    public $country_id;
    public $gender_id;
    
    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }
    
    public function rules() {
        return array(
                // name, email, subject and body are required
                array(
                        'first_name, last_name, number, birthday, document_type_id, gender_id', 
                        'required' ) );
    }
    
    public function tableName() {
        return 'passport';
    }
    
    public function checkValid(){
        return true;
    }
}