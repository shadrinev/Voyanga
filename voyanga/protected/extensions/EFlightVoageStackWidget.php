<?php
class EFlightVoageStackWidget extends CWidget
{
	public $FlightVoyageStrategy;
	public $data;
	public $viewName;
	
	public function init(){
		//echo "init my widget";
		//$this->data = 'JJJ';
		//$this->FlightVoyageStrategy = 'zz nazad z';
	}
	
	
	public function run(){
		//echo "run";
		if(!$this->viewName){
			$this->viewName = 'kg_am';
		}
		$this->render($this->viewName);
	}
}