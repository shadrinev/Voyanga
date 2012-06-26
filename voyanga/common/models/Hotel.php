<?php

class Hotel extends CApplicationComponent
{
    public static $categories = array(1=>'5*',2=>'3*',3=>'4*',4=>'2*',5=>'1*',6=>'-');
    public $searchId;
    public $hotelId;
    public $hotelName;
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
    public $centerDistance  = PHP_INT_MAX;
    public $latitude;
    public $longitude;
    public $providerHotelCode;
    public $cancelCharges;
    public $cancelExpiration;
    public $bestMask = 0;


    public $rooms;

    public function __construct($params)
    {
        $attrs = get_object_vars($this);
        foreach($attrs as $attrName=>$attrVal){
            if($attrName !== 'rooms')
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
        $params['price'] = isset($cancelParams['price']) ? $cancelParams['price'] : 0;
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
        }
    }

    public function getKey()
    {
        $sKey = $this->hotelId.'|'.$this->categoryId.'|'.$this->price.$this->currency.'|'.$this->providerId;
        foreach($this->rooms as $room)
            $sKey .= '|'.$room->key;

        return md5($sKey);
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
        $ret = array('hotelId' => $this->hotelId,
            'hotelName' => $this->hotelName,
            'searchId'=>$this->searchId,
            'resultId'=>$this->resultId,
            'category'=>$this->categoryName,
            'price' => $this->price,
            'centerDistance' => $this->centerDistance,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'currency' => $this->currency,
            'rubPrice' => $this->rubPrice,
            'bestMask' => $this->bestMask,
            'rooms' => array()
        );

        foreach ($this->rooms as $room)
        {
            $ret['rooms'][] = $room->getJsonObject();
        }
        return $ret;
    }

}