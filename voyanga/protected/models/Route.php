<?php
/**
 * Route class
 * Class for save and load routes for flight 
 * @author oleg
 *
 */
class Route extends CActiveRecord {
    public $id;
    public $search_id;
    public $departure_city_id;
    public $departure_airport_id;
    public $departure_date;
    public $arrival_city_id;
    public $arrival_airport_id;
    public $adult_count = 0;
    public $child_count = 0;
    public $infant_count = 0;
    private $departure_city;
    private $arrival_city;
    
    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }
    
    public function tableName() {
        return 'route';
    }
    
    public function __get( $name ) {
        if ( $name == 'departure_city' || $name == 'arrival_city' ) {
            if ( !$this->$name ) {
                $this->$name = City::model()->findByPk( $this->{$name . '_id'} );
                if ( !$this->$name ) throw new CException( Yii::t( 'application', '{var_name} not found. City with id {city_id} not set in db.', array(
                        '{var_name}' => $name, 
                        '{city_id}' => $this->{$name . '_id'} ) ) );
            }
            return $this->$name;
        } else {
            return $this->$name;
        }
    }

}