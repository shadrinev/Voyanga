<?php
class FlightVoyage
{
	public $price;
	public $taxes;
	public $flight_key;
	public $commission;
	public $aFlights;
	public $oAdultPassengerInfo;
	public $oChildPassengerInfo;
	public $oInfantPassengerInfo;
	public $iBestMask = 0;
	
	public function __construct($oParams){
		$this->price = $oParams->full_sum;
		$this->taxes = $oParams->commission_price;
		$this->flight_key = $oParams->flight_key;
		$this->commission = $oParams->commission_price;
		$this->aFlights = array();
		//$this->price = $oParams->price;
		$iInd = 0;
		$lastArrTime = 0;
		$lastCityToId = 0;
		$bStart = true;
		
		foreach($oParams->parts as $iGroupId=>$aParts){
			$iIndPart = 0;
			$this->aFlights[$iGroupId] = new Flight();
			if($this->flight_key == '-172'){
				//print_r($aParts);
			}
			foreach($aParts as $oPartParams){
				//$oPartParams;
				$oPart = new FlightPart($oPartParams);
				if($this->flight_key == '-172'){
					//print_r($oPartParams);
					echo "add part to group {$iGroupId}";
				}
				$this->aFlights[$iGroupId]->addPart($oPart);
				//$this->aFlights[$iGroupId]->aParts[] = new FlightPart($oPartParams);
				/*if($bStart){
					$iGroupId = $oPart->iGroupId;
					$bStart = false;
					$iIndPart = 0;
				}
				if($iGroupId != $oPart->iGroupId){
					$iInd++;
					$iIndPart = 0;
				}*/
				/*if($lastArrTime) {
					$oLastPart = &$this->aFlights[$iGroupId]->aParts[(count($this->aFlights[$iGroupId]->aParts)-1)];
					
					if( ($lastArrTime < $oPart->timestampBegin) && ($lastCityToId != $oPart->toCityId)){
						$aTransit['timeForTransit'] = $oPart->timestampBegin - $lastArrTime;
						$aTransit['city'] = Biletoid_References::getCityById($oPart->fromCityId)->local_ru;
						$aTransit['city_en'] = Biletoid_References::getCityById($oPart->fromCityId)->local_en;
						$aTransit['country'] = Biletoid_References::getCityById($oPart->fromCityId)->country->local_ru;
						$aTransit['country_en'] = Biletoid_References::getCityById($oPart->fromCityId)->country->local_en;
						$aTransit['arrival_time'] = date("H:i",$oLastPart->timestampEnd);
						$aTransit['arrival_airport'] = Biletoid_References::getAirportById($oLastPart->toAirportId)->local_ru;
						$aTransit['arrival_airport_en'] = Biletoid_References::getAirportById($oLastPart->toAirportId)->local_en;
						$aTransit['arrival_terminal'] = $oLastPart->toTerminalCode;
						$aTransit['departure_time'] = date("H:i",$oPart->timestampBegin);
						$aTransit['departure_airport'] = Biletoid_References::getAirportById($oPart->fromAirportId)->local_ru;
						$aTransit['departure_airport_en'] = Biletoid_References::getAirportById($oPart->fromAirportId)->local_en;
						$aTransit['departure_terminal'] = $oPart->fromTerminalCode;
						$aTransit['departure_code'] = $oPart->code;
						$aTransit['duration'] = $oPart->duration;
						$aTransit['airline'] = Biletoid_References::getAirlineById($oPart->transportAirlineId)->local_ru;
						$aTransit['aircraft_name'] = $oPart->aircraft_name;
						$this->fullDuration[$iInd] += $aTransit['timeForTransit'];
						$this->aTransits[$iInd][] = $aTransit;
						$this->fullDuration[$iInd] += $oPart->duration;
						$this->aParts[$iInd][] = $oPart;
					}else{
						$this->aParts[$iInd][(count($this->aParts[$iInd])-1)]->addTariff($aPart['tariff']);
					}
				}else{
					$this->fullDuration[$iInd] += $oPart->duration;
					$this->aParts[$iInd][] = $oPart;
				}
				$lastArrTime = $oPart->timestampEnd;
				$lastCityToId = $oPart->toCityId;*/
			}
		}
	}
	
	public function getFullDuration(){
		$iFullDuration = 0;
		foreach ($this->aFlights as $oFlight){
			$iFullDuration += $oFlight->fullDuration;
		}
		return $iFullDuration;
	}
	
	
	
	
	public $aPriceDetails =  array();
	public $tariffs = array(array(),array());
	public $aParts = array(array(),array());//array with index 0 - direct flight, index 1 - return fright
	public $fullDuration = array(0,0);
	public $aTransits = array(array(),array());
	public $bWithReturn = FALSE;//туда и обратно
	const BIT_BestPrice = 1;
	const BIT_Recommended = 2;
	const BIT_Fastest = 4;
	public static $flightClasses = array( 1=>array('E','W'), 2=>array('R','F','B') );
	
	
	public function __construct1($aSection,$aParams){
		$this->price = 0;
		$this->dateId = $aSection['dateId'];
		$this->aPriceDetails = $aSection['priceDetail'];
		foreach ($this->aPriceDetails as $oPriceDetails){
			$this->price += Biletoid_Utils::getFloatWithPoint($oPriceDetails['totalValue']);
		}
		//$this->price = Biletoid_Utils::getFloatWithPoint($this->price);
		//$this->sTariff = $aSection;
		//$this->taxes = $aSection['taxes'];
		//$this->commission = $aSection['commissionValue'];
		$iInd = 0;
		$lastArrTime = 0;
		$lastCityToId = 0;
		$iGroupId = 1;
		foreach ($aSection['variants'] as $aPart){
			$oPart = new Biletoid_Part($aPart);
			if($oPart->groupId != $iGroupId){
				$this->bWithReturn =TRUE;
				$iInd = 1;
				$lastArrTime = 0;
				$iGroupId++;
			}
			if($lastArrTime) {
				$oLastPart = &$this->aParts[$iInd][(count($this->aParts[$iInd])-1)];
				if( ($lastArrTime < $oPart->timestampBegin) && ($lastCityToId != $oPart->toCityId)){
					$aTransit['timeForTransit'] = $oPart->timestampBegin - $lastArrTime;
					$aTransit['city'] = Biletoid_References::getCityById($oPart->fromCityId)->local_ru;
					$aTransit['city_en'] = Biletoid_References::getCityById($oPart->fromCityId)->local_en;
					$aTransit['country'] = Biletoid_References::getCityById($oPart->fromCityId)->country->local_ru;
					$aTransit['country_en'] = Biletoid_References::getCityById($oPart->fromCityId)->country->local_en;
					$aTransit['arrival_time'] = date("H:i",$oLastPart->timestampEnd);
					$aTransit['arrival_airport'] = Biletoid_References::getAirportById($oLastPart->toAirportId)->local_ru;
					$aTransit['arrival_airport_en'] = Biletoid_References::getAirportById($oLastPart->toAirportId)->local_en;
					$aTransit['arrival_terminal'] = $oLastPart->toTerminalCode;
					$aTransit['departure_time'] = date("H:i",$oPart->timestampBegin);
					$aTransit['departure_airport'] = Biletoid_References::getAirportById($oPart->fromAirportId)->local_ru;
					$aTransit['departure_airport_en'] = Biletoid_References::getAirportById($oPart->fromAirportId)->local_en;
					$aTransit['departure_terminal'] = $oPart->fromTerminalCode;
					$aTransit['departure_code'] = $oPart->code;
					$aTransit['duration'] = $oPart->duration;
					$aTransit['airline'] = Biletoid_References::getAirlineById($oPart->transportAirlineId)->local_ru;
					$aTransit['aircraft_name'] = $oPart->aircraft_name;
					$this->fullDuration[$iInd] += $aTransit['timeForTransit'];
					$this->aTransits[$iInd][] = $aTransit;
					$this->fullDuration[$iInd] += $oPart->duration;
					$this->aParts[$iInd][] = $oPart;
				}else{
					$this->aParts[$iInd][(count($this->aParts[$iInd])-1)]->addTariff($aPart['tariff']);
				}
			}else{
				$this->fullDuration[$iInd] += $oPart->duration;
				$this->aParts[$iInd][] = $oPart;
			}
			$lastArrTime = $oPart->timestampEnd;
			$lastCityToId = $oPart->toCityId;
		}
	}
	
	
	
	public function getAdultsCount() {
		$iAdutlsCount = 0;
		foreach ($this->aPriceDetails as $aPriceInfo) {
			if ($aPriceInfo['touristType'] == 'adult') {
				$iAdutlsCount++;
			}
		}
		return $iAdutlsCount;
	}
	
	public function getChildrenCount() {
		$iChildrenCount = 0;
		foreach ($this->aPriceDetails as $aPriceInfo) {
			if ($aPriceInfo['touristType'] == 'child') {
				$iChildrenCount++;
			}
		}
		return $iChildrenCount;
	}
	
	public function getInfantsCount() {
		$iInfantsCount = 0;
		foreach ($this->aPriceDetails as $aPriceInfo) {
			if ($aPriceInfo['touristType'] == 'infant') {
				$iInfantsCount++;
			}
		}
		return $iInfantsCount;
	}
	
	public function getAirline($bReturnDirect = FALSE, $bNameIsImage = TRUE, $bReturnIsObject = FALSE, $bForTicket = FALSE){
		$iInd = $bReturnDirect?1:0;
		$aAirlines = array();
		if(!$bReturnIsObject){
			foreach ($this->aParts[$iInd] as $oPart){
				$oAirline = Biletoid_References::getAirlineById($oPart->airlineId);
				if(file_exists(ROOT.'/public/img/any_logos/'.$oAirline->code.'.png') && $bNameIsImage){
					if ($bForTicket) {
						$aAirlines[$oPart->airlineId] = '<img src="http://www.'.Biletoid::getConfig()->host.'/img/any_logos/'.$oAirline->code.'.png" alt="'.$oAirline->local_ru.'" title="'.$oAirline->local_ru.'" class="air_logo" />';
					} else {
						$aAirlines[$oPart->airlineId] = '<img src="/img/any_logos/'.$oAirline->code.'.png" alt="'.$oAirline->local_ru.'" title="'.$oAirline->local_ru.'" class="air_logo" />';
					}
				}else{
					$aAirlines[$oPart->airlineId] = Biletoid_References::getAirlineById($oPart->airlineId)->name_ru;
				}
			}
			$sAirlines = join(', ',$aAirlines);
			return $sAirlines;
		}else{
			$oPart = array_pop($this->aParts[$iInd]);
			return Biletoid_References::getAirlineById($oPart->airlineId);
		}
		//return Biletoid_References::getAirlineById($this->aParts[$iInd][0]->airlineId)->name_ru;
	}
	
	public function getAirlinesIds(){
		$aAirlines = array();
		foreach ($this->aParts[0] as $oPart){
			$aAirlines[$oPart->airlineId] = $oPart->airlineId;
		}
		foreach ($this->aParts[1] as $oPart){
			$aAirlines[$oPart->airlineId] = $oPart->airlineId;
		}
		$sAirlines = join(',',$aAirlines);
		return $sAirlines;
	}

	public function isOnlyDirect(){
		$bReturn = count($this->aTransits[0]) > 0? FALSE:TRUE;
		if($this->bWithReturn){
			$bReturn = $bReturn && ( count($this->aTransits[1]) > 0 ? FALSE : TRUE);
		}
		return $bReturn;
	}

	public function getAirlineId($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		return $this->aParts[$iInd][0]->airlineId;
	}
	
	public function getCarrierAirline($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		//print_r($this->aParts[$iInd][0]);
		return Biletoid_References::getAirlineById($this->aParts[$iInd][0]->transportAirlineId)->local_ru;
	}
	
	public function getAircrafts($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		$aAircrafts = array();
		foreach ($this->aParts[$iInd] as $oPart){
			$aAircrafts[$oPart->aircraft_name] = $oPart->aircraft_name;
		}
		$sAircrafts = join('<br />',$aAircrafts);
		return $sAircrafts;
	}
	
	public function getDepartureTime($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		return Biletoid_Utils::timestampToTime($this->aParts[$iInd][0]->timestampBegin);
	}
	
	public function getDepartureDate($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		return date("d.m.Y", $this->aParts[$iInd][0]->timestampBegin);
	}
	
	public function getDepartureTimestamp($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		return $this->aParts[$iInd][0]->timestampBegin;
	}
	
	public function getArrivalTime($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		return Biletoid_Utils::timestampToTime($this->aParts[$iInd][count($this->aParts[$iInd])-1]->timestampEnd);
	}
	
	public function getArrivalDate($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		return date("d.m.Y", $this->aParts[$iInd][count($this->aParts[$iInd])-1]->timestampEnd);
	}
	
	public function compareFlightDates($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		return (intval(date("Ymd", $this->aParts[$iInd][count($this->aParts[$iInd])-1]->timestampEnd)) - intval(date("Ymd", $this->aParts[$iInd][0]->timestampBegin)));
	}
	
	public function getDepartureCity($bReturnDirect = FALSE,$sLocal = 'ru'){
		$iInd = $bReturnDirect?1:0;
		return Biletoid_References::getCityById($this->aParts[$iInd][0]->fromCityId)->{'local_'.$sLocal};
	}
	
	public function getDepartureCityObject($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		return Biletoid_References::getCityById($this->aParts[$iInd][0]->fromCityId);
	}
	
	public function getDepartureAirport($bReturnDirect = FALSE,$sLocal = 'ru'){
		$iInd = $bReturnDirect?1:0;
		return Biletoid_References::getAirportById($this->aParts[$iInd][0]->fromAirportId)->{'name_'.$sLocal};
	}
	
	public function getDepartureAirportCode($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		return Biletoid_References::getAirportById($this->aParts[$iInd][0]->fromAirportId)->code;
	}
	
	public function getArrivalCity($bReturnDirect = FALSE,$sLocal = 'ru'){
		$iInd = $bReturnDirect?1:0;
		return Biletoid_References::getCityById($this->aParts[$iInd][count($this->aParts[$iInd])-1]->toCityId)->{'local_'.$sLocal};
	}
	
	public function getArrivalCityObject($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		return Biletoid_References::getCityById($this->aParts[$iInd][count($this->aParts[$iInd])-1]->toCityId);
	}
	
	public function getArrivalAirport($bReturnDirect = FALSE,$sLocal = 'ru'){
		$iInd = $bReturnDirect?1:0;
		return Biletoid_References::getAirportById($this->aParts[$iInd][count($this->aParts[$iInd])-1]->toAirportId)->{'name_'.$sLocal};
	}
	
	public function getArrivalAirportCode($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		return Biletoid_References::getAirportById($this->aParts[$iInd][count($this->aParts[$iInd])-1]->toAirportId)->code;
	}
	
	/**
	 * Функция для выдачи используемых тарифов в виде строки
	 * @param $bReturnDirect прямой или обратный рейс
	 */
	public function getTariffClass($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		$aTariffs = array();
		foreach ($this->aParts[$iInd] as $oPart){
			foreach($oPart->tariff as $oTariff){
				$aTariff = array();
				$aTariff['stdKey'] = $oTariff['stdKey'];
				$aTariff['name'] = $oTariff['nameLat'];

				$aTariffs[$aTariff['name']] = $aTariff['name'];
			}
		}
		$sTariffs = implode(', ',$aTariffs);
		return $sTariffs;
	}
	
	public function getFlightClass($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		
		foreach ($this->aParts[$iInd] as $oPart){
			foreach($oPart->tariff as $oTariff){
				if(in_array($oTariff['stdKey'], Biletoid_Variant::$flightClasses[2])){
					return 2;
				}else{
					return 1;
				}
			}
		}
	}
	
	public function getCommision(){
		$iCommision = 0;
		foreach ($this->aPriceDetails as $oPriceDetails){
			$iCommision += Biletoid_Utils::getFloatWithPoint($oPriceDetails['agencyCharges']);
		}
		return Biletoid_Utils::getFloatWithPoint($iCommision);
	}
	
	public function getFullCommission(){
		$iCommision = 0;
		foreach ($this->aPriceDetails as $oPriceDetails){
			$iCommision += Biletoid_Utils::getFloatWithPoint($oPriceDetails['commissionValue']);
		}
		return Biletoid_Utils::getFloatWithPoint($iCommision);
	}
	
	/**
	 * Функция получения стоимости без учета коммисий
	 */
	/*public function getTariffPrice(){
		$iTariffPrice = 0;
		foreach ($this->aPriceDetails as $oPriceDetails){
			$iTariffPrice += $oPriceDetails['tariff'];
			$iTariffPrice += $oPriceDetails['taxes'];
		}
		return $iTariffPrice;
	}*/
	
	/**
	 * 
	 * Функции для рассчета стоимости тарифа, сборов, коммиссии и суммы
	 */
	public function getTariffPrice() {
		$iTariffPrice = $this->price - $this->getCommision();
		return Biletoid_Utils::getFloatWithPoint($iTariffPrice);
	}
	
	public function getChargesPrice() {
		$iCommissionPrice = $this->getCommision();
		return Biletoid_Utils::getFloatWithPoint($iCommissionPrice);
	}
	
	public function getCommissionPrice() {
		if($this->systemId){
			$iCommission = Biletoid_Booking::$aPaymentSystems[$this->systemId]['commission'];
		}else{
			$iCommission = Biletoid::getConfig()->booking->payment_commission;
		}
		$iFullSumm = $this->price / (1 - $iCommission);
		$iFullSumm = ceil($iFullSumm);
		$iChargesPrice = $iFullSumm - $this->price ;
		return Biletoid_Utils::getFloatWithPoint($iChargesPrice);
	}
	
	public function getFullSumm() {
		if($this->systemId){
			$iCommission = Biletoid_Booking::$aPaymentSystems[$this->systemId]['commission'];
		}else{
			$iCommission = Biletoid::getConfig()->booking->payment_commission;
		}
		
		$iFullSumm = $this->price / (1 - $iCommission);
		$iFullSumm = ceil($iFullSumm);
		return Biletoid_Utils::getFloatWithPoint($iFullSumm);
	}
	
	public function getTransitsCount($bReturnDirect = FALSE){
		$iInd = $bReturnDirect?1:0;
		return count($this->aTransits[$iInd]);
	}
	
	public function getSerializeHash(){
		$dateId = $this->dateId;
		unset($this->dateId);
		$md = md5(serialize($this));
		$this->dateId = $dateId;
		return $md;
	}
	
}