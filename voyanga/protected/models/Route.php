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
    
    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }
    
    public function tableName() {
        return 'route';
    }

}