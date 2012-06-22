<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 15.06.12
 * Time: 11:21
 * To change this template use File | Settings | File Templates.
 */
class HotelStack
{
    public $_hotels;
    /**
     * @var HotelStack[]
     */
    public $hotelStacks;
    public $filterValues = array();
    public $searchId;
    public $fsKey;
    public $groupKey;
    public static $toTop;
    public static $sortParam;


    public $bestMask = 0; // bitwise mask 0b001 - Best price, 0b010 - best recommended, 0b100 best speed

    public $bestRatingInd;
    public $bestPriceInd;

    public function __construct($params = NULL)
    {
        if ($params)
        {
            if($params['hotels'])
            {
                $bParamsNeedInit = true;
                foreach($params['hotels'] as $hotel)
                {
                    $bNeedSave = TRUE;
                    //TODO: check filters and save only needed hotels
                    if($bNeedSave)
                    {
                        $hotelKey = $hotel->key;
                        $this->_hotels[$hotelKey] = $hotel;
                        if ($bParamsNeedInit)
                        {
                            //initializing best params
                            $bParamsNeedInit = false;
                            $this->bestPrice = $hotel->price;
                            $this->bestPriceInd = count($this->_hotels) - 1;
                        }
                        if($this->bestPrice > $hotel->price)
                        {
                            $this->bestPrice = $hotel->price;
                            $this->bestPriceInd = count($this->_hotels) - 1;
                        }
                    }
                }

                foreach ($this->_hotels as $iInd => $hotel)
                {
                    if($this->_hotels[$iInd]->price == $this->bestPrice)
                    {
                        $this->_hotels[$iInd]->bestMask |= 1;
                    }
                }

            }
        }
    }

    /**
     * addHotel
     * Add Hotel object to this HotelStack
     * @param Hotel $hotel
     */
    public function addHotel(Hotel $hotel)
    {
        $hotelKey = $hotel->key;
        $this->_hotels[$hotelKey] = $hotel;
        $this->bestMask |= $hotel->bestMask;
    }

    public function getHotelById($id)
    {
        foreach($this->_hotels as $hotel){
            if($hotel->hotelKey == $id){
                return $hotel;
            }
        }
        return false;
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
        } elseif ($a > $b)
        {
            return 1;
        }

        return 0;
    }

    public function groupBy($sKey, $iToTop = NULL)
    {
        if($this->_hotels)
        {
            //$aHotelsStacks = array();
            $this->hotelStacks = array();

            /** @var Hotel $hotel */
            foreach ($this->_hotels as $hotel)
            {

                $sVal = $hotel->getValueOfParam($sKey);

                if (!isset($aHotelsStacks[$sVal]))
                {
                    $this->hotelStacks[$sVal] = new HotelStack();
                }
                $this->hotelStacks[$sVal]->addHotel($hotel);
            }
            $this->_hotels = null;
            uksort($this->hotelStacks, 'HotelStack::compare_array'); //sort array by key
            reset($this->hotelStacks);
            $this->groupKey = $sKey;
            //$aEach = each($aHotelsStacks);
        }elseif($this->hotelStacks){
            foreach ($this->hotelStacks as $i=>$hotelStack)
            {
                $this->hotelStacks[$i]->groupBy($sKey, $iToTop);
            }
        }

        return $this;
    }

    /**
     * Function for sorting by uasort
     * @param HotelStack $a
     * @param HotelStack $b
     */
    private static function compareStacksByHotelsParams($a, $b)
    {

        $valA = $a->getHotel()->getValueOfParam(self::$sortParam);
        $valB = $b->getHotel()->getValueOfParam(self::$sortParam);
        //echo "Comparing ".$a->getHotel()->hotelId.' vs '.$b->getHotel()->hotelId." values: $valA vs $valB<br>";
        if ($valA < $valB)
        {
            return -1;
        } elseif ($valA > $valB)
        {
            return 1;
        }

        return 0;
    }

    /**
     * Function for sorting by uasort
     * @param Hotel $a
     * @param Hotel $b
     */
    private static function compareHotelsByHotelsParams($a, $b)
    {
        $valA = $a->getValueOfParam(self::$sortParam);
        $valB = $b->getValueOfParam(self::$sortParam);
        if ($valA < $valB)
        {
            return -1;
        } elseif ($valA > $valB)
        {
            return 1;
        }

        return 0;
    }

    public function sortBy($sKey = '',$deep = 0)
    {
        if($sKey) self::$sortParam = $sKey;
        if(self::$sortParam != $this->groupKey)
        {
            if($this->_hotels)
            {
                uasort($this->_hotels,'HotelStack::compareHotelsByHotelsParams');
            }
            else
            {
                if($deep != 0)
                {
                    foreach($this->hotelStacks as $i=>$hotelStack)
                    {
                        $this->hotelStacks[$i]->sortBy('', $deep -1);
                    }
                }
                //echo "sorting hotelStacks<br>";
                uasort($this->hotelStacks,'HotelStack::compareStacksByHotelsParams');
            }
        }
        return $this;
    }


    public function getHotels($deep = 0)
    {
        if($this->_hotels){
            return $this->_hotels;
        }else{
            $hotels = array();
            foreach ($this->hotelStacks as $i=>$hotelStack)
            {
                if($deep){
                    $stackHotels = $this->hotelStacks[$i]->getHotels($deep - 1);
                    foreach($stackHotels as $hotel)
                    {
                        $hotels[] = $hotel;
                    }
                }
                else
                {
                    $stackHotels = $this->hotelStacks[$i]->getHotels();
                    foreach($stackHotels as $hotel)
                    {
                        $hotels[] = $hotel;
                        break;
                    }
                }
            }
            return $hotels;
        }
    }

    /**
     * Function for get first Hotel from HotelStack
     * @return Hotel
     */
    public function getHotel(){
        if($this->_hotels){
            foreach($this->_hotels as $hotel)
            {
                return $hotel;
            }
        }
        else
        {
            foreach($this->hotelStacks as $hotelStack)
            {
                return $hotelStack->getHotel();
            }
        }
    }

    public function getAsJson($deep = 0)
    {
        return json_encode($this->getJsonObject($deep));
    }

    public function getJsonObject($deep = 0)
    {
        $ret = array('hotels'=>array());
        $hotels = $this->getHotels($deep);
        foreach($hotels as $hotel)
        {
            $ret['hotels'][] = $hotel->getJsonObject();
        }
        return $ret;
    }

    public function printStack()
    {
        foreach($this->hotelStacks as $key=>$hotelStack)
        {
            echo "key: $key <br>";
        }
    }
}
