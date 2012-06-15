<?php
class HotelBookClient
{
    public $differenceTimestamp = 0;
    public $isSynchronized = false;
    public $lastHeaders;

    public function request($url, $getData = null, $postData = null)
    {
        $rCh = curl_init();

        if($postData)
        {
            curl_setopt($rCh, CURLOPT_POST, (true));
        }
        curl_setopt($rCh, CURLOPT_HEADER, true);
        curl_setopt($rCh, CURLOPT_RETURNTRANSFER, true);
        if($postData)
        {
            curl_setopt($rCh, CURLOPT_POSTFIELDS, $postData);
        }
        curl_setopt($rCh, CURLOPT_TIMEOUT, 190);
        //$aHeadersToSend = array();
        //$aHeadersToSend[] = "Content-Length: " . strlen($sRequest);
        //$aHeadersToSend[] = "Content-Type: text/xml; charset=utf-8";
        //$aHeadersToSend[] = "SOAPAction: \"$sAction\"";

        //curl_setopt($rCh, CURLOPT_HTTPHEADER, $aHeadersToSend);

        //evaluate get parametrs
        if($getData)
        {
            $pos = strpos($url,'?');
            if($pos !== false)
            {
                list($url,$args) = explode("?", $url, 2);
                parse_str($args,$params);
                $getData = array_merge($params,$getData);
            }

            $url = $url.'?'.http_build_query($getData);
        }


        curl_setopt($rCh, CURLOPT_URL, $url);
        $sData = curl_exec($rCh);
        //Biletoid_Utils::addLogMessage($sData, '/tmp/curl_response.txt');
        if ($sData !== FALSE)
        {
            list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
            if (strpos($sHeaders, 'Continue') !== FALSE)
            {
                list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
            }
            $this->lastHeaders = $sHeaders;
        }
        else
        {
            $this->lastCurlError = curl_error ($rCh);
        }

        return $sData;
    }

    private function getChecksum($time)
    {
        return md5(md5(Yii::app()->params['HotelBook']['password']).$time);
    }

    public function getCountries()
    {
        $this->synchronize();
        echo "iNN";
        $time = time() + $this->differenceTimestamp;
        $getData = array('login'=>Yii::app()->params['HotelBook']['login'],'time'=>$time,'checksum'=>$this->getChecksum($time));
        $countries = $this->request(Yii::app()->params['HotelBook']['uri'].'countries',$getData);
        $countriesObject = simplexml_load_string($countries);
        $return = array();
        foreach($countriesObject->Countries->Country as $country)
        {
            $id = intval($country['id']);
            $name = trim((string)$country);
            $return[$id] = array('id'=>$id,'nameRu'=>$name);
        }
        $getData['language']= 'en';
        $countries = $this->request(Yii::app()->params['HotelBook']['uri'].'countries',$getData);
        $countriesObject = simplexml_load_string($countries);
        foreach($countriesObject->Countries->Country as $country)
        {
            $id = intval($country['id']);
            $name = trim((string)$country);
            $return[$id]['nameEn'] = $name;
        }
        //print_r($return);
        return $return;
    }

    public function getCities($countryId = 0)
    {
        $this->synchronize();
        $time = time() + $this->differenceTimestamp;
        $getData = array('login'=>Yii::app()->params['HotelBook']['login'],'time'=>$time,'checksum'=>$this->getChecksum($time));
        if($countryId)
        {
            $getData['country_id'] = $countryId;
        }
        $cities = $this->request(Yii::app()->params['HotelBook']['uri'].'cities',$getData);
        $citiesObject = simplexml_load_string($cities);
        $return = array();
        foreach($citiesObject->Cities->City as $city)
        {
            $id = intval($city['id']);
            $name = trim((string)$city);
            $country_id = intval($city['country']);
            $return[$id] = array('id'=>$id,'nameEn'=>$name,'countryId'=>$country_id);
        }
       /* $getData['language']= 'en';
        $cities = $this->request(Yii::app()->params['HotelBook']['uri'].'cities',$getData);
        $citiesObject = simplexml_load_string($cities);
        foreach($citiesObject->Cities->City as $city)
        {
            $id = intval($city['id']);
            $name = trim((string)$city);
            $return[$id]['nameEn'] = $name;
        }*/
        //print_r($return);
        return $return;
    }

    private function getHotelFromSXE($hotelSXE)
    {
        $hotelAttrMap = array(
            'hotelId','resultId','confirmation','price','currency','comparePrice','specialOffer','providerId','providerHotelCode',
            'categoryId'=>'hotelCatId',
            'categoryName'=>'hotelCatName',
            'address'=>'hotelAddress',
            'latitude'=>'hotelLatitude',
            'longitude'=>'hotelLongitude',
            'rubPrice'=>'comparePrice'
        );
        $roomAttrMap = array(
            'mealId','mealName','mealBreakfastId','mealBreakfastName','sharingBedding',
            'sizeId'=>'roomSizeId',
            'sizeName'=>'roomSizeName',
            'typeId'=>'roomTypeId',
            'typeName'=>'roomTypeName',
            'viewId'=>'roomViewId',
            'viewName'=>'roomViewName',
            'cotsCount'=>'cots',
        );


        $hotelParams = array();
        //$hotelParams['searchId'] = $searchId;
        foreach($hotelAttrMap as $paramKey=>$itemKey)
        {
            if(isset($hotelSXE[$itemKey]))
            {
                if(is_numeric($paramKey))
                {
                    $hotelParams[$itemKey] = (string)$hotelSXE[$itemKey];
                }
                else
                {
                    $hotelParams[$paramKey] = (string)$hotelSXE[$itemKey];
                }
            }
        }
        if(isset($hotelSXE->Rooms->Room))
        {
            $hotelParams['rooms'] = array();
            UtilsHelper::soapObjectsArray($hotelSXE->Rooms->Room);
            foreach($hotelSXE->Rooms->Room as $roomSXE)
            {
                $roomParams = array();
                foreach($roomAttrMap as $paramKey=>$itemKey)
                {
                    if(isset($roomSXE[$itemKey]))
                    {
                        if(is_numeric($paramKey))
                        {
                            $roomParams[$itemKey] = (string)$roomSXE[$itemKey];
                        }
                        else
                        {
                            $roomParams[$paramKey] = (string)$roomSXE[$itemKey];
                        }
                    }
                }
                if(isset($roomSXE->ChildAge))
                {
                    UtilsHelper::soapObjectsArray($roomSXE->ChildAge);
                    $childAges = array();
                    foreach($roomSXE->ChildAge as $childAge)
                    {
                        $childAges[] = (string)$childAge;
                    }
                    $roomParams['childAges'] = $childAges;
                    $roomParams['childCount'] = count($childAges);
                }
                $hotelParams['rooms'][] = $roomParams;
            }
        }

        $hotel = new Hotel($hotelParams);
        unset($hotelParams);
        unset($hotelAttrMap);
        unset($roomAttrMap);
        return $hotel;
    }

    public function hotelSearch($params)
    {
        $this->synchronize();
        $time = time() + $this->differenceTimestamp;
        $getData = array('login'=>Yii::app()->params['HotelBook']['login'],'time'=>$time,'checksum'=>$this->getChecksum($time));

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<HotelSearchRequest>
    <Request
        cityId="6788"
        checkIn="2012-09-17"
        duration="7" confirmation="all"/>
    <Rooms>
        <Room roomSizeId="8" child="0" roomNumber="1" >
        </Room>
    </Rooms>
</HotelSearchRequest>';

        /*if($countryId)
        {
            $getData['country_id'] = $countryId;
        }*/
        $requestObject = simplexml_load_string($xml);
        if(isset($params['cityId'])){
            $requestObject->Request['cityId'] = $params['cityId'];
        }
        if(isset($params['checkIn'])){
            $requestObject->Request['checkIn'] = $params['checkIn'];
        }
        if(isset($params['duration'])){
            $requestObject->Request['duration'] = $params['duration'];
        }
        if(isset($params['checkIn'])){
            $requestObject->Request['checkIn'] = $params['checkIn'];
        }
        if(isset($params['hotelId'])){
            $requestObject->Request['hotelId'] = $params['hotelId'];
        }
        if(isset($params['confirmation'])){
            $requestObject->Request['confirmation'] = $params['confirmation'];
        }
        if(isset($params['hotelName'])){
            $requestObject->Request['hotelName'] = $params['hotelName'];
        }
        /*if(isset($params['duration'])){
            $requestObject->HotelSearchRequest->Request['duration'] = $params['duration'];
        }
        if(isset($params['duration'])){
            $requestObject->HotelSearchRequest->Request['duration'] = $params['duration'];
        }*/
        if(isset($params['hotelItems'])){
            $hotelItems = $requestObject->Rooms->addChild('HotelItems');
            foreach($params['hotelItems'] as $item){
                $hotelItems->addChild('HotelItem',$item);
            }
        }
        if(isset($params['rooms'])){

            unset($requestObject->Rooms->Room);

            foreach($params['rooms'] as $room){
                //print_r($requestObject->Request);die();
                $newRoom = $requestObject->Rooms->addChild('Room');
                foreach($room as $attrName=>$attrVal){
                    if($attrName !== 'ChildAge'){
                        $newRoom->addAttribute($attrName, $attrVal);
                    }else{
                        $newRoom->addChild($attrName,$attrVal);
                    }
                }

                //$requestObject->Request->Rooms;
            }
        }

        VarDumper::dump($requestObject);
        $xml = $requestObject->asXML();
        $hotelsXml = $this->request(Yii::app()->params['HotelBook']['uri'].'hotel_search',$getData,array('request'=>$xml));
        echo $hotelsXml;
        $hotelsObject = simplexml_load_string($hotelsXml);
        $response = new HotelSearchResponse();
        $searchId = (string)$hotelsObject->HotelSearch['searchId'];
        $response->searchId = $searchId;
        $response->timestamp = time();
        if(isset($hotelsObject->Hotels->Hotel))
        {
            UtilsHelper::soapObjectsArray($hotelsObject->Hotels->Hotel);
            foreach($hotelsObject->Hotels->Hotel as $hotelItem)
            {
                $hotel = $this->getHotelFromSXE($hotelItem);
                $hotel->searchId = $searchId;
                $response->hotels[] = $hotel;
            }
        }
        if(isset($hotelsObject->Errors->Error))
        {
            UtilsHelper::soapObjectsArray($hotelsObject->Errors->Error);
            foreach($hotelsObject->Errors->Error as $errorItem)
            {
                $response->errorsDescriptions[] = array('code'=>(string)$errorItem['code'],'description'=>(string)$errorItem['description'] );
            }
        }
        if($response->hotels && $response->errorsDescriptions)
        {
            $response->errorStatus = 1;
        }
        elseif($response->hotels)
        {
            $response->errorStatus = 0;
        }
        else
        {
            $response->errorStatus = 2;
        }
        return $response;
    }

    public function synchronize()
    {
        if(!$this->isSynchronized)
        {
            $unixtime = $this->request(Yii::app()->params['HotelBook']['uri'].'unix_time');

            $diff = Yii::app()->cache->get('hotelbookDifferenceTimestamp');
            if($diff === false){
                $this->differenceTimestamp = $unixtime - time();
                Yii::app()->cache->set('hotelbookDifferenceTimestamp',$this->differenceTimestamp);
            }else{
                $this->differenceTimestamp = $diff;
            }
            $this->isSynchronized = true;
        }

        //echo "ts:{$unixtime} NN:".date("Y-m-d H:i:s",$unixtime).' NFT:'.date("Y-m-d H:i:s").' NCC:'.date("Y-m-d H:i:s",(time() + $this->differenceTimestamp));

    }
}