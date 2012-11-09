<?php

class Hotel extends CApplicationComponent
{
    //type for saving to basket
    const TYPE = 2;

    const STARS_ONE = 1;
    const STARS_TWO = 2;
    const STARS_THREE = 3;
    const STARS_FOUR = 4;
    const STARS_FIVE = 5;
    const STARS_UNDEFINDED = 0;

    public static $categoryIdHotelbook = array(1=>'5*',2=>'3*',3=>'4*',4=>'2*',5=>'1*',6=>'-');
    public static $categoryIdMapHotelbook = array(6=>0,5=>1,4=>2,2=>3,3=>4,1=>5);

    /** @var string hotelBook search identifier */
    public $searchId;

    /** @var int unique hotel id */
    public $hotelId;

    /** @var string hotel name */
    public $hotelName;

    /** @var string one hotel search result among whole search request */
    public $resultId;

    /** @var int one of STARS_* - star rating of hotel */
    public $categoryId;

    /** @var string date of hotel check in (should be 'Y-m-d') */
    public $checkIn;

    /** @var int */
    public $cityId;

    /** @var int count of nights inside hotel */
    public $duration;

    /** @var string human readable star rating */
    public $categoryName;

    /** @var string hotel address */
    public $address;

    /** @var string type of confirmation. We use only online confirmation now. */
    public $confirmation;

    /** @var float whole cost in local currency */
    public $price;

    /** @var string local hotel currency */
    public $currency;

    /** @var float cost of whole booking into RUR */
    public $rubPrice;

    /** @var int amount of apartment with same type */
    public $countNumbers = 1;

    /** @var float cost of whole booking into RUR */
    public $comparePrice;

    public $markupPrice;

    /** @var int is it special offer */
    public $specialOffer;

    /** @var string internal provider of that hotel */
    public $providerId;

    /** @var int default distance from city center */
    public $centerDistance  = PHP_INT_MAX;

    /** @var float hotel latitude */
    public $latitude;

    /** @var float hotel longtitude */
    public $longitude;

    /** @var string internal hotel code unique for each hotel provider */
    public $providerHotelCode;

    //todo: convert it to class
    /** @var array charges that we get cancelling hotel*/
    public $cancelCharges;

    /** @var int timestamp when first charge applied */
    public $cancelExpiration;

    /** @var int bitmask for hotel (1st bit for the best price) */
    public $bestMask = 0;

    /** @var HotelRoom[] */
    public $rooms;

    /** @var where do we get if from */
    public $cacheId;

    /** @var hotel rating */
    private $_rating;

    /** @var City */
    private $_city;

    private $internalId;

    //implementation of ICartPosition
    public function getId()
    {
        return 'hotel_key'.$this->cacheId.'_'.$this->searchId.'_'.$this->resultId;
    }

    /**
     * @return float price
     */
    public function getPrice()
    {
        return $this->markupPrice;
    }

    /**
     * @return float price
     */
    public function getOriginalPrice()
    {
        return $this->rubPrice;
    }

    //implementation of IOrderElement
    public function getIsValid()
    {
        $request = new HotelBookClient();
        return $request->checkHotel($this);
    }

    public function getIsPayable()
    {
        return true;
    }

    public function saveToOrderDb()
    {
        $key = $this->getId();
        $order = OrderHotel::model()->findByAttributes(array('key' => $key));
        if (!$order)
            $order = new OrderHotel();
        $order->key = $key;
        $order->checkIn = $this->checkIn;
        $order->duration = $this->duration;
        $city = City::model()->getCityByHotelbookId($this->cityId);
        $order->cityId = $city->id;
        $order->object = serialize($this);

        if ($order->save())
        {
            $this->internalId = $order->id;
            return $order;
        }
        return false;
    }

    public function saveReference($order)
    {
        $orderHasHotel = OrderHasHotel::model()->findByAttributes(array('orderId'=>$order->id, 'orderHotel'=>$this->internalId));
        if (!$orderHasHotel)
        {
            $orderHasHotel = new OrderHasHotel();
            $orderHasHotel->orderId = $order->id;
            $orderHasHotel->orderHotel = $this->internalId;
            $orderHasHotel->save();
            if (!$orderHasHotel->save())
                throw new CException(VarDumper::dumpAsString($orderHasHotel->errors));
        }
    }

    public static function getFromCache($cacheId, $hotelId, $resultId)
    {
        $request = Yii::app()->cache->get('hotelResult'.$cacheId);
        $foundHotel = false;
        if (!isset($request['hotels']))
            return false;
        foreach ($request['hotels'] as $unique=>$hotel)
        {
            if ($hotel->resultId==$resultId)
                $foundHotel = $hotel;
        }
        return $foundHotel;
    }

    public function __construct($params)
    {
        $attrs = get_object_vars($this);
        $exclude = array('rooms', 'categoryId');
        foreach($attrs as $attrName=>$attrVal)
        {
            if(!in_array($attrName, $exclude))
            {
                if(isset($params[$attrName])){
                    $this->{$attrName} = $params[$attrName];
                }
            }
        }
        if(isset($params['rooms']))
        {
            foreach($params['rooms'] as $roomParams)
            {
                $this->rooms[] = new HotelRoom($roomParams);
            }
        }
        if(isset($params['categoryId']))
        {
            $this->categoryId = isset(self::$categoryIdMapHotelbook[intval($params['categoryId'])]) ? self::$categoryIdMapHotelbook[intval($params['categoryId'])]  : self::STARS_UNDEFINDED;
        }
        if(isset($params['rating']))
        {
            $this->_rating = $params['rating'];
        }
    }

    public function addRoom($room)
    {
        if($room instanceof HotelRoom){
            $this->rooms[] = $room;
        }else{
            $hotelRoom = new HotelRoom($room);
            if($hotelRoom){
                $this->rooms[] = $hotelRoom;
            }
        }
    }

    public function addCancelCharge($cancelParams)
    {
        $params = array();
        $params['price'] = isset($cancelParams['price']) ? $cancelParams['price'] * ($this->rubPrice / $this->price) : 0;
        if(isset($cancelParams['from']))
        {
            $time = strtotime($cancelParams['from']);
            $params['fromTimestamp'] = $time;
            $params['charge'] = $cancelParams['charge'] == 'false' ? false : true;
            $params['denyChanges'] = $cancelParams['denyChanges'] == 'false' ? false : true;
            if($params['charge'] == true)
            {
                if(!$this->cancelExpiration){
                    $this->cancelExpiration = strtotime($this->checkIn);
                }
                if($this->cancelExpiration > $params['fromTimestamp'])
                {
                    $this->cancelExpiration = $params['fromTimestamp'];
                }
                $this->cancelCharges[] = $params;
            }
        }elseif(!$this->cancelCharges){

            $params['fromTimestamp'] = time();
            $params['charge'] = $cancelParams['charge'] == 'false' ? false : true;
            $params['denyChanges'] = $cancelParams['denyChanges'] == 'false' ? false : true;
            if($params['charge'] == true)
            {
                if(!$this->cancelExpiration){
                    $this->cancelExpiration = strtotime($this->checkIn);
                }
                if($this->cancelExpiration > $params['fromTimestamp'])
                {
                    $this->cancelExpiration = $params['fromTimestamp'];
                }
                $this->cancelCharges[] = $params;
            }
        }
    }

    public function getKey()
    {
        $sKey = $this->hotelId.'|'.$this->categoryId.'|'.$this->price.$this->currency.'|'.$this->providerId;
        foreach($this->rooms as $room)
            $sKey .= '|'.$room->key;

        return md5($sKey);
    }

    public function getRoomNames()
    {
        $roomNames = array();
        //foreach($this->rooms as $room)
        //    $sKey .= '|'.$room->key;

        //return md5($sKey);
    }

    public function getValueOfParam($paramName)
    {
        switch ($paramName)
        {
            case "price":
                $sVal = intval($this->price);
                break;
            case "hotelId":
                $sVal = intval($this->hotelId);
                break;
            case "categoryId":
                $sVal = intval($this->categoryId);
                break;
            case "providerId":
                $sVal = intval($this->providerId);
                break;
            case "rubPrice":
                $sVal = intval($this->rubPrice);
                break;
            case "roomSizeId":
                $sVal = intval($this->getRoomsAttributeForSort('sizeId'));
                break;
            case "roomTypeId":
                $sVal = intval($this->getRoomsAttributeForSort('typeId'));
                break;
            case "roomViewId":
                $sVal = intval($this->getRoomsAttributeForSort('viewId'));
                break;
            case "roomMealId":
                $sVal = intval($this->getRoomsAttributeForSort('mealId'));
                break;
            case "roomShowName":
                $sVal = $this->getRoomsAttributeForSort('showName');
                break;
            case "centerDistance":
                $sVal = intval($this->centerDistance);
                break;
        }
        return $sVal;
    }

    /**
     * Function need for sorting hotels by room attributes
     * @param $attrName
     */
    public function getRoomsAttributeForSort($attrName)
    {
        $ret = '';
        foreach($this->rooms as $room)
        {
            $ret .= $room->{$attrName}.'0';
        }
        return $ret;
    }

    public function getJsonObject()
    {
        /*
        public $searchId;
        public $hotelId;
        public $resultId;
        public $categoryId;
        public $checkIn;
        public $duration;
        public $categoryName;
        public $address;
        public $confirmation;
        public $price;
        public $currency;
        public $rubPrice;
        public $comparePrice;
        public $specialOffer;
        public $providerId;
        public $providerHotelCode;
        public $cancelCharges;
        public $cancelExpiration;
        */
        $ret = array(
            'hotelId' => $this->hotelId,
            'hotelName' => $this->hotelName,
            'searchId'=>$this->searchId,
            'resultId'=>$this->resultId,
            'countNumbers'=>$this->countNumbers,
            'category'=>$this->categoryName,
            'price' => $this->price,
            'centerDistance' => $this->centerDistance,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'currency' => $this->currency,
            'rubPrice' => $this->getPrice(),
            'discountPrice' => $this->rubPrice,
            'bestMask' => $this->bestMask,
            'categoryId' => $this->categoryId,
            'checkIn' => $this->checkIn,
            'checkOut' => $this->getCheckOut(),
            'duration' => $this->duration,
            'city' => ($city = City::model()->getCityByHotelbookId($this->cityId)) ? $city->localRu : '',
            'rating' => $this->rating,
            'rooms' => array()
        );

        foreach ($this->rooms as $room)
        {
            $ret['rooms'][] = $room->getJsonObject();
        }
        return $ret;
    }

    public function getCheckOut()
    {
        if (!$this->checkIn)
            return null;
        if (!$this->duration)
            return null;
        $checkInInternal = DateTime::createFromFormat('Y-m-d', $this->checkIn);
        $checkOutInternal = $checkInInternal->add(new DateInterval('P'.$this->duration.'D'));
        return $checkOutInternal->format('Y-m-d');
    }

    /**
     * @return float user rating
     */
    function getRating()
    {
        return $this->_rating?$this->_rating:'-';
    }

    function setRating($val)
    {
        $this->_rating = $val;
    }

    /**
     * Return array of passports. If no passport needs so it should return false. If we need passports but they not provided return an empty array.
     * Array = array of classes derived from BasePassportForm (e.g. BaseFlightPassportForm)
     *
     * @return HotelPassportForm
     */
    public function getPassports()
    {
    }

    public function getTime()
    {
        return strtotime($this->checkIn);
    }

    public function getWeight()
    {
        return 2;
    }

    public function getMergeMetric(Hotel $otherHotel)
    {
        $metrica = 0;
        $coef = ($this->rubPrice / $otherHotel->rubPrice);
        if($coef < 1) $coef = 1/$coef;
        $metrica += $coef * 945;
        //echo "firstValue: {$metrica}";
        foreach($this->rooms as $roomKey=>$thisRoom){
            if(isset($otherHotel->rooms[$roomKey])){
                $otherRoom = $otherHotel->rooms[$roomKey];
                if($thisRoom->showName != $otherRoom->showName){
                    $metrica+=3000;
                    //echo "others name";
                }else{
                    $metrica= $metrica/1.1;
                }
                //if()
                if($thisRoom->sizeName != $otherRoom->sizeName){
                    $metrica+=200;
                }else{
                    $metrica= $metrica/1.1;
                }
                if($thisRoom->typeName != $otherRoom->typeName){
                    $metrica+=200;
                }else{
                    $metrica= $metrica/1.1;
                }
                if($thisRoom->mealName != $otherRoom->mealName){
                    $metrica+=200;
                }else{
                    $metrica= $metrica/1.1;
                }
                if($thisRoom->viewName != $otherRoom->viewName){
                    $metrica+=200;
                }else{
                    $metrica= $metrica/1.1;
                }
            }

        }

        return $metrica;
    }

    /**
       Returns City object for given hotel
       @return City
     */
    public function getCity()
    {
        if (!$this->_city)
        {
            $this->_city = City::getCityByHotelbookId($this->cityId);
            if (!$this->_city) throw new CException(Yii::t('application', 'Hotel city not found. City with hotelbookId {city_id} not set in db.', array(
                '{city_id}' => $this->cityId)));
        }
        return $this->_city;
    }
}
