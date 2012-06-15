<?php

class Hotel extends CApplicationComponent
{
    /*
     * 'resultId' => '50475'
                    'hotelId' => '57'
                    'hotelName' => 'HOLIDAY INN LESNAYA'
                    'hotelCatId' => '3'
                    'hotelCatName' => '4*'
                    'hotelAddress' => '15 LESNAYA STREET
MOSCOW
RUSSIA'
                    'hotelPhotoUrl' => 'http://test.hotelbook.vsespo.ru/photos/118/118/4/57/1868b.jpg'
                    'hotelSmallPhotoUrl' => 'http://test.hotelbook.vsespo.ru/photos/118/118/4/57/1868s.jpg'
                    'hotelLatitude' => '55.779354095458984'
                    'hotelLongitude' => '37.588970184326172'
                    'confirmation' => 'online'
                    'price' => '3243.82'
                    'currency' => 'EUR'
                    'comparePrice' => '134966.34'
                    'information' => ''
                    'visaMsk' => 'false'
                    'visaSpb' => 'false'
                    'specialOffer' => 'false'
                    'providerId' => '2'
                    'providerHotelCode' => 'HOL1/MOW'
                )
                [Rooms] => SimpleXMLElement#36
                (
                    [Room] => SimpleXMLElement#37
                    (
                        [@attributes] => array
                        (
                            'roomSizeId' => '2'
                            'roomSizeName' => 'DBL'
                            'roomTypeId' => '1'
                            'roomTypeName' => 'STD'
                            'roomViewId' => '1'
                            'roomViewName' => 'ROH'
                            'roomNumber' => '1'
                            'mealId' => '2'
                            'mealName' => 'Завтрак'
                            'mealBreakfastId' => '25'
                            'mealBreakfastName' => ''
                            'child' => '1'
                            'cots' => '0'
                            'sharingBedding' => 'false'
                        )
                        [ChildAge] => '6'
                    )
                )
                [Locations] => SimpleXMLElement#38
                (
                    [Location] => SimpleXMLElement#39
                    (
                        [@attributes] => array
                        (
                            'id' => '1'
                            'name' => 'Central'
                        )
                    )
                )
     */
    public static $categories = array(1=>'5*',2=>'3*',3=>'4*',4=>'2*',5=>'1*',6=>'-');
    public $searchId;
    public $hotelId;
    public $resultId;
    public $categoryId;
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

}