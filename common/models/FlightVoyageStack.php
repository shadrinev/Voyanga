<?php
/**
 * FlightVoyageStack class
 * Class with array of FlightVoyage
 * @author oleg
 *
 */
class FlightVoyageStack
{
    public $flightVoyages = array();
    //public $flightVoyages;
    public $filterValues = array();
    public $searchId;
    public $fsKey;
    public static $toTop;

    public $bestMask = 0; // bitwise mask 0b001 - Best price, 0b010 - best recommended, 0b100 best speed

    public $bestPriceTimeInd;
    public $bestTimeInd;
    public $bestPriceInd;


    public function __construct($params = NULL, $dontStackResults = false)
    {
        if ($params)
        {
            $this->airportsFrom = array(
                array(),
                array()
            );
            $this->airportsTo = array(
                array(),
                array()
            );
            $this->timePeriodFrom = array(
                array(),
                array()
            );
            $this->timePeriodTo = array(
                array(),
                array()
            );
            $this->airlines = array();
            $this->transits = array();
            $this->bestTime = 0;
            $this->bestPrice = 0;
            $this->bestPriceTime = 0;
            $this->searchId = $params['searchId'];
            $this->fsKey = $params['fsKey'];

            //todo: refactor here
            if ($params['flights'])
            {
                $bParamsNeedInit = true;
                $flightsCodes = array();
                foreach ($params['flights'] as $oFlightParams)
                {
                    $oFlightVoyage = new FlightVoyage($oFlightParams);
                    if (!FlightManager::isIncluded($oFlightVoyage))
                        continue;
                    $bNeedSave = TRUE;

                    $priceInfo = FlightPricing::getPriceInfo($oFlightVoyage);
                    //$oFlightVoyage->setPriceInfo($priceInfo);

                    $flightCodes = implode(',', $oFlightVoyage->getFlightCodes());
                    //If already have same flight select cheaper flight
                    if (isset($flightsCodes[$flightCodes])&&!$dontStackResults)
                    {
                        //exclude current voyage because it already saved
                        $bNeedSave = FALSE;

                        $oldPriceInfo = FlightPricing::getPriceInfo($this->flightVoyages[$flightsCodes[$flightCodes]]);
                        if ($priceInfo['fullPrice'] < $oldPriceInfo['fullPrice'])
                        {
                            //$oFlightVoyage->setPriceInfo($priceInfo);
                            //replace already saved flight voyage
                            $this->flightVoyages[$flightsCodes[$flightCodes]] = $oFlightVoyage;
                        }

                        # When price is equal prefer galileo
                        if ($priceInfo['fullPrice'] == $oldPriceInfo['fullPrice']) {
                            if($oFlightVoyage->webService == 'GALILEO') {
                                $this->flightVoyages[$flightsCodes[$flightCodes]] = $oFlightVoyage;
                            }
                        }
                    }

                    if ($bNeedSave)
                    {
                        //If Voyage don't filtered, add to stack
                        $this->flightVoyages[] = $oFlightVoyage;
                        $flightsCodes[$flightCodes] = count($this->flightVoyages) - 1;
                        $iFullDuration = $oFlightVoyage->getFullDuration();
                        if ($bParamsNeedInit)
                        {
                            //initializing best params
                            $bParamsNeedInit = false;
                            $this->bestPrice = $oFlightVoyage->price;
                            $this->bestTime = $iFullDuration;
                            $this->bestTimeInd = count($this->flightVoyages) - 1;
                            $this->bestPriceInd = $this->bestTimeInd;
                        }
                        if ($this->bestPrice > $oFlightVoyage->price)
                        {
                            //update best price params 
                            $this->bestPrice = $oFlightVoyage->price;
                            $this->bestPriceInd = count($this->flightVoyages) - 1;
                        }
                        if ($this->bestTime > $iFullDuration)
                        {
                            //update best time params
                            $this->bestTime = $iFullDuration;
                            $this->bestTimeInd = count($this->flightVoyages) - 1;
                        }
                    }
                }

                $bParamsNeedInit = true;
                //todo: is it correct?
                $this->bestPriceTimeInd = 0;
                //find best pricetime params
                foreach ($this->flightVoyages as $iInd => $oFlightVoyage)
                {
                    $iFullDuration = $oFlightVoyage->getFullDuration();
                    $iParamsFactor = intval(($oFlightVoyage->price / $this->bestPrice) * Yii::app()->params['flight_price_factor']) + intval(($iFullDuration / $this->bestTime) * Yii::app()->params['flight_time_factor']);

                    if ($bParamsNeedInit)
                    {
                        $bParamsNeedInit = false;
                        $this->bestPriceTime = $iParamsFactor;
                    }
                    if ($this->bestPriceTime > $iParamsFactor)
                    {
                        $this->bestPriceTime = $iParamsFactor;
                        $this->bestPriceTimeInd = $iInd;
                    }
                }

                $this->flightVoyages[$this->bestPriceInd]->bestMask |= FlightVoyage::MASK_BEST_PRICE;
                $this->flightVoyages[$this->bestTimeInd]->bestMask |= FlightVoyage::MASK_BEST_TIME;
                $this->flightVoyages[$this->bestPriceTimeInd]->bestMask |= FlightVoyage::MASK_BEST_PRICETIME;
            }
        }
    }

    /**
     * addFlightVoyage
     * Add FlightVoyage object to this FlightVoyageStack
     * @param FlightVoyage $oFlightVoyage
     */
    public function addFlightVoyage(FlightVoyage $oFlightVoyage)
    {
        $this->flightVoyages[] = $oFlightVoyage;
        $this->bestMask |= $oFlightVoyage->bestMask;
    }

    public function getFlightById($id)
    {
        foreach ($this->flightVoyages as $flightVoyage)
        {
            if ($flightVoyage->flightKey == $id)
            {
                return $flightVoyage;
            }
        }
        return false;
    }

    public function setAttributes($values)
    {
        foreach ($values as $name => $value)
        {
            $this->$name = $value;
        }
    }

    /**
     * Function for sorting by uksort
     * @param $a
     * @param $b
     */
    private static function compare_array($a, $b)
    {
        if ($a < $b)
        {
            return -1;
        }
        elseif ($a > $b)
        {
            return 1;
        }

        return 0;
    }

    /**
     * groypBy method
     * Group internal FlightVoyage elements, and return array of FlightVoyageStack elements
     * @param string $sKey - name key for grouping
     * @param integer $iToTop - push to top group with this value
     * @param integer $iFlightIndex - index of Flight in FlightVoyage
     * @return array of FlightVoyageStack
     */
    public function groupBy($sKey, $iToTop = NULL, $iFlightIndex = FALSE)
    {
        $aVariantsStacks = array();

        foreach ($this->flightVoyages as $oFlightVoyage)
        {
            switch ($sKey)
            {
                case "price":
                    $sVal = intval($oFlightVoyage->price);
                    break;
            }

            if (!isset($aVariantsStacks[$sVal]))
            {
                $aVariantsStacks[$sVal] = new FlightVoyageStack();

            }
            $aVariantsStacks[$sVal]->addFlightVoyage($oFlightVoyage);
        }
        uksort($aVariantsStacks, 'FlightVoyageStack::compare_array'); //sort array by key
        reset($aVariantsStacks);
        $aEach = each($aVariantsStacks);
        return $aVariantsStacks;
    }

    public function getAsJson($inject=array())
    {
        $ret = array('searchId' => $this->fsKey, 'flightVoyages' => array());
        foreach ($this->flightVoyages as $flightVoyage)
        {
            $res = $flightVoyage->getJsonObject();
            $ret['flightVoyages'][] = $res;
        }
        $ret = CMap::mergeArray($ret, $inject);
        return json_encode($ret);
    }

    public function getJsonObject()
    {
        $ret = array('searchId' => $this->fsKey, 'flightVoyages' => array());
        foreach ($this->flightVoyages as $flightVoyage)
        {
            $res = $flightVoyage->getJsonObject();
            $ret['flightVoyages'][] = $res;
        }
        return $ret;
    }
}