<?php
class FlightSearch extends CActiveRecord
{
	public $id;
	public $timestamp;
	public $request_id;
	public $status;
	public $key;
	public $data;
	public $flight_class;
	public $oFlightVoyageStack;
	private $_aRoutes;
	
	public function __construct($scenario='insert')
	{
		parent::__construct($scenario);
		
	}
	
	public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	
	public function tableName()
    {
        return 'flight_search';
    }
    
    public function sendRequest($aParams){
    	$this->_aRoutes = array();
		//print_r($aParams);
		foreach($aParams['flights'] as $aFlightParams){
			$oRoute = new Route();
			$oRoute->departure_city_id = $aFlightParams['departure_city_id'];
			$oRoute->arrival_city_id = $aFlightParams['arrival_city_id'];
			list($dd,$mm,$yy) = explode('.',$aFlightParams['departure_date']);//TODO: date correct function
			
			$oRoute->departure_date = "{$yy}-{$mm}-{$dd}";
			$oRoute->adult_count = $aParams['adult_count'];
			$oRoute->child_count = $aParams['child_count'];
			$oRoute->infant_count = $aParams['infant_count'];
			$this->_aRoutes[] = $oRoute;
		}
		$this->flight_class = $aParams['flight_class'];
	
    	$sKey = $this->flight_class.json_encode($this->_aRoutes);
		$this->key = md5($sKey);
		
		
		$timestamp = date('Y-m-d H:i:s', time()-3600*3);
		$sameFlightSearch = FlightSearch::model()->find('`key`=:KEY AND timestamp>=:TIMESTAMP AND status=1', array(':KEY'=>$this->key,':TIMESTAMP'=>$timestamp) );
		$sameFlightSearch = false;
		
		if($sameFlightSearch){
			$this->id = $sameFlightSearch->id;
			$this->timestamp = $sameFlightSearch->timestamp;
			$this->data = $sameFlightSearch->data;
			echo "from cache";
			$this->request_id = $sameFlightSearch->request_id;
			$this->_aRoutes = Route::model()->findAll('`search_id`=:SEARCH_ID', array(':SEARCH_ID'=>$this->id));
		}else{
			//TODO: Making request to GDS
			//fill fields of object:
			//data
			//status
			//request_id
			$this->status = 1;
			$sJdata = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"].'/flightsearch.json'));
			
			$aParamsFS['aFlights'] = $sJdata->section->variants;
			$oFlightVoyageStack = new FlightVoyageStack($aParamsFS);
			echo "Initialize ... ";
			$this->oFlightVoyageStack = $oFlightVoyageStack;
			$this->data = '{}';
			$this->request_id = '1';
			$this->save();
			
		}
    }
    
 	public function __get($name)
    {
    	if($name === 'aRoutes'){
    		return $this->_aRoutes;
    		/*if(!$this->country){
	    		if($this->country_id){
	    			$this->country = Country::model()->findByPk($this->country_id);
	    			return $this->country;
	    		}else{
	    			return NULL;
	    		}
    		}else return $this->country;*/
    	}else {
    		return parent::__get($name);
    	}
    }
    
    public function save($runValidation = true, $attributes = null)
    {
    	if($runValidation){
    		if($this->_aRoutes){//check valid of Rotes
    			parent::save();//TODO: check good save
    			$this->id = $this->getPrimaryKey();
    			$this->timestamp = date('Y-m-d H:i:s');
    			foreach ($this->_aRoutes as $oRoute){
    				$oRoute->search_id = $this->id;
    				$oRoute->save();
    				$oRoute->id = $oRoute->getPrimaryKey();
    			}
    		}else{
    			
    		}
    		
    	}
    }
}