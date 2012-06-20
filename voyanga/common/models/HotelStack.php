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
    public $hotels;
    public $filterValues = array();
    public $searchId;
    public $fsKey;
    public static $toTop;

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
                        $this->hotels[$hotelKey] = $hotel;
                        if ($bParamsNeedInit)
                        {
                            //initializing best params
                            $bParamsNeedInit = false;
                            $this->bestPrice = $hotel->price;
                            $this->bestPriceInd = count($this->hotels) - 1;
                        }
                        if($this->bestPrice > $hotel->price)
                        {
                            $this->bestPrice = $hotel->price;
                            $this->bestPriceInd = count($this->hotels) - 1;
                        }
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
        $this->hotels[$hotelKey] = $hotel;
        $this->bestMask |= $hotel->bestMask;
    }

    public function getHotelById($id)
    {
        foreach($this->hotels as $hotel){
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
        $aHotelsStacks = array();

        foreach ($this->hotels as $hotel)
        {
            switch ($sKey)
            {
                case "price":
                    $sVal = intval($hotel->price);
                    break;
            }

            if (!isset($aHotelsStacks[$sVal]))
            {
                $aHotelsStacks[$sVal] = new HotelStack();
            }
            $aHotelsStacks[$sVal]->addHotel($hotel);
        }
        uksort($aHotelsStacks, 'HotelStack::compare_array'); //sort array by key
        reset($aHotelsStacks);
        $aEach = each($aHotelsStacks);
        return $aHotelsStacks;
    }

    public function getAsJson()
    {
        $ret = array('hotels'=>array());
        foreach($this->hotels as $hotel)
        {
            $ret['hotels'][] = $hotel->getJsonObject();
        }
        return json_encode($ret);
    }
}
