<?php
class HotelBookClient
{
    public $differenceTimestamp = 0;
    public $isSynchronized = false;
    public $lastHeaders;
    public $multiCurl;
    public static $roomSizeRoomTypesMap = array(1 => array(1), 2 => array(2, 3), 3 => array(5), 4 => array(6));
    public static $roomSizeIdNamesMap = array(1 => 'SGL', 2 => 'DBL', 3 => 'TWN', 4 => 'TWNSU', 5 => 'TRLP', 6 => 'QUAD', 7 => 'DBLSU', 8 => 'DBLORTWIN');
    public static $lastRequestMethod;
    /** @var City lastRequestCity */
    public static $lastRequestCity;
    public static $lastRequestCityHaveCoordinates;
    public static $lastRequestDescription;
    public static $groupId;
    public static $requestIds;
    public $requests;

    public function request($url, $getData = null, $postData = null, $asyncParams = null)
    {
        $rCh = curl_init();

        if ($postData)
        {
            curl_setopt($rCh, CURLOPT_POST, (true));
        }
        curl_setopt($rCh, CURLOPT_HEADER, true);
        curl_setopt($rCh, CURLOPT_RETURNTRANSFER, true);
        if ($postData)
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
        if ($getData)
        {
            $pos = strpos($url, '?');
            if ($pos !== false)
            {
                list($url, $args) = explode("?", $url, 2);
                parse_str($args, $params);
                $getData = array_merge($params, $getData);
            }

            $url = $url . '?' . http_build_query($getData);
        }


        curl_setopt($rCh, CURLOPT_URL, $url);
        $key = $url . md5(serialize($postData));
        $mongoKey = substr(md5($key . uniqid('', true)), 0, 10);

        $hotelRequest = new HotelRequest();
        $hotelRequest->requestNum = $mongoKey;
        self::$requestIds[] = array('key'=>$mongoKey,'class'=>get_class($hotelRequest),'keyName'=>'requestNum');
        $hotelRequest->timestamp = time();
        //echo 'send req: '.self::$lastRequestMethod."\n";
        $hotelRequest->methodName = self::$lastRequestMethod;
        $hotelRequest->requestUrl = $url;
        if (self::$groupId)
        {
            $hotelRequest->groupId = self::$groupId;
        }
        $hotelRequest->requestDescription = self::$lastRequestDescription;
        $hotelRequest->requestXml = isset($postData['request']) ? $postData['request'] : '';
        $valid = $hotelRequest->save();
        if(!$valid) CVarDumper::dump($hotelRequest->getErrors());



        if ($asyncParams === null)
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
                $this->lastCurlError = curl_error($rCh);
                $hotelRequest->errorDescription = $this->lastCurlError;
                $hotelRequest->save();
            }

            return $sData;
        }
        else
        {
            curl_setopt($rCh, CURLOPT_HEADER, false);
            if (!$this->multiCurl)
            {
                $this->multiCurl = curl_multi_init();
            }

            $this->requests[] = array('curlHandle' => $rCh, 'completed' => false, 'hotelRequestLog' => $hotelRequest);

            $id = count($this->requests) - 1;
            curl_multi_add_handle($this->multiCurl, $this->requests[$id]['curlHandle']);
            $this->requests[$id]['id'] = $id;
            $this->requests[$id] = array_merge($this->requests[$id], $asyncParams);
            return $id;
        }
    }

    public function processAsyncRequests()
    {
        if ($this->multiCurl)
        {
            $startTime = microtime(true);
            do
            {
                $status = curl_multi_exec($this->multiCurl, $active);
                $info = curl_multi_info_read($this->multiCurl);
                if (false !== $info)
                {
                    //var_dump($info);
                    //echo  curl_multi_getcontent($info['handle']);
                    //partial processing
                    $endTime = microtime(true);
                    foreach ($this->requests as $i => $requestInfo)
                    {
                        if (!$requestInfo['completed'])
                        {
                            if ($requestInfo['curlHandle'] == $info['handle'])
                            {
                                //Process response
                                $result = curl_multi_getcontent($requestInfo['curlHandle']);
                                curl_close($requestInfo['curlHandle']);

                                $requestInfo['hotelRequestLog']->executionTime = ($endTime - $startTime);
                                $requestInfo['hotelRequestLog']->responseXml = UtilsHelper::formatXML($result);
                                $requestInfo['hotelRequestLog']->save();
                                if (isset($requestInfo['function']))
                                {
                                    $params = array($result);
                                    if ($requestInfo['params'])
                                    {
                                        foreach ($requestInfo['params'] as $param)
                                        {
                                            $params[] = $param;
                                        }
                                    }
                                    $this->requests[$i]['result'] = call_user_func_array($requestInfo['function'], $params);
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

            curl_multi_close($this->multiCurl);
            $this->multiCurl = null;
        }

    }

    private function getChecksum($time)
    {
        return md5(md5(Yii::app()->params['HotelBook']['password']) . $time);
    }

    public function getCountries()
    {
        $this->synchronize();
        //echo "iNN";
        $time = time() + $this->differenceTimestamp;
        $getData = array('login' => Yii::app()->params['HotelBook']['login'], 'time' => $time, 'checksum' => $this->getChecksum($time));
        self::$lastRequestMethod = 'Countries';
        self::$lastRequestDescription = '';
        $countries = $this->request(Yii::app()->params['HotelBook']['uri'] . 'countries', $getData);
        $countriesObject = simplexml_load_string($countries);
        $return = array();
        foreach ($countriesObject->Countries->Country as $country)
        {
            $id = intval($country['id']);
            $name = trim((string)$country);
            $return[$id] = array('id' => $id, 'nameRu' => $name);
        }
        $getData['language'] = 'en';
        $countries = $this->request(Yii::app()->params['HotelBook']['uri'] . 'countries', $getData);
        $countriesObject = simplexml_load_string($countries);
        foreach ($countriesObject->Countries->Country as $country)
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
        $getData = array('login' => Yii::app()->params['HotelBook']['login'], 'time' => $time, 'checksum' => $this->getChecksum($time));
        self::$lastRequestMethod = 'Cities';
        self::$lastRequestDescription = '';
        if ($countryId)
        {
            $getData['country_id'] = $countryId;
            self::$lastRequestDescription = Country::getCountryByHotelbookId($countryId)->code;
        }
        $cities = $this->request(Yii::app()->params['HotelBook']['uri'] . 'cities', $getData);
        $citiesObject = simplexml_load_string($cities);
        $return = array();
        foreach ($citiesObject->Cities->City as $city)
        {
            $id = intval($city['id']);
            $name = trim((string)$city);
            $country_id = intval($city['country']);
            $return[$id] = array('id' => $id, 'nameEn' => $name, 'countryId' => $country_id);
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
            'hotelId', 'hotelName', 'resultId', 'confirmation', 'price', 'currency', 'comparePrice', 'specialOffer', 'providerId', 'providerHotelCode',
            'categoryId' => 'hotelCatId',
            'categoryName' => 'hotelCatName',
            'address' => 'hotelAddress',
            'latitude' => 'hotelLatitude',
            'longitude' => 'hotelLongitude',
            'rubPrice' => 'comparePrice'
        );
        $roomAttrMap = array(
            'mealId', 'mealName', 'mealBreakfastId', 'mealBreakfastName', 'sharingBedding',
            'sizeId' => 'roomSizeId',
            'sizeName' => 'roomSizeName',
            'typeId' => 'roomTypeId',
            'typeName' => 'roomTypeName',
            'viewId' => 'roomViewId',
            'viewName' => 'roomViewName',
            'cotsCount' => 'cots',
        );


        $hotelParams = array();
        //$hotelParams['searchId'] = $searchId;
        foreach ($hotelAttrMap as $paramKey => $itemKey)
        {
            if (isset($hotelSXE[$itemKey]))
            {
                if (is_numeric($paramKey))
                {
                    $hotelParams[$itemKey] = (string)$hotelSXE[$itemKey];
                }
                else
                {
                    $hotelParams[$paramKey] = (string)$hotelSXE[$itemKey];
                }
            }
        }
        if (isset($hotelSXE->Rooms->Room))
        {
            $hotelParams['rooms'] = array();
            UtilsHelper::soapObjectsArray($hotelSXE->Rooms->Room);
            foreach ($hotelSXE->Rooms->Room as $roomSXE)
            {
                $roomParams = array();
                foreach ($roomAttrMap as $paramKey => $itemKey)
                {
                    if (isset($roomSXE[$itemKey]))
                    {
                        if (is_numeric($paramKey))
                        {
                            $roomParams[$itemKey] = (string)$roomSXE[$itemKey];
                        }
                        else
                        {
                            $roomParams[$paramKey] = (string)$roomSXE[$itemKey];
                        }
                    }
                }
                if (isset($roomSXE->ChildAge))
                {
                    UtilsHelper::soapObjectsArray($roomSXE->ChildAge);
                    $childAges = array();
                    foreach ($roomSXE->ChildAge as $childAge)
                    {
                        $childAges[] = (string)$childAge;
                    }
                    $roomParams['childAges'] = $childAges;
                    $roomParams['childCount'] = count($childAges);
                }
                $k = intval((string)$roomSXE['roomNumber']);
                for ($i = 0; $i < $k; $i++)
                    $hotelParams['rooms'][] = $roomParams;

            }
        }

        $hotel = new Hotel($hotelParams);
        if (self::$lastRequestCityHaveCoordinates)
        {
            if (($hotel->latitude !== null) && ($hotel->longitude !== null))
            {
                $hotel->centerDistance = intval(UtilsHelper::calculateTheDistance(self::$lastRequestCity->latitude, self::$lastRequestCity->longitude, $hotel->latitude, $hotel->longitude));
                if ($hotel->centerDistance > 100000)
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
        if (isset($params['cityId']))
        {
            $requestObject->Request['cityId'] = $params['cityId'];
        }
        if (isset($params['checkIn']))
        {
            $requestObject->Request['checkIn'] = $params['checkIn'];
        }
        if (isset($params['duration']))
        {
            $requestObject->Request['duration'] = $params['duration'];
        }
        if (isset($params['hotelId']))
        {
            $requestObject->Request['hotelId'] = $params['hotelId'];
        }
        if (isset($params['confirmation']))
        {
            $requestObject->Request['confirmation'] = $params['confirmation'];
        }
        if (isset($params['hotelName']))
        {
            $requestObject->Request['hotelName'] = $params['hotelName'];
        }

        if (isset($params['hotelItems']))
        {
            $hotelItems = $requestObject->Rooms->addChild('HotelItems');
            foreach ($params['hotelItems'] as $item)
            {
                $hotelItems->addChild('HotelItem', $item);
            }
        }
        if (isset($params['rooms']))
        {

            unset($requestObject->Rooms->Room);

            foreach ($params['rooms'] as $room)
            {
                //print_r($requestObject->Request);die();
                $newRoom = $requestObject->Rooms->addChild('Room');
                foreach ($room as $attrName => $attrVal)
                {
                    if ($attrName !== 'ChildAge')
                    {
                        $newRoom->addAttribute($attrName, $attrVal);
                    }
                    else
                    {
                        if (intval($room['child']) > 0)
                        {
                            //echo "MMMMMM||";
                            $newRoom->addChild($attrName, $attrVal);
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

    private function processHotelSearchResponse($hotelsXml, $checkIn, $duration)
    {
        $hotelsObject = simplexml_load_string($hotelsXml);
        $response = new HotelSearchResponse();
        if($hotelsObject)
        {
            if (isset($hotelsObject->HotelSearch['searchId']))
            {
                $searchId = (string)$hotelsObject->HotelSearch['searchId'];
            }
            else
            {
                $response->searchId = null;
                $response->timestamp = time();
                $response->errorStatus = 2;
                $response->errorsDescriptions[] = array('code' => '', 'description' => 'Incorrect response from remote server');
                return $response;
            }
            $response->searchId = $searchId;
            $response->timestamp = time();
            if (isset($hotelsObject->Hotels->Hotel))
            {
                UtilsHelper::soapObjectsArray($hotelsObject->Hotels->Hotel);
                foreach ($hotelsObject->Hotels->Hotel as $hotelItem)
                {
                    $hotel = $this->getHotelFromSXE($hotelItem);
                    $hotel->searchId = $searchId;
                    $hotel->checkIn = $checkIn;
                    $hotel->duration = $duration;
                    $hotel->cityId = (string)$hotelsObject->HotelSearch['cityId'];
                    $response->hotels[] = $hotel;
                }
            }
            if (isset($hotelsObject->Errors->Error))
            {
                UtilsHelper::soapObjectsArray($hotelsObject->Errors->Error);
                foreach ($hotelsObject->Errors->Error as $errorItem)
                {
                    $response->errorsDescriptions[] = array('code' => (string)$errorItem['code'], 'description' => (string)$errorItem['description']);
                }
            }
            if ($response->hotels && $response->errorsDescriptions)
            {
                $response->errorStatus = 1;
            }
            elseif ($response->hotels)
            {
                $response->errorStatus = 0;
            }
            else
            {
                $response->errorStatus = 2;
            }
        }
        else
        {
            $response->searchId = null;
            $response->timestamp = time();
            $response->errorStatus = 2;
            $response->errorsDescriptions[] = array('code' => '', 'description' => 'Empty response from remote server');
        }
        return $response;
    }

    /**
     * @param $params
     * @param bool $async
     * @return array|int|mixed
     */
    public function hotelSearch($params, $async = false)
    {
        $this->synchronize();
        $time = time() + $this->differenceTimestamp;
        $getData = array('login' => Yii::app()->params['HotelBook']['login'], 'time' => $time, 'checksum' => $this->getChecksum($time));
        self::$lastRequestMethod = 'HotelSearch';
        self::$lastRequestDescription = '';
        self::$lastRequestCity = City::getCityByHotelbookId($params['cityId']);
        self::$lastRequestCityHaveCoordinates = (self::$lastRequestCity->latitude !== null) && (self::$lastRequestCity->longitude !== null);
        foreach ($params['rooms'] as $room)
        {
            self::$lastRequestDescription .= (self::$lastRequestDescription ? ' & ' : '') . self::$roomSizeIdNamesMap[$room['roomSizeId']] . ($room['cots'] ? $room['cots'] . 'COTS' : '') . ($room['child'] ? 'CHLD' . $room['ChildAge'] . 'AGE' : '') . (isset($room['roomNumber']) ? ($room['roomNumber'] > 1 ? 'x' . $room['roomNumber'] : '') : '');
        }
        if ($async)
        {
            $asyncParams = array('function' => array($this, 'processHotelSearchResponse'), 'params' => array($params['checkIn'], $params['duration']));
            //$asyncParams = array();
            return $this->request(Yii::app()->params['HotelBook']['uri'] . 'hotel_search', $getData, array('request' => $this->prepareHotelSearchRequest($params)), $asyncParams);

        }
        else
        {
            $path = Yii::getPathOfAlias('application.runtime');
            file_put_contents($path . '/request_' . date('Y-m-d_H_i_s') . '.txt', $this->prepareHotelSearchRequest($params));
            $hotelsXml = $this->request(Yii::app()->params['HotelBook']['uri'] . 'hotel_search', $getData, array('request' => $this->prepareHotelSearchRequest($params)));

            //CTextHighlighter::registerCssFile();
            file_put_contents($path . '/response_' . date('Y-m-d_H_i_s') . '.txt', $hotelsXml);


            //echo $hotelsXml;
            //die();
            //VarDumper::xmlDump($hotelsXml);
            //die();
            return (array)$this->processHotelSearchResponse($hotelsXml, $params['checkIn'], $params['duration']);
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
        }
        elseif ($a['adultCount'] > $b['adultCount'])
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
        uasort($rooms, 'HotelBookClient::compareArrayAdultCount');
        $combinations = array();
        foreach ($rooms as $key => $room)
        {
            $rooms[$key]['sizeCount'] = count(self::$roomSizeRoomTypesMap[$room['adultCount']]);
            $rooms[$key]['sizeIndex'] = 0;
        }
        $allCombined = false;
        // Make ALL possible combinations
        while (!$allCombined)
        {
            $combination = array();
            $allCombined = true;
            foreach ($rooms as $key => $room)
            {

                if ($room['sizeCount'] !== ($room['sizeIndex'] + 1)) $allCombined = false;
                $rooms[$key]['roomSizeId'] = self::$roomSizeRoomTypesMap[$room['adultCount']][$room['sizeIndex']];
                $combination[] = array('roomSizeId' => $rooms[$key]['roomSizeId'], 'child' => $rooms[$key]['childCount'], 'cots' => $rooms[$key]['cots'], 'ChildAge' => $rooms[$key]['childAge']);
            }
            sort($combination);
            $combinations[] = $combination;
            if (!$allCombined)
            {
                //next possible state
                $overflow = false;
                $iterationComplete = false;
                foreach ($rooms as $key => $room)
                {
                    if ($room['sizeCount'] == 1)
                    {
                        continue;
                    }
                    if ($iterationComplete)
                    {
                        if ($overflow)
                        {
                            if ($room['sizeCount'] == ($room['sizeIndex'] + 1))
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
                        if ($room['sizeCount'] == ($room['sizeIndex'] + 1))
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
        foreach ($combinations as $key => $combination)
        {
            if (!isset($prevComb))
            {
                $prevComb = $combination;
                continue;
            }
            if ($prevComb == $combination)
            {
                unset($combinations[$key]);
            }

            $prevComb = $combination;
        }
        unset($prevComb);
        unset($combination);
        //add requests to queue
        self::$groupId = substr(md5(uniqid('', true)), 0, 10);
        $params = array('cityId' => $hotelSearchParams->city->hotelbookId, 'checkIn' => $hotelSearchParams->checkIn, 'duration' => $hotelSearchParams->duration);
        foreach ($combinations as $key => $combination)
        {
            $params['rooms'] = array();
            foreach ($combination as $i => $room)
            {
                if (!isset($prevInd))
                {
                    $prevInd = $i;
                    $roomNumber = 1;
                    continue;
                }
                if ($combination[$i] === $combination[$prevInd])
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
        foreach ($this->requests as $request)
        {
            //echo count($request['result']->hotels).'<br>';
            if($request['result']->hotels)
            {
                foreach ($request['result']->hotels as $hotel)
                {
                    $key = $hotel->key;
                    if (isset($hotels[$key]))
                    {
                        //echo '--duplicate';
                        //echo 'have:';
                        //VarDumper::dump($hotels[$key]);
                        //echo 'new:';
                        //VarDumper::dump($hotel);
                    }
                    $hotels[$key] = $hotel;
                }
            }
            if($request['result']->errorsDescriptions)
            {
                foreach ($request['result']->errorsDescriptions as $desc)
                {
                    $errorDescriptions[] = $desc;
                }
            }
        }

        if ($hotels && $errorDescriptions)
        {
            $errorStatus = 1;
        }
        elseif ($hotels)
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
        if ($hotels)
        {
            if (count($hotelSearchParams->rooms) == 1)
            {
                // O_o
                foreach ($hotelSearchParams->rooms as $room) break;
                if (($room['adultCount'] == 2) && ($room['childCount'] == 0) && ($room['cots'] == 0))
                {
                    $allHotelStack = new HotelStack(array('hotels' => $hotels));
                    //VarDumper::dump($allHotelStack);die();
                    $allHotelStack->groupBy('categoryId')->groupBy('roomSizeId')->groupBy('roomTypeId')->groupBy('centerDistance')->groupBy('rubPrice');
                    //VarDumper::dump($allHotelStack);die();
                    //VarDumper::dump($hotelStack->hotelStacks);
                    foreach ($allHotelStack->hotelStacks as $categoryId => $hotelStack)
                    {
                        //categoryId - star rating (we need 3..5 stars)
                        if (($categoryId == Hotel::STARS_THREE) || ($categoryId == Hotel::STARS_FOUR) || ($categoryId == Hotel::STARS_FIVE))
                        {
                            //echo "category: $categoryId<br>";
                            //VarDumper::dump($hotelStack); die();
                            $haveStack = false;
                            foreach ($hotelStack->hotelStacks as $i => $hotelStackSize)
                            {
                                //VarDumper::dump($i);
                                //echo "roomSizeId: $i<br>";
                                //todo: move to room class
                                if (!in_array($i, array(appParams('HotelBook.room.DBL'), appParams('HotelBook.room.TWIN')) ) )
                                {
                                    unset($hotelStack->hotelStacks[$i]);
                                }
                                else
                                {
                                    //echo "in 2";
                                    foreach ($hotelStack->hotelStacks[$i]->hotelStacks as $j => $hotelStackType)
                                    {
                                        //echo "roomTypeId: $j<br>";
                                        if (!in_array($j, appParams('HotelBook.room.STD')))
                                        {
                                            unset($hotelStack->hotelStacks[$i]->hotelStacks[$j]);
                                        }
                                        else
                                        {
                                            //echo "in 3";
                                            foreach ($hotelStack->hotelStacks[$i]->hotelStacks[$j]->hotelStacks as $k => $hotelStackDistance)
                                            {
                                                //echo "distance: $k";
                                                if (($k > appParams('HotelBook.distanceFromCityCenter')))
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
                            if ($haveStack)
                            {
                                $jsonObject = $hotelStack->sortBy('rubPrice',5)->getHotel()->getJsonObject();
                                $jsonObject['cityId'] = $hotelSearchParams->city->id;
                                $jsonObject['dateFrom'] = $hotelSearchParams->checkIn;
                                $from = DateTime::createFromFormat('Y-m-d', $hotelSearchParams->checkIn);
                                $jsonObject['dateTo'] = $from->add(new DateInterval('P'.$hotelSearchParams->duration.'D'))->format('Y-m-d');
                                $hotelCache = new HotelCache();
                                $hotelCache->populateFromJsonObject($jsonObject);
                                $hotelCache->save();
                            }
                            else
                            {
                                //echo "havent";
                                //VarDumper::dump($hotelStack->getJsonObject(5));
                            }
                        }
                    }

                }
            }
        }
        return array('hotels' => $hotels, 'errorsDescriptions' => $errorDescriptions, 'errorStatus' => $errorStatus);
    }

    /**
     * Additional information to hotel
     * @param Hotel $hotel
     */
    public function hotelSearchDetails(Hotel &$hotel)
    {
        $this->synchronize();
        $time = time() + $this->differenceTimestamp;
        $getData = array('login' => Yii::app()->params['HotelBook']['login'], 'time' => $time, 'checksum' => $this->getChecksum($time));

        self::$lastRequestMethod = 'HotelSearchDetails';
        self::$lastRequestDescription = "SID:{$hotel->searchId} RID: {$hotel->resultId} HID: {$hotel->hotelId}";

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<HotelSearchDetailsRequest>
    <HotelSearches>
        <HotelSearch>
            <SearchId>' . $hotel->searchId . '</SearchId>
            <ResultId>' . $hotel->resultId . '</ResultId>
        </HotelSearch>
    </HotelSearches>
</HotelSearchDetailsRequest>';

        $hotelXml = $this->request(Yii::app()->params['HotelBook']['uri'] . 'hotel_search_details', $getData, array('request' => $xml));
        $hotelObject = simplexml_load_string($hotelXml);
        if (isset($hotelObject->HotelSearchDetails->Hotel->ChargeConditions))
        {
            $currency = (string)$hotelObject->HotelSearchDetails->Hotel->ChargeConditions->Currency;
            UtilsHelper::soapObjectsArray($hotelObject->HotelSearchDetails->Hotel->ChargeConditions->Cancellations->Cancellation);
            foreach ($hotelObject->HotelSearchDetails->Hotel->ChargeConditions->Cancellations->Cancellation as $cancelSXE)
            {
                $cancelParams = array();
                $cancelParams['charge'] = (string)$cancelSXE['charge'];
                $cancelParams['denyChanges'] = (string)$cancelSXE['denyChanges'];
                if ($cancelSXE['from'])
                {
                    $cancelParams['from'] = (string)$cancelSXE['from'];
                }
                if ($cancelSXE['to'])
                {
                    $cancelParams['to'] = (string)$cancelSXE['to'];
                }
                if ($cancelSXE['price'])
                {
                    $cancelParams['price'] = (string)$cancelSXE['price'];
                }
                $hotel->addCancelCharge($cancelParams);
            }
        }
        //VarDumper::dump($hotelObject);
        //echo $hotelsXml;
    }

    public function hotelSearchFullDetails(HotelSearchParams $hotelSearchParams,$hotelId,$hotels = null)
    {
        $rooms = $hotelSearchParams->rooms;
        //Make combinations to combinations Array
        //uasort($rooms,'HotelBookClient::compareArrayAdultCount');
        $maxAdultCount = 0;
        foreach($rooms as $room)
        {
            $count = $room['adultCount'] + $room['childCount'];
            if($count > $maxAdultCount)
            {
                $maxAdultCount = $count;
            }
        }
        if($maxAdultCount > 4) $maxAdultCount = 4;

        self::$groupId = substr(md5(uniqid('',true)),0,10);
        $i = 1;
        $j = 0;
        $end = false;
        $reqs = array();
        while(!$end)
        {
            $params = array('cityId'=>$hotelSearchParams->city->hotelbookId,'checkIn'=>$hotelSearchParams->checkIn,'duration'=>$hotelSearchParams->duration, 'hotelId'=>$hotelId);
            $params['rooms'] = array();
            $params['rooms'][] = array('roomSizeId'=>self::$roomSizeRoomTypesMap[$i][$j], 'child'=>0, 'cots'=>0, 'roomNumber'=>1);
            $reqs[] = self::hotelSearch($params, true);
            if(count(self::$roomSizeRoomTypesMap[$i]) > ($j+1))
            {
                $j++;
            }
            elseif($i < $maxAdultCount)
            {
                $i++;
                $j = 0;
            }
            else
            {
                $end = true;
            }
        }

        //run all requests
        $this->processAsyncRequests();

        //$hotelStacks = array();
        $hotels = array();

        foreach($reqs as $requestId)
        {
            $hotelStack = new HotelStack();

            if($this->requests[$requestId]['result']->hotels)
            {
                //print_r($this->requests[$requestId]['result']);die();
                foreach($this->requests[$requestId]['result']->hotels as $hotel)
                {
                    $hotelStack->addHotel($hotel);
                }
                foreach($hotelStack->_hotels as $key=>$hotel)
                {
                    if(!isset($hotels[$key]))
                    {
                        $hotels[$key] = $hotel;
                    }
                }
            }
            //$hotelStacks[] = $hotelStack;
        }
        return $hotels;

    }

    public function processHotelDetail($hotelDetailXml)
    {
        $hotelObject = simplexml_load_string($hotelDetailXml);
        //VarDumper::dump($hotelsObject);
        $hotelSXE = $hotelObject->HotelDetail;


        /*
         * <RoomService24h>круглосуточное обслуживание</RoomService24h>
        <PorterageFrom>начало работы носильщиков</PorterageFrom>
        <PorterageTo>окончание работы носильщиков</PorterageTo>
        <Porterage24h>круглосуточная работа носильщиков</Porterage24h>
        <IndoorPool>закрытые бассейны</IndoorPool>
        <OutdoorPool>открытые бассейны</OutdoorPool>
        <ChildrensPool>детские бассейны</ChildrensPool>
        <Description>описание отеля</Description>
        <Distances>расстояния</Distances>
        <HotelFacility>
            <Facility id="...">услуга</Facility> - список услуг отеля
        </HotelFacility>
        <RoomAmenity>
            <Amenity id="...">удобство</Amenity> - список удобств номера
        </RoomAmenity>
        <HotelType>
            <Type id="..">тип отеля</Type>
        </HotelType>
        <Images> - список фотографий отеля и его внутренних помещений
            <Image>
                <Info>описание фотографии</Info>
                <Small width="..." height="...">url фотографии маленького размера</Small>
                <Large width="..." height="...">url фотографии большого размера</Large>
            </Image>
        </Images>
        <GTAHotelCode>код отеля</GTAHotelCode>
        <GTACityCode>код города</GTACityCode>
        <Updated>дата обновления</Updated>
         */
        $hotelAttrMap = array(
            'address' => 'Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'phone' => 'Phone',
            'fax' => 'Fax',
            'email' => 'Email',
            'site' => 'WWW',
            'builtIn' => 'BuiltIn',
            'buildingType' => 'BuildingType',
            'numberLifts' => 'NumberLifts',
            'numberFloors' => 'NumberFloors',
            'conference' => 'Conference',
            'voltage' => 'Voltage',
            'childAgeFrom' => 'ChildAgeFrom',
            'childAgeTo' => 'ChildAgeTo',
            'classification' => 'Classification',
            'earliestCheckInTime' => 'EarlestCheckInTime',
            'roomServiceFrom' => 'RoomServiceFrom',
            'roomServiceTo' => 'RoomServiceTo',
            'roomService24h' => 'RoomService24h',
            'porterageFrom' => 'PorterageFrom',
            'porterageTo' => 'PorterageTo',
            'porterage24h' => 'Porterage24h',
            'indoorPool' => 'IndoorPool',
            'outdoorPool' => 'OutdoorPool',
            'childrenPool' => 'ChildrensPool',
            'description' => 'Description',
            'distances' => 'Distances',
            'gtaHotelCode' => 'GTAHotelCode',
            'gtaCityCode' => 'GTACityCode',
            'updated' => 'Updated'
        );
        $roomAttrMap = array(
            'mealId', 'mealName', 'mealBreakfastId', 'mealBreakfastName', 'sharingBedding',
            'sizeId' => 'roomSizeId',
            'sizeName' => 'roomSizeName',
            'typeId' => 'roomTypeId',
            'typeName' => 'roomTypeName',
            'viewId' => 'roomViewId',
            'viewName' => 'roomViewName',
            'cotsCount' => 'cots',
        );


        $hotelParams = array();
        //$hotelParams['searchId'] = $searchId;
        foreach ($hotelAttrMap as $paramKey => $itemKey)
        {
            if (isset($hotelSXE[$itemKey]))
            {

                $hotelParams[$paramKey] = (string)$hotelSXE[$itemKey];
            }
            elseif(isset($hotelSXE->{$itemKey}))
            {
                $hotelParams[$paramKey] = (string)$hotelSXE->{$itemKey};
            }
        }
        if (isset($hotelSXE->Images->Image))
        {
            $hotelParams['images'] = array();
            UtilsHelper::soapObjectsArray($hotelSXE->Images->Image);
            foreach ($hotelSXE->Images->Image as $imageSXE)
            {
                if((int)$imageSXE->Small['width'])
                {
                    $hotelParams['images'][] = array('description'=>(string)$imageSXE->Info, 'smallUrl'=>(string)$imageSXE->Small, 'largeUrl'=>(string)$imageSXE->Large);
                }
            }
        }
        if (isset($hotelSXE->HotelFacility->Facility))
        {
            $hotelParams['facilities'] = array();
            UtilsHelper::soapObjectsArray($hotelSXE->HotelFacility->Facility);
            foreach ($hotelSXE->HotelFacility->Facility as $facilitySXE)
            {
                $hotelParams['facilities'][] = (string)$facilitySXE;
            }
        }
        if (isset($hotelSXE->RoomAmenity->Amenity))
        {
            $hotelParams['roomAmenities'] = array();
            UtilsHelper::soapObjectsArray($hotelSXE->HotelFacility->Facility);
            foreach ($hotelSXE->RoomAmenity->Amenity as $amenitySXE)
            {
                $hotelParams['roomAmenities'][] = (string)$amenitySXE;
            }
        }
        if ($hotelSXE->Cat['id'])
        {
            $categoryId = (int)$hotelSXE->Cat['id'];
            $hotelParams['categoryId'] = isset(Hotel::$categoryIdMapHotelbook[$categoryId]) ? Hotel::$categoryIdMapHotelbook[$categoryId]  : Hotel::STARS_UNDEFINDED;
        }
        if ($hotelSXE->City['id'])
        {

            $hotelParams['city'] = City::getCityByHotelbookId((string)$hotelSXE->City['id']);
        }
        //VarDumper::dump($hotelSXE);
        //VarDumper::dump($hotelParams);
        return new HotelInfo($hotelParams);
    }

    public function hotelDetail($hotelId, $async = false)
    {
        $this->synchronize();
        $time = time() + $this->differenceTimestamp;
        $getData = array('login' => Yii::app()->params['HotelBook']['login'], 'time' => $time, 'checksum' => $this->getChecksum($time), 'hotel_id' => $hotelId);
        self::$lastRequestMethod = 'HotelDetail';
        self::$lastRequestDescription = $hotelId;
        if ($async)
        {
            $asyncParams = array('function' => array($this, 'processHotelDetail'));
            return $this->request(Yii::app()->params['HotelBook']['uri'] . 'hotel_detail', $getData, null, $asyncParams);

        }
        else
        {


            $hotelDetailXml = $this->request(Yii::app()->params['HotelBook']['uri'] . 'hotel_detail', $getData);

            //CTextHighlighter::registerCssFile();


            //echo $hotelsXml;
            //die();
            //VarDumper::xmlDump($hotelsXml);
            //die();
            return $this->processHotelDetail($hotelDetailXml);
        }
    }

    public function checkHotel($hotel)
    {
        $hotelBookClient = $this;
        $searchParams = array();
        $hotelKey = $hotel->key;
        $searchParams['cityId'] = $this->hotel->cityId;
        $searchParamsFull = Yii::app()->cache->get('hotelSearchParams'.Yii::app()->user->getState('avia.cacheId'));
        $searchParams['checkIn'] = $searchParamsFull->checkIn;
        $searchParams['duration'] = $searchParamsFull->duration;
        $searchParams['rooms'] = array();
        foreach ($this->hotel->rooms as $room)
        {
            $searchParams['rooms'][] = array(
                'roomSizeId' => $room->sizeId,
                'child' => $room->childCount ? $room->childCount : 0,
                'cots' => $room->cotsCount,
                'ChildAge' => isset($room->childAges[0]) ? $room->childAges[0] : 0,
                'roomNumber'=>1
            );
        }
        $hotelSearchResponse = $hotelBookClient->hotelSearch($searchParams);

        $find = false;
        if ($hotelSearchResponse['hotels'])
        {
            foreach ($hotelSearchResponse['hotels'] as $hotel)
            {
                if ($hotel->key == $hotelKey)
                {
                    //$this->hotel = $hotel;
                    $find = true;
                    //$this->hotelBooker->hotel = $this->hotel;
                    break;
                }
            }
        }
        return $find;
    }

    /**
     * @param HotelOrderParams $hotelOrderParams
     * @return HotelOrderResponse
     */
    public function addOrder(HotelOrderParams $hotelOrderParams)
    {
        $this->synchronize();
        $time = time() + $this->differenceTimestamp;
        $getData = array('login' => Yii::app()->params['HotelBook']['login'], 'time' => $time, 'checksum' => $this->getChecksum($time));
        self::$lastRequestMethod = 'HotelOrder';

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<AddOrderRequest>
  <ContactInfo>
    <Name>'.$hotelOrderParams->contactName.'</Name>
    <Email>'.$hotelOrderParams->contactEmail.'</Email>
    <Phone>'.$hotelOrderParams->contactPhone.'</Phone>
    <Comment>'.$hotelOrderParams->contactComment.'</Comment>
  </ContactInfo>
  <Items>
    <HotelItem>
      <Search resultId="'.$hotelOrderParams->hotel->resultId.'" searchId="'.$hotelOrderParams->hotel->searchId.'" />
      <Rooms>
      </Rooms>
    </HotelItem>
  </Items>
</AddOrderRequest>';

        $requestObject = simplexml_load_string($xml);

        if (isset($hotelOrderParams->roomers))
        {

            $lastRoomId = null;

            foreach ($hotelOrderParams->roomers as $roomer)
            {
                if($lastRoomId !== $roomer->roomId)
                {
                    $roomItem = $requestObject->Items->HotelItem->Rooms->addChild('Room');
                }

                $roomPax = $roomItem->addChild('RoomPax');
                if($roomer->age)
                {
                    $roomPax->addAttribute('child', 'true');
                    $roomPax->addAttribute('age', $roomer->age);
                    $roomPax->addChild('Title','Chld');
                }
                else
                {
                    $roomPax->addAttribute('child', 'false');
                    $roomPax->addChild('Title',($roomer->genderId == Passport::GENDER_M ? 'Mr' : 'Ms'));
                }
                $roomPax->addChild('FirstName',$roomer->firstName);
                $roomPax->addChild('LastName',$roomer->lastName);
                $roomPax->addChild('FullName',$roomer->fullName);
                $lastRoomId = $roomer->roomId;

            }

        }
        $xml = $requestObject->asXML();

        self::$lastRequestDescription = '';

        foreach ($hotelOrderParams->hotel->rooms as $room)
        {
            self::$lastRequestDescription .= (self::$lastRequestDescription ? ' & ' : '') . self::$roomSizeIdNamesMap[$room->sizeId] . ($room->cotsCount ? $room->cotsCount . 'COTS' : '') . ($room->childCount ? 'CHLD' . $room->childAges[0] . 'AGE' : '');
        }

        $response = $this->request(Yii::app()->params['HotelBook']['uri'] . 'add_order', $getData, array('request' => $xml));
        $responseObject = simplexml_load_string($response);
        $hotelOrderResponse = new HotelOrderResponse();
        if(isset($responseObject->OrderId))
        {
            $hotelOrderResponse->orderId = (string)$responseObject->OrderId;
        }
        if($hotelOrderResponse->orderId)
        {
            $hotelOrderResponse->error = 0;
        }
        else
        {
            $hotelOrderResponse->error = 1;
        }
        return $hotelOrderResponse;
    }

    public function confirmOrder($orderId)
    {
        $this->synchronize();
        $time = time() + $this->differenceTimestamp;
        $getData = array('login' => Yii::app()->params['HotelBook']['login'], 'time' => $time, 'checksum' => $this->getChecksum($time),'order_id'=>$orderId);
        self::$lastRequestMethod = 'confirmOrder';
        self::$lastRequestDescription = (string)$orderId;
        $response = $this->request(Yii::app()->params['HotelBook']['uri'] . 'confirm_order', $getData);
        $responseObject = simplexml_load_string($response);
        $hotelOrderConfirmResponse = new HotelOrderConfirmResponse();
        $hotelOrderConfirmResponse->orderId = (string)$responseObject->Order->Id;
        $hotelOrderConfirmResponse->tag = (string)$responseObject->Order->Tag;
        $hotelOrderConfirmResponse->orderState = (string)$responseObject->Order->State;
        $hotelOrderConfirmResponse->error = 0;
        if(isset($responseObject->Errors->Error)){
            CVarDumper::dump($responseObject->Errors);
            if(isset($responseObject->Errors->Error['code']))
            {
                if((string)$responseObject->Errors->Error['code'] == 'E1')
                {
                    return $this->OrderInfo($orderId);
                }
                else
                {
                    $hotelOrderConfirmResponse->error = 1;
                }


            }
            else
            {
                $hotelOrderConfirmResponse->error = 1;
            }


        }

        return $hotelOrderConfirmResponse;
    }

    public function OrderInfo($orderId)
    {
        $this->synchronize();
        $time = time() + $this->differenceTimestamp;
        $getData = array('login' => Yii::app()->params['HotelBook']['login'], 'time' => $time, 'checksum' => $this->getChecksum($time),'order_id'=>$orderId);
        self::$lastRequestMethod = 'OrderInfo';
        self::$lastRequestDescription = (string)$orderId;
        $response = $this->request(Yii::app()->params['HotelBook']['uri'] . 'order_info', $getData);
        $responseObject = simplexml_load_string($response);
        $hotelOrderConfirmResponse = new HotelOrderConfirmResponse();
        $hotelOrderConfirmResponse->orderId = (string)$responseObject->Order->Id;
        $hotelOrderConfirmResponse->tag = (string)$responseObject->Order->Tag;
        $hotelOrderConfirmResponse->orderState = (string)$responseObject->Order->State;
        $hotelOrderConfirmResponse->error = 0;
        if(isset($responseObject->Errors->Error)){
            $hotelOrderConfirmResponse->error = 1;
        }

        return $hotelOrderConfirmResponse;
    }

    public function synchronize()
    {
        if (!$this->isSynchronized)
        {
            self::$lastRequestMethod = 'unixtime';


            $diff = Yii::app()->cache->get('hotelbookDifferenceTimestamp');
            if ($diff === false)
            {
                $unixtime = $this->request(Yii::app()->params['HotelBook']['uri'] . 'unix_time');
                $this->differenceTimestamp = $unixtime - time();
                Yii::app()->cache->set('hotelbookDifferenceTimestamp', $this->differenceTimestamp);
            }
            else
            {
                $this->differenceTimestamp = $diff;
            }
            $this->isSynchronized = true;
        }

        //echo "ts:{$unixtime} NN:".date("Y-m-d H:i:s",$unixtime).' NFT:'.date("Y-m-d H:i:s").' NCC:'.date("Y-m-d H:i:s",(time() + $this->differenceTimestamp));

    }
}