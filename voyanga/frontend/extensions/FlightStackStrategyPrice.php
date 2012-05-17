<?php
class FlightStackStrategyPrice extends CBehavior
{
	public $sortKey;
    public $flightSearchKey;
	public function setFlightStack($oFlightVoyageStack){
		if($oFlightVoyageStack instanceof FlightVoyageStack){
			$this->owner->data = $oFlightVoyageStack->groupBy($this->sortKey);
			$this->owner->viewName = 'flight_voyages_stack_widget';
		}else throw new CException(Yii::t('yii','Parametr must be a Type FlightVoyageStack".'	) );
		
		
	} 
}