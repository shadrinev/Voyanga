<?php
class HotelBookClient
{
    public $differenceTimestamp = 0;
    public $isSynchronized = false;
    public $lastHeaders;
    public $multiCurl;
    public static $roomSizeRoomTypesMap = array(1=>array(1),2=>array(2,3),3=>array(5),4=>array(6));
    public static $roomSizeIdNamesMap = array(1=>'SGL',2=>'DBL',3=>'TWN',4=>'TWNSU',5=>'TRLP',6=>'QUAD',7=>'DBLSU');
    public static $lastRequestMethod;
    /** @var City lastRequestCity */
    public static $lastRequestCity;
    public static $lastRequestCityHaveCoordinates;
    public static $lastRequestDescription;
    public static $groupId;
    public $requests;

    public function request($url, $getData = null, $postData = null, $asyncParams = null)
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
        $key = $url . md5(serialize($postData));
        $mongoKey = substr(md5($key. uniqid('',true)),0,10);

        $hotelRequest = new HotelRequest();
        $hotelRequest->requestNum = $mongoKey;
        $hotelRequest->timestamp = time();
        $hotelRequest->methodName = self::$lastRequestMethod;
        $hotelRequest->requestUrl = $url;
        if(self::$groupId)
        {
            $hotelRequest->groupId = self::$groupId;
        }
        $hotelRequest->requestDescription = self::$lastRequestDescription;
        $hotelRequest->requestXml = isset($postData['request']) ? $postData['request'] : '';
        $hotelRequest->save();


        if($asyncParams === null)
        {
            $startTime = microtime(true);
            $sData = curl_exec($rCh);
            $endTime = microtime(true);

            //Biletoid_Utils::addLogMessage($sData, '/tmp/curl_response.txt');
            if ($sData !== FALSE)
            {
                list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
                if (strpos($sHeaders, 'Continue') !== FALSE)
                {
                    list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
                }
                $this->lastHeaders = $sHeaders;

                $hotelRequest->executionTime = ($endTime - $startTime);
                $hotelRequest->responseXml = UtilsHelper::formatXML($sData);
                $hotelRequest->save();
            }
            else
            {
                $this->lastCurlError = curl_error ($rCh);
                $hotelRequest->errorDescription = $this->lastCurlError;
                $hotelRequest->save();
            }

            return $sData;
        }
        else
        {
            curl_setopt($rCh, CURLOPT_HEADER, false);
            if(!$this->multiCurl)
            {
                $this->multiCurl = curl_multi_init();
            }

            $this->requests[] = array('curlHandle'=>$rCh,'completed'=>false,'hotelRequestLog'=>$hotelRequest);

            $id = count($this->requests) - 1;
            curl_multi_add_handle($this->multiCurl,$this->requests[$id]['curlHandle']);
            $this->requests[$id]['id'] = $id;
            $this->requests[$id] = array_merge($this->requests[$id],$asyncParams);
            return $id;
        }
    }

    public function processAsyncRequests()
    {
        if($this->multiCurl)
        {
            $startTime = microtime(true);
            do {
                $status = curl_multi_exec($this->multiCurl, $active);
                $info = curl_multi_info_read($this->multiCurl);
                if (false !== $info) {
                    //var_dump($info);
                    //echo  curl_multi_getcontent($info['handle']);
                    //TODO: partitial processing
                    $endTime = microtime(true);
                    foreach ($this->requests as $i => $requestInfo) {
                        if(!$requestInfo['completed'])
                        {
                            if($requestInfo['curlHandle'] == $info['handle'])
                            {
                                //Process response
                                $result = curl_multi_getcontent($requestInfo['curlHandle']);
                                curl_close($requestInfo['curlHandle']);

                                $requestInfo['hotelRequestLog']->executionTime = ($endTime - $startTime);
                                $requestInfo['hotelRequestLog']->responseXml = UtilsHelper::formatXML($result);
                                $requestInfo['hotelRequestLog']->save();
                                if(isset($requestInfo['function']))
                                {
                                    $params = array($result);
                                    if($requestInfo['params'])
                                    {
                                        foreach($requestInfo['params'] as $param)
                                        {
                                            $params[] = $param;
                                        }
                                    }
                                    $this->requests[$i]['result'] = call_user_func_array($requestInfo['function'],$params);
                                    unset($this->requests[$i]['function']);

                                }
                                else
                                {
                                    $this->requests[$i]['result'] = $result;
                                }
                                $this->requests[$i]['completed'] = true;
                            }
                        }
                    }
                }
            } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

            /*
            foreach ($this->requests as $i => $requestInfo) {
                if(!$requestInfo['completed'])
                {
                    $result = curl_multi_getcontent($requestInfo['curlHandle']);
                    curl_close($requestInfo['curlHandle']);
                    if(isset($requestInfo['function']))
                    {
                        $params = array($result);
                        if($requestInfo['params'])
                        {
                            foreach($requestInfo['params'] as $param)
                            {
                                $params[] = $param;
                            }
                        }
                        $this->requests[$i]['result'] = call_user_func_array($requestInfo['function'],$params);
                        unset($this->requests[$i]['function']);

                    }
                    else
                    {
                        $this->requests[$i]['result'] = $result;
                    }
                    $this->requests[$i]['completed'] = true;
                }
                //$res[$i] = curl_multi_getcontent($conn[$i]);

            }*/

            curl_multi_close($this->multiCurl);
            $this->multiCurl = null;
        }

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
        self::$lastRequestMethod = 'Countries';
        self::$lastRequestDescription = '';
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
        self::$lastRequestMethod = 'Cities';
        self::$lastRequestDescription = '';
        if($countryId)
        {
            $getData['country_id'] = $countryId;
            self::$lastRequestDescription = Country::getCountryByHotelbookId($countryId)->code;
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
            'hotelId','hotelName','resultId','confirmation','price','currency','comparePrice','specialOffer','providerId','providerHotelCode',
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
                $k = intval((string)$roomSXE['roomNumber']);
                for($i=0;$i<$k;$i++)
                    $hotelParams['rooms'][] = $roomParams;

            }
        }

        $hotel = new Hotel($hotelParams);
        if(self::$lastRequestCityHaveCoordinates)
        {
            if(($hotel->latitude !== null) && ($hotel->longitude !== null))
            {
                $hotel->centerDistance = intval(UtilsHelper::calculateTheDistance(self::$lastRequestCity->latitude,self::$lastRequestCity->longitude,$hotel->latitude,$hotel->longitude));
                if($hotel->centerDistance > 100000)
                {
                    $hotel->centerDistance = PHP_INT_MAX;
                }
            }
        }
        unset($hotelParams);
        unset($hotelAttrMap);
        unset($roomAttrMap);
        return $hotel;
    }

    private function prepareHotelSearchRequest($params)
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
<HotelSearchRequest>
    <Request
        cityId="6788"
        checkIn="2012-09-17"
        duration="7" confirmation="online"/>
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
        if(isset($params['hotelId'])){
            $requestObject->Request['hotelId'] = $params['hotelId'];
        }
        if(isset($params['confirmation'])){
            $requestObject->Request['confirmation'] = $params['confirmation'];
        }
        if(isset($params['hotelName'])){
            $requestObject->Request['hotelName'] = $params['hotelName'];
        }

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
                        if(intval($room['child']) > 0)
                        {
                            echo "MMMMMM||";
                            $newRoom->addChild($attrName,$attrVal);
                        }
                    }
                }

                //$requestObject->Request->Rooms;
            }
        }

        //VarDumper::dump($requestObject);
        $xml = $requestObject->asXML();
        return $xml;
    }

    private function processHotelSearchResponse($hotelsXml,$checkIn,$duration)
    {
        $hotelsObject = simplexml_load_string($hotelsXml);
        //VarDumper::dump($hotelsObject);
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
                $hotel->checkIn = $checkIn;
                $hotel->duration = $duration;
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

    public function hotelSearch($params, $async = false)
    {
        $this->synchronize();
        $time = time() + $this->differenceTimestamp;
        $getData = array('login'=>Yii::app()->params['HotelBook']['login'],'time'=>$time,'checksum'=>$this->getChecksum($time));
        self::$lastRequestMethod = 'HotelSearch';
        self::$lastRequestDescription = '';
        self::$lastRequestCity = City::getCityByHotelbookId($params['cityId']);
        self::$lastRequestCityHaveCoordinates = (self::$lastRequestCity->latitude !== null) && (self::$lastRequestCity->longitude !== null);
        foreach($params['rooms'] as $room)
        {
            self::$lastRequestDescription .= (self::$lastRequestDescription ? ' & ' : '').self::$roomSizeIdNamesMap[$room['roomSizeId']].($room['cots'] ? $room['cots'].'COTS' : '' ).($room['child'] ? 'CHLD'.$room['ChildAge'].'AGE' : '' ).(isset($room['roomNumber']) ? ($room['roomNumber'] > 1 ? 'x'.$room['roomNumber'] : '' ) : '' );
        }
        if($async)
        {
            $asyncParams = array('function'=>array($this,'processHotelSearchResponse'),'params'=>array($params['checkIn'], $params['duration']) );
            //$asyncParams = array();
            return $this->request(Yii::app()->params['HotelBook']['uri'].'hotel_search',$getData,array('request'=>$this->prepareHotelSearchRequest($params)),$asyncParams);

        }else{
            $path = Yii::getPathOfAlias('application.runtime');
            file_put_contents($path.'/request_'.date('Y-m-d_H_i_s').'.txt',$this->prepareHotelSearchRequest($params));
            $hotelsXml = $this->request(Yii::app()->params['HotelBook']['uri'].'hotel_search',$getData,array('request'=>$this->prepareHotelSearchRequest($params)));

            //CTextHighlighter::registerCssFile();
            file_put_contents($path.'/response_'.date('Y-m-d_H_i_s').'.txt',$hotelsXml);


            //echo $hotelsXml;
            //die();
            //VarDumper::xmlDump($hotelsXml);
            //die();
            return (array)$this->processHotelSearchResponse($hotelsXml,$params['checkIn'],$params['duration']);
        }
    }

    /**
     * Function for sorting by uasort
     * @param $a
     * @param $b
     */
    private static function compareArrayAdultCount($a, $b)
    {
        if ($a['adultCount'] < $b['adultCount'])
        {
            return -1;
        } elseif ($a['adultCount'] > $b['adultCount'])
        {
            return 1;
        }
        return 0;
    }

    /**
     * Do HotelSearch requests with all combinations of room types,
     * @param HotelSearchParams $hotelSearchParams
     */
    public function fullHotelSearch(HotelSearchParams $hotelSearchParams)
    {
        $rooms = $hotelSearchParams->rooms;
        //Make combinations to combinations Array
        uasort($rooms,'HotelBookClient::compareArrayAdultCount');
        $combinations = array();
        foreach($rooms as $key=>$room)
        {
            $rooms[$key]['sizeCount'] = count(self::$roomSizeRoomTypesMap[$room['adultCount']]);
            $rooms[$key]['sizeIndex'] = 0;

        }
        $allCombined = false;
        // Make ALL possible combinations
        while(!$allCombined)
        {
            $combination = array();
            $allCombined = true;
            foreach($rooms as $key=>$room)
            {

                if($room['sizeCount'] !== ($room['sizeIndex'] + 1)) $allCombined = false;
                $rooms[$key]['roomSizeId'] = self::$roomSizeRoomTypesMap[$room['adultCount']][$room['sizeIndex']];
                $combination[] = array('roomSizeId'=>$rooms[$key]['roomSizeId'],'child'=>$rooms[$key]['childCount'],'cots'=>$rooms[$key]['cots'],'ChildAge'=>$rooms[$key]['childAge']);
            }
            sort($combination);
            $combinations[] = $combination;
            if(!$allCombined)
            {
                //next possible state
                $overflow = false;
                $iterationComplete = false;
                foreach($rooms as $key=>$room)
                {
                    if($room['sizeCount'] == 1){
                        continue;
                    }
                    if($iterationComplete)
                    {
                        if($overflow)
                        {
                            if($room['sizeCount'] == ($room['sizeIndex'] + 1) )
                            {
                                $rooms[$key]['sizeIndex'] = 0;
                            }
                            else
                            {
                                $rooms[$key]['sizeIndex']++;
                                $overflow = false;
                                break;
                            }
                        }
                        else
                        {
                            break;
                        }
                    }
                    else
                    {
                        if($room['sizeCount'] == ($room['sizeIndex'] + 1) )
                        {
                            $rooms[$key]['sizeIndex'] = 0;
                            $iterationComplete = true;
                            $overflow = true;
                        }
                        else
                        {
                            $rooms[$key]['sizeIndex']++;
                            break;
                        }
                    }
                }
            }
        }
        //delete same combinations
        sort($combinations);
        foreach($combinations as $key=>$combination)
        {
            if(!isset($prevComb))
            {
                $prevComb = $combination;
                continue;
            }
            if($prevComb == $combination)
            {
                unset($combinations[$key]);
            }

            $prevComb = $combination;
        }
        unset($prevComb);
        unset($combination);
        //add requests to queue
        self::$groupId = substr(md5(uniqid('',true)),0,10);
        $params = array('cityId'=>$hotelSearchParams->city->hotelbookId,'checkIn'=>$hotelSearchParams->checkIn,'duration'=>$hotelSearchParams->duration);
        foreach($combinations as $key=>$combination)
        {
            $params['rooms'] = array();
            foreach($combination as $i=>$room)
            {
                if(!isset($prevInd))
                {
                    $prevInd = $i;
                    $roomNumber = 1;
                    continue;
                }
                if($combination[$i] === $combination[$prevInd])
                {
                    $roomNumber++;
                    continue;
                }
                else
                {
                    $combination[$prevInd]['roomNumber'] = $roomNumber;
                    $params['rooms'][] = $combination[$prevInd];
                    $prevInd = $i;
                    $roomNumber = 1;
                }
            }
            $combination[$prevInd]['roomNumber'] = $roomNumber;
            $params['rooms'][] = $combination[$prevInd];
            //print_r($params);
            unset($prevInd);
            $this->hotelSearch($params, true);
        }
        //run all requests
        $this->processAsyncRequests();
        self::$groupId = null;
        $hotels = array();
        $errorDescriptions = array();
        foreach($this->requests as $request)
        {
            //echo count($request['result']->hotels).'<br>';
            foreach($request['result']->hotels as $hotel)
            {
                $key = $hotel->key;
                if(isset($hotels[$key]))
                {
                    //echo '--duplicate';
                    //echo 'have:';
                    //VarDumper::dump($hotels[$key]);
                    //echo 'new:';
                    //VarDumper::dump($hotel);
                }
                $hotels[$key] = $hotel;
            }
            foreach($request['result']->errorsDescriptions as $desc)
            {
                $errorDescriptions[] = $desc;
            }
        }

        if($hotels && $errorDescriptions)
        {
            $errorStatus = 1;
        }
        elseif($hotels)
        {
            $errorStatus = 0;
        }
        else
        {
            $errorStatus = 2;
        }

/*        //print_r($combinations);
        print_r(count($hotels));
        print_r($errorDescriptions);*/
        if($hotels)
        {
            if(count($hotelSearchParams->rooms) == 1)
            {
                foreach($hotelSearchParams->rooms as $room) break;
                if( ($room['adultCount'] == 2) && ($room['childCount'] == 0) && ($room['cots'] == 0))
                {
                    $allHotelStack = new HotelStack(array('hotels'=>$hotels));
                    $allHotelStack->groupBy('categoryId')->groupBy('roomSizeId')->groupBy('roomTypeId')->groupBy('centerDistance')->groupBy('rubPrice');
                    //VarDumper::dump($hotelStack->hotelStacks);
                    foreach($allHotelStack as $categoryId=>$hotelStack)
                    {
                        if(($categoryId == 2) || ($categoryId == 1) || ($categoryId == 3) )
                        {

                            $haveStack = false;
                            foreach($hotelStack->hotelStacks as $i=>$hotelStackSize)
                            {
                                if(($i != 20) && ($i != 30))
                                {
                                    unset($hotelStack->hotelStacks[$i]);
                                }
                                else
                                {
                                    //echo "in 2";
                                    foreach($hotelStack->hotelStacks[$i]->hotelStacks as $j=>$hotelStackType)
                                    {
                                        if(($j != 10) && ($j != 12900))
                                        {
                                            unset($hotelStack->hotelStacks[$i]->hotelStacks[$j]);
                                        }
                                        else
                                        {
                                            //echo "in 3";
                                            foreach($hotelStack->hotelStacks[$i]->hotelStacks[$j]->hotelStacks as $k=>$hotelStackDistance)
                                            {
                                                if(($k > 5000))
                                                {
                                                    //echo "out $k";
                                                    unset($hotelStack->hotelStacks[$i]->hotelStacks[$j]->hotelStacks[$k]);
                                                }
                                                else
                                                {
                                                    //echo "in 4";
                                                    $haveStack = true;
                                                }
                                            }

                                        }
                                    }
                                }
                            }
                            if($haveStack)
                            {
                                VarDumper::dump($hotelStack->sortBy('rubPrice')->getHotel()->getJsonObject());
                            }else{
                                VarDumper::dump($hotelStack->getJsonObject(5));
                            }
                        }
                    }

                }
            }
        }
        return array('hotels'=>$hotels,'errorsDescriptions'=>$errorDescriptions,'errorStatus'=>$errorStatus);
    }

    /**
     * Additional information to hotel
     * @param Hotel $hotel
     */
    public function hotelSearchDetails(Hotel &$hotel)
    {
        $this->synchronize();
        $time = time() + $this->differenceTimestamp;
        $getData = array('login'=>Yii::app()->params['HotelBook']['login'],'time'=>$time,'checksum'=>$this->getChecksum($time));

        self::$lastRequestMethod = 'HotelSearchDetails';
        self::$lastRequestDescription = "SID:{$hotel->searchId} RID: {$hotel->resultId} HID: {$hotel->hotelId}";

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<HotelSearchDetailsRequest>
    <HotelSearches>
        <HotelSearch>
            <SearchId>'.$hotel->searchId.'</SearchId>
            <ResultId>'.$hotel->resultId.'</ResultId>
        </HotelSearch>
    </HotelSearches>
</HotelSearchDetailsRequest>';

        $hotelXml = $this->request(Yii::app()->params['HotelBook']['uri'].'hotel_search_details',$getData,array('request'=>$xml));
        $hotelObject = simplexml_load_string($hotelXml);
        if(isset($hotelObject->HotelSearchDetails->Hotel->ChargeConditions)){
            $currency = (string)$hotelObject->HotelSearchDetails->Hotel->ChargeConditions->Currency;
            UtilsHelper::soapObjectsArray($hotelObject->HotelSearchDetails->Hotel->ChargeConditions->Cancellations->Cancellation);
            foreach($hotelObject->HotelSearchDetails->Hotel->ChargeConditions->Cancellations->Cancellation as $cancelSXE)
            {
                $cancelParams = array();
                $cancelParams['charge'] = (string)$cancelSXE['charge'];
                $cancelParams['denyChanges'] = (string)$cancelSXE['denyChanges'];
                if($cancelSXE['from']){
                    $cancelParams['from'] = (string)$cancelSXE['from'];
                }
                if($cancelSXE['to']){
                    $cancelParams['to'] = (string)$cancelSXE['to'];
                }
                if($cancelSXE['price']){
                    $cancelParams['price'] = (string)$cancelSXE['price'];
                }
                $hotel->addCancelCharge($cancelParams);
            }
        }
        //VarDumper::dump($hotelObject);
        //echo $hotelsXml;
    }

    public function hotelSearchFullDetails()
    {

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