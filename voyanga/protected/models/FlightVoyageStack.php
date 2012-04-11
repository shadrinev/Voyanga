<?php
class FlightVoyageStack
{
	public $aFlightVoyages = array();
	public $aFilterValues = array();
	public static $iToTop;//переменная для сортировки с выталкиванием значения наверх
	public $bIsFastest = FALSE;
	public $bIsRecommended = FALSE;
	public $bIsBestPrice = FALSE;
	const BIT_BestPrice = 1;
	const BIT_Recommended = 2;
	const BIT_Fastest = 4;
	const FACTOR_Price = 100;
	const FACTOR_Time = 70;
	public $iBestMask = 0;// bitwise mask 0b001 - Best price, 0b010 - best recommended, 0b100 best speed
	
	
	public function __construct($aParams = NULL){
		if($aParams){
			//echo "initialize Stack";
			$this->aAirportsFrom = array(array(),array());
			$this->aAirportsTo = array(array(),array());
			$this->aTimePeriodFrom = array(array(),array());
			$this->aTimePeriodTo = array(array(),array());
			$this->aAirlines = array();
			$this->aTransits = array();
			$this->iBestTime = 0;
			$this->iBestPrice = 0;
			$this->iBestParams = 0;
			
			if($aParams['aFlights']){
				foreach ($aParams['aFlights'] as $oFlightParams){
					$oFlightVoyage = new FlightVoyage($oFlightParams);
					//echo "initialize Voyage";
					$bNeedSave = TRUE;
					if($bNeedSave){
						$this->aFlightVoyages[] = $oFlightVoyage;
						//echo "add Voyage";
						$iFullDuration = $oFlightVoyage->getFullDuration();
					}
				}
			}
			
		}
	}
	
	public function addFlightVoyage(FlightVoyage $oFlightVoyage){
		$this->aFlightVoyages[] = $oFlightVoyage;
		$this->iBestMask |= $oFlightVoyage->iBestMask;
	}
	
	/**
	 * функция для сортировки через uksort
	 * @param $a
	 * @param $b
	 */
	private static function compare_array($a, $b){
		if($a < $b){
			$ret = -1;
		}elseif($a > $b){
			$ret = 1;
		}else{
			$ret = 0;
		}
		//if($a === Biletoid_VariantsStack::$iToTop){
		//	$ret = -1;
		//}elseif($b === Biletoid_VariantsStack::$iToTop){
		//	$ret = 1;
		//}
		return $ret;
	}
	
	public function groupBy($sKey,$iToTop = NULL,$iFlightIndex = FALSE){
		$aVariantsStacks = array();
		
		foreach ($this->aFlightVoyages as $oFlihtVoyage){
			switch($sKey){
				case "price":
					$sVal = intval($oFlihtVoyage->price);
					break;
			}
			//$iFullDuration = $oFlihtVoyage->getFullDuration() + $oFlihtVoyage->getFullDuration(TRUE);
			
			if(!isset($aVariantsStacks[$sVal])){
				$aVariantsStacks[$sVal] = new FlightVoyageStack();

			}
			$aVariantsStacks[$sVal]->addFlightVoyage($oFlihtVoyage);
			//$this->aFilterValues[$sVal] = array('value'=>$sVal,'selected'=>FALSE);
		}
		//Biletoid_VariantsStack::$iToTop = intval($iToTop);//задаем значение которое будем выталкивать наверх.
		uksort($aVariantsStacks, 'FlightVoyageStack::compare_array');//сортировка массива по ключу
		reset($aVariantsStacks);
		$aEach = each($aVariantsStacks);
		//$this->aFilterValues[$aEach['key']]['selected'] = TRUE;
		return $aVariantsStacks;
	}
}