<?php
/**
 * FlightSearch class
 * Class for making FlightSearch requesh with saving results into db
 * @author oleg
 *
 */
class FlightSearch extends CActiveRecord {
    public $id;
    public $timestamp;
    public $request_id;
    public $status;
    public $key;
    public $data;
    public $flight_class;
    public $oFlightVoyageStack;
    private $_aRoutes;
    
    public function __construct( $scenario = 'insert' ) {
        parent::__construct( $scenario );
    
    }
    
    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }
    
    public function tableName() {
        return 'flight_search';
    }
    
    public function sendRequest(FlightSearchParams $oFlightSearchParams ) {
        if($oFlightSearchParams instanceof FlightSearchParams){
            if($oFlightSearchParams->checkValid()){
                $this->_aRoutes = $oFlightSearchParams->routes;
                $this->flight_class = $oFlightSearchParams->flight_class;
                $this->key = $oFlightSearchParams->key;
                
                $timestamp = date( 'Y-m-d H:i:s', time() - Yii::app()->params['fligh_search_cache_time'] );
                $sameFlightSearch = FlightSearch::model()->find( '`key`=:KEY AND timestamp>=:TIMESTAMP AND status=1', array(
                        ':KEY' => $this->key, 
                        ':TIMESTAMP' => $timestamp ) );
                $sameFlightSearch = false;
                
                if ( $sameFlightSearch ) {
                    $this->id = $sameFlightSearch->id;
                    $this->timestamp = $sameFlightSearch->timestamp;
                    $this->data = $sameFlightSearch->data;
                    echo "from cache";
                    $this->request_id = $sameFlightSearch->request_id;
                    $this->_aRoutes = Route::model()->findAll( '`search_id`=:SEARCH_ID', array(
                            ':SEARCH_ID' => $this->id ) );
                } else {
                    //TODO: Making request to GDS
                    //fill fields of object:
                    //data
                    //status
                    //request_id
                    $sJdata = Yii::app()->gdsAdapter->FlightSearch( $oFlightSearchParams );
                    if ( $sJdata ) {
                        $aParamsFS['aFlights'] = $sJdata->section->variants;
                        $oFlightVoyageStack = new FlightVoyageStack( $aParamsFS );
                        
                        $this->oFlightVoyageStack = $oFlightVoyageStack;
                        
                        $this->status = 1;
                        $this->data = json_encode($this->oFlightVoyageStack);
                        $this->request_id = '1';
                    } else
                        $this->status = 2;
                    echo "before saving";
                    $this->save();
                    
                    if($this->oFlightVoyageStack){
                        //saving best data to FlightCache
                        $aAttrs = array(
                                'adult_count' => $oFlightSearchParams->adult_count, 
                                'child_count' => $oFlightSearchParams->child_count, 
                                'infant_count' => $oFlightSearchParams->infant_count,
                                'flight_search_id'=>$this->id
                        );
                        $this->oFlightVoyageStack->setAttributes($aAttrs);
                        FlightCache::addCacheFromStack($this->oFlightVoyageStack);
                    }
                }
            }else{
                throw new CException( Yii::t( 'application', 'Data in oFlightSearchParams not valid' ) );
            }
        }else{
            throw new CException( Yii::t( 'application', 'Parametr oFlightSearchParams not type of FlightSearchParams' ) );
        }
    }
    
    public function __get( $name ) {
        if ( $name === 'aRoutes' ) {
            return $this->_aRoutes;
        } else {
            return parent::__get( $name );
        }
    }
    
    public function save( $runValidation = true, $attributes = null ) {
        if ( $runValidation ) {
            if ( $this->_aRoutes ) { 
                //check valid of Rotes
                //TODO: check good save
                parent::save(); 
                $this->id = $this->getPrimaryKey();
                $this->timestamp = date( 'Y-m-d H:i:s' );
                foreach( $this->_aRoutes as $oRoute ) {
                    $oRoute->search_id = $this->id;
                    $oRoute->save();
                    $oRoute->id = $oRoute->getPrimaryKey();
                }
            } else {
                throw new CException( Yii::t( 'application', 'Cant save FlightSearch without Routes' ) );
            }
        }
    }
}