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
    public $filterValues = array();
    public $searchId;
    public $fsKey;
    public $groupKey;
    public static $toTop;
    public static $sortParam;
    public $groupDeep = 0;

    /** @var Hotel[] */
    public $_hotels;
    /**
     * @var HotelStack[]
     */
    public $hotelStacks;


    public $bestMask = 0; // bitwise mask 0b001 - Best price, 0b010 - best recommended, 0b100 best speed

    public $bestRatingInd;
    public $bestPriceInd;

    //todo: add filters here
    public function __construct($params = NULL)
    {
        if ($params)
        {
            $hotels = null;
            if($params instanceof HotelSearchResponse)
            {
                $hotels = &$params->hotels;
            }elseif($params['hotels']){
                $hotels = &$params['hotels'];
            }

            if($hotels)
            {
                $bParamsNeedInit = true;
                foreach($hotels as $hotel)
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
                    //save all hotels to db
                    /*$hotelRoomDb = new HotelRoomDb();
                    $room = $hotel->rooms[0];
                    $hotelRoomDb->setAttributes(get_object_vars($room),false);

                    $hotelRoomDb->requestId = $hotel->searchId;
                    $hotelRoomDb->resultId = $hotel->resultId;
                    $hotelRoomDb->rubPrice = intval($hotel->rubPrice);
                    $hotelRoomDb->providerKey = $hotel->providerId;
                    $hotelRoomDb->hotelId = $hotel->hotelId;
                    $hotelRoomDb->hotelName = $hotel->hotelName;
                    $hotelRoomDb->sharingBedding = $hotelRoomDb->sharingBedding ? 1 : 0;*/
                    try{
                        //if(!$hotelRoomDb->save()){
                        //    VarDumper::dump($hotelRoomDb->getErrors());
                        //}
                    }catch (CException $e){
                        VarDumper::dump($e->getMessage());
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

        if(isset($this->_hotels[$hotelKey]))
        {
            $this->_hotels[$hotelKey]->countNumbers++;
        }
        else
        {
            $this->_hotels[$hotelKey] = $hotel;
            $this->bestMask |= $hotel->bestMask;
        }
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
        $find1 = false;
        $find2 = false;
        if($this->_hotels)
        {
            //$aHotelsStacks = array();
            $this->hotelStacks = array();

            /** @var Hotel $hotel */
            foreach ($this->_hotels as $hotel)
            {

                $sVal = $hotel->getValueOfParam($sKey);

                if (!isset($this->hotelStacks[$sVal]))
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

        $this->groupDeep++;

        return $this;
    }

    public function mergeStep(){
        if($this->hotelStacks){
            if($this->groupKey == 'providerId'){

                $maxCountKey = false;
                $maxCount = false;
                $mergedStacks = array();
                foreach($this->hotelStacks as $key=>$hotelStack){
                    if( ($maxCount === false) || ( $maxCount < count($hotelStack->_hotels) ) ){
                        $maxCount = count($hotelStack->_hotels);
                        $maxCountKey = $key;
                    }
                }
                $pricesMap = array();
                /** @var $hotel Hotel */
                foreach($this->hotelStacks[$maxCountKey]->_hotels as $hotel){
                    //echo "innn loop;";
                    if(!isset($mergedStacks[$hotel->price])){
                        $newHotelStack = new HotelStack();
                        $newHotelStack->addHotel($hotel);
                        $mergedStacks[$hotel->price] = $newHotelStack;
                    }
                }

                $mCount=0;
                $ppCnt = 0;
                foreach($this->hotelStacks as $keyStack=>$hotelStack){
                    if($keyStack != $maxCountKey){
                        foreach($hotelStack->_hotels as $hotelKey=>$otherHotel)
                        {
                            $minMetrica = 0;
                            $minStackKey = false;
                            foreach($mergedStacks as $workHotelStackKey=>$workHotelStack){
                                $tmpHotel = $workHotelStack->getHotel();
                                $metrica = $tmpHotel->getMergeMetric($otherHotel);
                                //echo "metrica:".$metrica." p1:".$tmpHotel->rubPrice." p2:".$otherHotel->rubPrice." showName"."<br>";
                                if(($minStackKey === false) || $metrica < $minMetrica){
                                    $minMetrica = $metrica;
                                    $minStackKey = $workHotelStackKey;
                                }
                            }
                            //die();
                            if($minMetrica > 1000){
                                $mCount++;
                                $newHotelStack = new HotelStack();
                                $newHotelStack->addHotel($otherHotel);
                                $mergedStacks[] = $newHotelStack;
                                if($mCount >0){
                                    //VarDumper::dump($otherHotel);
                                    //VarDumper::dump($mergedStacks);
                                    //die();
                                }
                                //echo "merged!!!".$mCount;
                            }elseif($minMetrica < 0){
                                //??
                                $ppCnt++;
                                //echo "adding+1+!!!";
                                if($ppCnt >4){
                                    //VarDumper::dump($otherHotel);
                                    //VarDumper::dump($mergedStacks[$minStackKey]->getHotel());
                                    //VarDumper::dump($otherHotel);
                                    //die();
                                }
                                //$mergedStacks[$minStackKey]->addHotel($otherHotel);
                            }else{
                                //??
                                //echo "adding+2+!!!";
                                //$mergedStacks[$minStackKey]->addHotel($otherHotel);
                            }
                            //$newHotelStack = new HotelStack();
                            //$newHotelStack->addHotel($tmpHotel);
                        }
                    }
                }
                $this->groupKey = 'merged';
                $this->hotelStacks = $mergedStacks;

                //}

            }else{
                foreach($this->hotelStacks as $hotelStack){
                    $hotelStack->mergeStep();
                }
            }
        }
    }

    public function mergeStepV2(){
        if($this->_hotels){

            $mergedStacks = array();
            $needStop = false;
            $firstElem = true;

            //foreach($this->hotelStacks as $keyStack=>$hotelStack){
                foreach($this->_hotels as $hotelKey=>$otherHotel)
                {
                    if($firstElem){
                        $newHotelStack = new HotelStack();

                        $newHotelStack->addHotel($otherHotel);
                        $mergedStacks[] = $newHotelStack;
                        $firstElem = false;
                    }else{
                        $found = false;
                        foreach($mergedStacks as $workHotelStackKey=>$workHotelStack){
                            $tmpHotel = $workHotelStack->getHotel();
                            $same = $tmpHotel->getMergeMetricV2($otherHotel);
                            if($same){
                                $found = true;
                                //may be add to hotel stack...
                                $mergedStacks[$workHotelStackKey]->addHotel($otherHotel);
                                break;
                            }else{
                                if(false && $otherHotel->hotelId == '65664'){
                                    echo Hotel::$compereDesc."hotelId: {$otherHotel->hotelId}<br>";
                                    //VarDumper::dump($otherHotel);
                                    $needStop = true;

                                }
                            }
                        }
                        if(!$found){
                            $newHotelStack = new HotelStack();

                            $newHotelStack->addHotel($otherHotel);
                            $mergedStacks[] = $newHotelStack;
                        }

                    }
                }
            //}
            if($needStop){
                exit();
            }
            //$this->groupKey = 'merged';
            //foreach()
            $this->_hotels = null;
            $this->groupKey = 'merged';
            $this->hotelStacks = $mergedStacks;

                //}


        }else{
            foreach($this->hotelStacks as $hotelStack){
                $hotelStack->mergeStepV2();
            }
        }
        $this->groupDeep++;
        return $this;
    }

    public function mergeSame(){
        //$startDeep = $this->groupDeep;
        //$this->groupBy('providerId');



        $this->mergeStepV2();

        return $this;
    }

    /**
     * Function for sorting by uasort
     * @param HotelStack $a
     * @param HotelStack $b
     */
    private static function compareStacksByHotelsParams($a, $b)
    {
        if (is_object($a->getHotel()) and ($a->getHotel() instanceof Hotel))
        {
            $valA = $a->getHotel()->getValueOfParam(self::$sortParam);
        }
        else
        {
            throw new CException ('Incorrect first hotel incoming to compareness: '.VarDumper::dumpAsString($a));
        }
        if (is_object($b->getHotel()) and ($b->getHotel() instanceof Hotel))
        {
            $valB = $b->getHotel()->getValueOfParam(self::$sortParam);
        }
        else
        {
            throw new CException ('Incorrect second hotel incoming to compareness: '.VarDumper::dumpAsString($b));
        }
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
        if($sKey)
        {
            self::$sortParam = $sKey;
            if(self::$sortParam != $this->groupKey)
            {
                if($this->_hotels)
                {
                    uasort($this->_hotels,'HotelStack::compareHotelsByHotelsParams');
                }
                elseif($this->hotelStacks)
                {
                    if($deep != 0)
                    {
                        foreach($this->hotelStacks as $i=>$hotelStack)
                        {
                            $this->hotelStacks[$i]->sortBy($sKey, $deep -1);
                        }
                    }
                    //echo "sorting hotelStacks<br>";
                    try{
                        uasort($this->hotelStacks,'HotelStack::compareStacksByHotelsParams');
                    }catch (CException $e){
                        //echo "group: {$this->groupKey}";
                        print_r($this->hotelStacks);
                    }
                }
                else
                {
                    return false;
                }
            }
        }
        return $this;
    }


    public function getHotels($deep = 0)
    {
        if($this->_hotels){
            foreach($this->_hotels as $hotel){
                $hotel->groupKey = $this->groupKey;
            }
            return $this->_hotels;
        }elseif($this->hotelStacks){
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
                    //$stackHotels = $this->hotelStacks[$i]->getHotels();
                    //foreach($stackHotels as $hotel)
                    //{
                    //    $hotels[] = $hotel;
                    //    break;
                    //}
                    $hotel = $this->hotelStacks[$i]->getHotel();
                    $hotel->groupKey = $this->groupKey;
                    $hotels[] = $this->hotelStacks[$i]->getHotel();
                }
            }
            return $hotels;
        }else{
            return false;
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

    public function printStack($limit = 200)
    {
        $cnt = $limit;
        $return = array();
        if($this->_hotels){
            $return['hotels'] = array();
            foreach($this->_hotels as $hotel){
                if($cnt <= 0){
                    break;
                }
                $return['hotels'][] = $hotel->hotelId.'||'.$hotel->rubPrice;//$hotel->getJsonObject();
                $cnt--;
            }
            return $return;
        }else{
            $return['groupKey'] = $this->groupKey;
            $return['groupDeep'] = $this->groupDeep;
            $return['hotelStack'] = array();
            foreach($this->hotelStacks as $key=>$hotelStack)
            {
                if($cnt <= 0){
                    break;
                }
                $return['hotelStack'][$key] = $hotelStack->printStack($limit);
                $cnt--;
            }
            return $return;
        }
    }

    public function saveHotelDb(){
        $hotels = $this->getJsonObject(0);

        if($hotels){
            $city = City::getCityByPk($hotels['hotels'][0]['cityId']);
            $hotelNames = array();
            foreach($hotels['hotels'] as $hotelInfo){
                $hotelNames[$hotelInfo['hotelName']] = $hotelInfo['hotelName'];
            }
            $ratingNames = HotelRating::model()
                ->findByNames($hotelNames, $city);
            foreach($hotels['hotels'] as $hotelInfo){
                $hotelDb = HotelDb::lazySaveHotelDb(
                    array(
                        'id'=>$hotelInfo['hotelId'],
                        'name'=>$hotelInfo['hotelName'],
                        'stars'=>$hotelInfo['categoryId'],
                        'cityId'=>$city->id,
                        'countryId'=>$city->countryId,
                        'rating'=>(isset($ratingNames[$hotelInfo['hotelName']]) ? $ratingNames[$hotelInfo['hotelName']] : null),
                        'minPrice'=>$hotelInfo['rubPrice'],
                    )
                );
            }
            HotelDb::lazySave();

        }
    }

    /**
     * @param $indexes 'ind1,ind2,ind3' example : '4,10,50,77'
     */
    public function deleteStackWithIndex($indexes)
    {
        $arrIndexes = explode(',',$indexes);
        $findInd = $arrIndexes[0];
        if(count($arrIndexes)>1){
            if(isset($this->hotelStacks[$findInd])){
                unset($arrIndexes[0]);

                $this->hotelStacks[$findInd]->deleteStackWithIndex(join(',',$arrIndexes));
                if(!$this->hotelStacks[$findInd]->hotelStacks and !$this->hotelStacks[$findInd]->_hotels){
                    unset($this->hotelStacks[$findInd]);
                }
            }
        }else{
            if(isset($this->hotelStacks[$findInd])){
                unset($this->hotelStacks[$findInd]);
            }
        }
    }
}
