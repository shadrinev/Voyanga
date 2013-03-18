<?php

class LandingController extends FrontendController
{
    public $morphy;
    public $breadLinks = array();

    public function actionIndex()
    {
        $this->layout = 'static';
        $this->render('landing');
    }

    public function actionBestHotels()
    {
        $currentCity = Geoip::getCurrentCity();
        $this->breadLinks['Страны'] = '/land/';
        $this->breadLinks['Отели'] = '/land/hotels/';

        $sql = 'SELECT count(*) as cnt, cityId  FROM `hotel`  GROUP BY `cityId` ORDER BY cnt DESC LIMIT 3';
        $cityIds = array();
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        while (($row = $dataReader->read()) !== false) {
            $cityIds[] = $row['cityId'];
        }

        Yii::import('site.common.modules.hotel.models.*');
        $hotelClient = new HotelBookClient();

        $hotelsCaches = array();
        foreach ($cityIds as $cityId) {
            $criteria = new CDbCriteria();
            //$criteria->addInCondition('`cityId`',$cityIds);
            $criteria->addCondition('cityId = ' . $cityId);

            $criteria->limit = 9;

            $hotelCache = HotelDb::model()->findAll($criteria);
            $hotelsInfo = array();
            foreach ($hotelCache as $hc) {
                $hotelInfo = $hotelClient->hotelDetail($hc->id);
                if ($hotelInfo) {
                    $hotelInfo->price = $hc->minPrice;
                    $hotelInfo->hotelName = $hc->name;
                    $hotelInfo->hotelId = $hc->id;
                    $hotelsInfo[] = $hotelInfo;
                }
            }
            $hotelsCaches[$cityId] = $hotelsInfo;
        }

        $flightCacheFromCurrent = array();
        $connection = Yii::app()->db;
        $sql = 'SELECT `from`,`to`,`dateFrom`,`dateBack`,`priceBestPrice` from (SELECT * FROM `flight_cache` WHERE `from` = \'' . $currentCity->id . '\' AND `dateFrom` > \'' . date('Y-m-d') . '\' AND `dateFrom` < \'' . date('Y-m-d', time() + 3600 * 24 * 60) . '\' ORDER BY priceBestPrice) as tbl1 GROUP BY `to` ORDER BY priceBestPrice  limit 14';
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        while (($row = $dataReader->read()) !== false) {
            $flightCacheFromCurrent[] = (object)$row;
        }

        $this->layout = 'static';
        $this->assignTitle('landHotels');
        $this->render('bestHotels', array('hotelsCaches' => $hotelsCaches, 'currentCity' => $currentCity,
            'flightCacheFromCurrent' => $flightCacheFromCurrent
        ));
    }


    public function actionHotels($countryCode = '', $cityCode = '')
    {
        if (!($country = $this->testCountry($countryCode)))
            return false;
        $this->breadLinks['Страны'] = '/land/';
        $this->breadLinks['Отели'] = '/land/hotels/';
        $this->breadLinks[$country->localRu] = '/land/hotels/'.$country->code.'/';
        $this->morphy = Yii::app()->morphy;
        $countryUp = mb_strtoupper($country->localRu, 'utf-8');
        $countryMorph = array('caseAcc' => $this->getCase($countryUp, 'ВН'), 'casePre' => $this->getCase($countryUp, 'ПР'),'caseGen' => $this->getCase($countryUp, 'РД'));
        //if(!$cityFromCode){
        $currentCity = Geoip::getCurrentCity();
        //}else{
        //    $currentCity = City::getCityByCode($cityFromCode);
        //}
        $citySet = false;
        $city = false;
        if ($cityCode) {
            $city = City::getCityByCode($cityCode);
            $citySet = true;
        } else {
            $criteria = new CDbCriteria();
            //$criteria->addInCondition('`cityId`',$cityIds);
            $criteria->addCondition('`countryId` = ' . $country->id);

            $criteria->limit = 1;

            $hc = HotelDb::model()->find($criteria);
            if($hc){
                $city = City::getCityByPk($hc->cityId);
            }
            if(!$city){
                $criteria = new CDbCriteria();
                $criteria->addCondition('`countryId` = ' . $country->id);
                $criteria->addCondition('`hotelbookId` > 0');
                $criteria->order = 'position desc';
                $city = City::model()->find($criteria);
            }
        }
        if (!$city) {
            $city = City::getCityByPk(5185);
        }
        if ($city->id == $currentCity->id) {
            if ($currentCity->id != 4466) {
                $currentCity = City::getCityByPk(4466);
            } elseif ($currentCity->id != 5185) {
                $currentCity = City::getCityByPk(5185);
            }
        }

        $citiesFrom = array();
        $this->addCityFrom($citiesFrom, 4466); // Moscow
        $this->addCityFrom($citiesFrom, 5185); // Spb
        $this->addCityFrom($citiesFrom, $currentCity->id);


        Yii::import('site.common.modules.hotel.models.*');
        $hotelClient = new HotelBookClient();


        $criteria = new CDbCriteria();
        $criteria->addCondition('`to` = ' . $city->id);
        $criteria->addCondition('`from` = ' . $currentCity->id);
        $criteria->group = 'dateFrom';
        $criteria->addCondition('`from` = ' . $currentCity->id);
        $criteria->addCondition('`dateFrom` >= ' . date("'Y-m-d'"));
        $criteria->addCondition('`dateFrom` <= ' . date("'Y-m-d'", time() + 3600 * 24 * 30));
        $criteria->order = 'priceBestPrice';
        //$criteria->limit = 18;

        $flightCache = FlightCache::model()->findAll($criteria);
        $sortFc = array();
        foreach ($flightCache as $fc) {
            $k = strtotime($fc->dateFrom);
            $sortFc[$k] = array('date' => $fc->dateFrom, 'price' => $fc->priceBestPrice);
        }
        ksort($sortFc);
        $sortFc = array_slice($sortFc, 0, 18);

        //print_r($flightCache);die();

        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`cityId`',$cityIds);
        $criteria->addCondition('cityId = ' . $city->id);

        $criteria->limit = 15;

        $hotelCache = HotelDb::model()->findAll($criteria);
        $hotelsInfo = array();
        foreach ($hotelCache as $hc) {
            $hotelInfo = $hotelClient->hotelDetail($hc->id);
            if ($hotelInfo) {
                $hotelInfo->price = $hc->minPrice;
                $hotelInfo->hotelName = $hc->name;
                $hotelInfo->hotelId = $hc->id;
                $hotelsInfo[] = $hotelInfo;
            }
        }

        $flightCacheFromCurrent = array();
        $connection = Yii::app()->db;
        $sql = 'SELECT `from`,`to`,`dateFrom`,`dateBack`,`priceBestPrice` from (SELECT * FROM `flight_cache` WHERE `from` = \'' . $currentCity->id . '\' AND `dateFrom` > \'' . date('Y-m-d') . '\' AND `dateFrom` < \'' . date('Y-m-d', time() + 3600 * 24 * 60) . '\' ORDER BY priceBestPrice) as tbl1 GROUP BY `to` ORDER BY priceBestPrice  limit 14';
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        while (($row = $dataReader->read()) !== false) {
            $flightCacheFromCurrent[] = (object)$row;
        }


        $this->layout = 'static';
        $countryCities = false;
        if($citySet){
            $this->breadLinks['в '.$city->casePre] = '/land/hotels/'.$country->code.'/'.$city->code;
            $this->assignTitle('landHotelsCity',array('##cityCasePre##'=>$city->casePre,'##cityCaseAcc##'=>$city->caseAcc,'##countryCaseGen##'=>$countryMorph['caseGen'],'##countryCaseAcc##'=>$countryMorph['caseAcc']));
        }else{
            $criteria = new CDbCriteria();
            $criteria->addCondition('`countryId` = ' . $country->id);
            $criteria->addCondition('`hotelbookId` > 0');
            $criteria->order = 'position desc';
            $countryCities = City::model()->findAll($criteria);
            $this->assignTitle('landHotelsCountry',array('##countryCaseGen##'=>$countryMorph['caseGen'],'##countryCaseAcc##'=>$countryMorph['caseAcc']));
        }
        $this->render('hotels', array('countryCities'=>$countryCities,'city' => $city, 'citySet' => $citySet, 'countryMorph' => $countryMorph, 'citiesFrom' => $citiesFrom, 'hotelsInfo' => $hotelsInfo, 'currentCity' => $currentCity, 'flightCache' => $sortFc,
            'flightCacheFromCurrent' => $flightCacheFromCurrent
        ));
    }

    public function actionHotelInfo($hotelId, $update = '')
    {
        Yii::import('site.common.modules.hotel.models.*');
        $hotelClient = new HotelBookClient();

        $this->breadLinks['Страны'] = '/land/';
        $this->breadLinks['Отели'] = '/land/hotels/';


        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`cityId`',$cityIds);
        $criteria->addCondition('id = ' . $hotelId);


        $hc = HotelDb::model()->find($criteria);
        HotelBookClient::$updateProcess = true;
        HotelBookClient::$downCountCacheFill = 2;
        if ($update === 'yes') {
            $cachePath = Yii::getPathOfAlias('cacheStorage');
            $cacheSubDir = md5('HotelDetail' . $hotelId);
            $cacheSubDir = substr($cacheSubDir, -3);
            $cacheFilePath = $cachePath . '/' . $cacheSubDir . '/HotelDetail' . $hotelId . '.xml';
            if (file_exists($cacheFilePath)) {
                unlink($cacheFilePath);
            }
        }
        $hotelsInfo = array();
        $city = false;
        if ($hc) {
            $hotelInfo = $hotelClient->hotelDetail($hc->id);
            $hotelInfo->price = $hc->minPrice;
            $hotelInfo->hotelName = $hc->name;
            $hotelInfo->hotelId = $hc->id;
            $city = City::getCityByPk($hc->cityId);
        } else {
            $hotelInfo = $hotelClient->hotelDetail($hotelId);
            if ($hotelInfo->city) {
                $city = City::getCityByHotelbookId($hotelInfo->city);
            }else{
                //print_r($hotelInfo);
                //die();
            }
        }
        if ($city) {
            $rating = new HotelRating();
            $ratings = $rating->findByNames(array($hotelInfo->hotelName), $city);
            if ($ratings) {
                foreach ($ratings as $rate) break;
                $hotelInfo->rating = $rate;
            }
            $country = $city->country;
            $this->breadLinks[$country->localRu] = '/land/hotels/'.$country->code.'/';
            $this->breadLinks[$city->caseNom] = '/land/hotels/'.$country->code.'/'.$city->casePre;
        }
        $serviceGroupIcons = array('Сервис' => 'service', 'Спорт и отдых' => 'sport', 'Туристам' => 'turist', 'Интернет' => 'internet', 'Развлечения и досуг' => 'dosug', 'Парковка' => 'parkovka', 'Дополнительно' => 'dop', 'В отеле' => 'in-hotel');
        $serviceList = array();
        if ($hotelInfo->hotelGroupServices) {
            foreach ($hotelInfo->hotelGroupServices as $grName => $group) {
                $serviceList[] = array('name' => $grName, 'icon' => $serviceGroupIcons[$grName], 'elements' => $group);
            }
        }
        //print_r($serviceList);die();
        $this->breadLinks[$hotelInfo->hotelName] = '/land/hotel/'.$hotelId;

        $this->layout = 'static';
        $this->assignTitle('landHotel',array('##hotelName##'=>$hotelInfo->hotelName,'##wordStars##'=>UtilsHelper::WordAfterNum(array('звезда','здезды','звезд'),$hotelInfo->categoryId)));
        $this->render('hotelInfo', array('hotelInfo' => $hotelInfo, 'city' => $city, 'serviceList' => $serviceList
        ));
    }

    private function testCountry($countryCode)
    {
        if ($countryCode) {
            try {
                $country = Country::getCountryByCode($countryCode);
            } catch (CException $e) {
                $this->layout = 'static';
                $this->render('notfound');
                return false;
            }
            return $country;
        } else {
            return false;
        }
    }

    public function actionCity($countryCode = '', $cityCode = '', $cityFromCode = '')
    {

        if (!($country = $this->testCountry($countryCode)))
            return false;

        $this->breadLinks['Страны'] = '/land/';
        $this->breadLinks[$country->localRu] = '/land/'.$country->code.'/';

        $this->morphy = Yii::app()->morphy;
        $countryUp = mb_strtoupper($country->localRu, 'utf-8');
        $countryMorph = array('caseAcc' => $this->getCase($countryUp, 'ВН'));
        $fromCity = false;
        if (!$cityFromCode) {
            $currentCity = Geoip::getCurrentCity();
        } else {
            $currentCity = Geoip::getCurrentCity(); //City::getCityByCode($cityFromCode);
            $fromCity = City::getCityByCode($cityFromCode);
        }
        $city = City::getCityByCode($cityCode);
        $this->breadLinks['в '.$city->caseAcc] = '/land/'.$country->code.'/'.$city->code.'/trip/OW/';

        if ($city->id == $currentCity->id) {
            if ($currentCity->id != 4466) {
                $currentCity = City::getCityByPk(4466);
            } elseif ($currentCity->id != 5185) {
                $currentCity = City::getCityByPk(5185);
            }
        }

        $citiesFrom = array();

        if ($city->id != 4466) {
            $this->addCityFrom($citiesFrom, 4466); // Moscow
        }
        if ($city->id != 5185) {
            $this->addCityFrom($citiesFrom, 5185); // Spb
        }
        if ($currentCity->id != $city->id) {
            $this->addCityFrom($citiesFrom, $currentCity->id);
        }
        if ($fromCity) {
            if ($fromCity->id != $city->id) {
                $this->addCityFrom($citiesFrom, $fromCity->id);
            }
        }


        Yii::import('site.common.modules.hotel.models.*');
        $hotelClient = new HotelBookClient();

        foreach($citiesFrom as $cityId=>$cityFromInfo){
            $criteria = new CDbCriteria();
            $criteria->addCondition('`to` = ' . $city->id);
            /*if ($fromCity) {
                $criteria->addCondition('`from` = ' . $fromCity->id);
            } else {
                $criteria->addCondition('`from` = ' . $currentCity->id);
            }*/
            $criteria->group = 'dateFrom';
            $criteria->addCondition('`from` = ' . $cityId);
            $criteria->addCondition('`dateFrom` >= ' . date("'Y-m-d'"));
            $criteria->addCondition('`dateFrom` <= ' . date("'Y-m-d'", time() + 3600 * 24 * 17));
            $criteria->addCondition('`dateBack` = \'0000-00-00\'' );
            $criteria->order = 'priceBestPrice';
            //$criteria->limit = 18;

            $flightCache = FlightCache::model()->findAll($criteria);
            $sortFc = array();
            foreach ($flightCache as $fc) {
                $k = strtotime($fc->dateFrom);
                $sortFc[$k] = array('date' => $fc->dateFrom, 'price' => $fc->priceBestPrice);
            }
            ksort($sortFc);
            $sortFc = array_slice($sortFc, 0, 18);
            $citiesFrom[$cityId]['flightCache'] = $sortFc;

            //select bast price for all future time
            $criteria = new CDbCriteria();
            $criteria->addCondition('`to` = ' . $city->id);

            $criteria->addCondition('`from` = ' . $cityId);

            $criteria->addCondition('`dateFrom` >= ' . date("'Y-m-d'"));
            $criteria->addCondition('`dateBack` = \'0000-00-00\'' );

            $criteria->order = 'priceBestPrice';

            $flightCacheBestPrice = FlightCache::model()->find($criteria);
            if ($flightCacheBestPrice) {
                $flightCacheBestPriceArr = array('price' => $flightCacheBestPrice->priceBestPrice, 'date' => $flightCacheBestPrice->dateFrom);
            } else {
                $flightCacheBestPriceArr = array();
            }
            $citiesFrom[$cityId]['flightCacheBestPriceArr'] = $flightCacheBestPriceArr;
        }




        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`cityId`',$cityIds);
        $criteria->addCondition('cityId = ' . $city->id);

        $criteria->limit = 15;

        $hotelCache = HotelDb::model()->findAll($criteria);
        $hotelsInfo = array();
        foreach ($hotelCache as $hc) {
            $hotelInfo = $hotelClient->hotelDetail($hc->id);
            if ($hotelInfo) {
                $hotelInfo->price = $hc->minPrice;
                $hotelInfo->hotelName = $hc->name;
                $hotelInfo->hotelId = $hc->id;
                $hotelsInfo[] = $hotelInfo;
            }
        }

        $flightCacheFromCurrent = array();
        $connection = Yii::app()->db;
        $sql = 'SELECT `from`,`to`,`dateFrom`,`dateBack`,`priceBestPrice` from (SELECT * FROM `flight_cache` WHERE `from` = \'' . $currentCity->id . '\' AND `dateFrom` > \'' . date('Y-m-d') . '\' AND `dateFrom` < \'' . date('Y-m-d', time() + 3600 * 24 * 60) . '\' ORDER BY priceBestPrice) as tbl1 GROUP BY `to` ORDER BY priceBestPrice  limit 14';
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        while (($row = $dataReader->read()) !== false) {
            $flightCacheFromCurrent[] = (object)$row;
        }


        $this->layout = 'static';
        if($fromCity){
            $this->breadLinks['из '.$fromCity->caseGen] = '/land/'.$country->code.'/'.$fromCity->code.'/'.$city->code.'/trip/OW/';
            $this->assignTitle('landFromTo',array('##cityFrom##'=>$fromCity->caseNom,'##cityTo##'=>$city->caseNom,'##cityFromCaseGen##'=>$fromCity->caseGen,'##cityToCaseAcc##'=>$city->caseAcc));
        }else{
            $this->assignTitle('landTo',array('##cityTo##'=>$city->caseNom,'##cityToCaseAcc##'=>$city->caseAcc));
        }
        $this->render('city', array('city' => $city, 'citiesFrom' => $citiesFrom, 'hotelsInfo' => $hotelsInfo, 'fromCity' => $fromCity, 'currentCity' => $currentCity,
            'flightCacheFromCurrent' => $flightCacheFromCurrent
        ));
    }

    private function getCase($word, $case)
    {
        $info = $this->morphy->castFormByGramInfo($word, 'С', array($case, 'ЕД'), false);
        if (isset($info[0]))
            return $this->mb_ucwords($info[0]['form']);
        return $this->mb_ucwords($word);
    }

    function mb_ucwords($str)
    {
        $str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
        return ($str);
    }

    private function addCityFrom(&$cityFromArray, $cityFromId)
    {
        if (!isset($cityFromArray[$cityFromId])) {
            $city = City::getCityByPk($cityFromId);
            $cityFromArray[$cityFromId] = array('cityId' => $cityFromId, 'cityCode' => $city->code, 'cityName' => $city->localRu, 'cityAcc' => $city->caseAcc);
        }
    }

    public function actionCountry($countryCode = '')
    {
        if (!($country = $this->testCountry($countryCode)))
            return false;
        $this->breadLinks['Страны'] = '/land/';
        $this->breadLinks[$country->localRu] = '/land/'.$country->code.'/';

        $this->morphy = Yii::app()->morphy;
        $countryUp = mb_strtoupper($country->localRu, 'utf-8');
        $countryMorph = array('caseAcc' => $this->getCase($countryUp, 'ВН'), 'casePre' => $this->getCase($countryUp, 'ПР'), 'caseGen' => $this->getCase($countryUp, 'РД'));
        $currentCity = Geoip::getCurrentCity();

        $citiesFrom = array();
        $this->addCityFrom($citiesFrom, 4466); // Moscow
        $this->addCityFrom($citiesFrom, 5185); // Spb
        $this->addCityFrom($citiesFrom, $currentCity->id);


        Yii::import('site.common.modules.hotel.models.*');
        $hotelClient = new HotelBookClient();

        $criteria = new CDbCriteria();
        $criteria->addCondition('countAirports > 0');
        $criteria->addCondition('countryId = ' . $country->id);
        $cities = City::model()->findAll($criteria);
        $cityIds = array();
        foreach ($cities as $city) {
            $cityIds[$city->id] = "'" . $city->id . "'";
        }

        $connection = Yii::app()->db;
        $city = false;

        foreach($citiesFrom as $cityId=>$cityFromInfo){
            $flightCache = array();
            $tmpCityIds = $cityIds;
            if (isset($cityIds[$cityId])) {
                unset($cityIds[$cityId]);
            }
            if ($tmpCityIds) {
                $sql = 'SELECT `from`,`to`,`dateFrom`,`dateBack`,`priceBestPrice` from (SELECT * FROM `flight_cache` WHERE `from` = \'' . $cityId . '\' AND `to` IN (' . implode(',', $tmpCityIds) . ') AND `dateFrom` > \'' . date('Y-m-d') . '\' AND `dateFrom` < \'' . date('Y-m-d', time() + 3600 * 24 * 60) . '\' ORDER BY priceBestPrice) as tbl1 GROUP BY `to` ORDER BY priceBestPrice  limit 10';
                $command = $connection->createCommand($sql);
                $dataReader = $command->query();
                while (($row = $dataReader->read()) !== false) {
                    $flightCache[] = (object)$row;
                    if(!$city){
                        if(!isset($citiesFrom[$row['to']])){
                            $city = City::getCityByPk($row['to']);
                        }
                    }
                }
            }
            $citiesFrom[$cityId]['flightCache'] = $flightCache;
        }
        if(!$city){
            $sql = "SELECT * FROM city WHERE `countAirports` > 0 AND `countryId` = {$country->id} ORDER BY countAirports desc  limit 1";
            $command = $connection->createCommand($sql);
            $row = $command->queryRow();
            if($row){
                $city = new City;
                $city->populateRecord($row);
            }
        }



        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`cityId`',$cityIds);
        $criteria->addCondition('countryId = ' . $country->id);

        $criteria->limit = 15;

        $hotelCache = HotelDb::model()->findAll($criteria);
        $hotelsInfo = array();
        foreach ($hotelCache as $hc) {
            $hotelInfo = $hotelClient->hotelDetail($hc->id);
            if ($hotelInfo) {
                $hotelInfo->price = $hc->minPrice;
                $hotelInfo->hotelName = $hc->name;
                $hotelInfo->hotelId = $hc->id;
                $hotelsInfo[] = $hotelInfo;
            }
        }
        //echo 'count'.count($hotelCache);

        $flightCacheFromCurrent = array();
        $connection = Yii::app()->db;
        $sql = 'SELECT `from`,`to`,`dateFrom`,`dateBack`,`priceBestPrice` from (SELECT * FROM `flight_cache` WHERE `from` = \'' . $currentCity->id . '\' AND `dateFrom` > \'' . date('Y-m-d') . '\' AND `dateFrom` < \'' . date('Y-m-d', time() + 3600 * 24 * 60) . '\' ORDER BY priceBestPrice) as tbl1 GROUP BY `to` ORDER BY priceBestPrice  limit 14';
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        while (($row = $dataReader->read()) !== false) {
            $flightCacheFromCurrent[] = (object)$row;
        }
        $criteria = new CDbCriteria();
        $criteria->addCondition('`countryId` = ' . $country->id);
        $criteria->addCondition('`countAirports` > 0');
        $criteria->order = 'position desc';
        $countryCities = City::model()->findAll($criteria);

        $this->layout = 'static';
        $this->assignTitle('landToCountry',array('##countryCaseGen##'=>$countryMorph['caseGen'],'##countryCaseAcc##'=>$countryMorph['caseAcc']));
        $this->render('country', array('countryCities'=>$countryCities,'country'=> $country, 'countryMorph' => $countryMorph, 'flightCacheFromCurrent' => $flightCacheFromCurrent, 'hotelsInfo' => $hotelsInfo,
            'currentCity' => $currentCity,
            'city'=>$city,
            'citiesFrom' => $citiesFrom
        ));
    }


    public function actionCountries()
    {
        $this->breadLinks['Страны'] = '/land/';
        $countries = Country::model()->findAll();
        $this->layout = 'static';
        $this->assignTitle('landCountries');
        $this->render('countries', array('breadLinks'=>$this->breadLinks,'countries' => $countries));
    }

    public function actionOWFlight($countryCode = '', $cityCodeFrom = '', $cityCodeTo = '')
    {
        $this->actionCity($countryCode, $cityCodeTo, $cityCodeFrom);
    }

    public function actionRTFlight($countryCode = '', $cityCodeFrom = '', $cityCodeTo = '')
    {
        if (!($country = $this->testCountry($countryCode)))
            return false;
        $this->breadLinks['Страны'] = '/land/';
        $this->breadLinks[$country->localRu] = '/land/'.$country->code.'/';

        $this->morphy = Yii::app()->morphy;
        $countryUp = mb_strtoupper($country->localRu, 'utf-8');
        $countryMorph = array('caseAcc' => $this->getCase($countryUp, 'ВН'));

        $fromCity = false;
        if (!$cityCodeFrom) {
            $currentCity = Geoip::getCurrentCity();
        } else {
            $currentCity = Geoip::getCurrentCity(); //City::getCityByCode($cityFromCode);
            $fromCity = City::getCityByCode($cityCodeFrom);
        }
        $city = City::getCityByCode($cityCodeTo);
        $this->breadLinks['в '.$city->caseAcc] = '/land/'.$country->code.'/'.$city->code.'/';

        if ($city->id == $currentCity->id) {
            if ($currentCity->id != 4466) {
                $currentCity = City::getCityByPk(4466);
            } elseif ($currentCity->id != 5185) {
                $currentCity = City::getCityByPk(5185);
            }
        }

        $citiesFrom = array();
        //if (!$fromCity) {
            if ($city->id != 4466) {
                $this->addCityFrom($citiesFrom, 4466); // Moscow
            }
            if ($city->id != 5185) {
                $this->addCityFrom($citiesFrom, 5185); // Spb
            }
            if ($currentCity->id != $city->id) {
                $this->addCityFrom($citiesFrom, $currentCity->id);
            }
        //}
        if ($fromCity) {
            if ($fromCity->id != $city->id) {
                $this->addCityFrom($citiesFrom, $fromCity->id);
            }
        }


        Yii::import('site.common.modules.hotel.models.*');
        $hotelClient = new HotelBookClient();

        /*$criteria = new CDbCriteria();
        $criteria->addCondition('countAirports > 0');
        $criteria->addCondition('countryId = '.$country->id);
        $cities = City::model()->findAll($criteria);
        $cityIds = array();
        foreach($cities as $city){
            $cityIds[$city->id] = $city->id;
        }

        if(isset($cityIds[$currentCity->id])){
            unset($cityIds[$currentCity->id]);
        }*/


        foreach($citiesFrom as $cityId=>$cityFromInfo){
            // selection flight cache for show best price grafik
            $criteria = new CDbCriteria();
            $criteria->addCondition('`to` = ' . $city->id);
            $criteria->addCondition('`from` = ' . $cityId);
            //$criteria->group = 'dateBack';
            //$criteria->addCondition('`from` = '.$currentCity->id);
            $criteria->addCondition('`dateFrom` >= ' . date("'Y-m-d'"));
            $criteria->addCondition('`dateFrom` <= ' . date("'Y-m-d'", time() + 3600 * 24 * 17));
            $criteria->addCondition('`dateBack` >= ' . date("'Y-m-d'"));
            $criteria->addCondition('`dateBack` <= ' . date("'Y-m-d'", time() + 3600 * 24 * 17));

            //$criteria->addCondition("`dateBack` <> '0000-00-00'");
            $criteria->order = 'priceBestPrice';
            //$criteria->limit = 18;

            $flightCache = FlightCache::model()->findAll($criteria);

            $minPrice = false;
            $maxPrice = false;
            $activeMin = null;
            $activeMax = null;
            foreach ($flightCache as $k => $fc) {
                $k = strtotime($fc->dateFrom);
                if (!$minPrice) {
                    $minPrice = $fc->priceBestPrice;
                    $maxPrice = $fc->priceBestPrice;
                } else {
                    if ($fc->priceBestPrice < $minPrice) {
                        $minPrice = $fc->priceBestPrice;
                        $activeMin = $k;
                    }
                    if ($fc->priceBestPrice > $maxPrice) {
                        $maxPrice = $fc->priceBestPrice;
                        $activeMax = $k;
                    }
                }
            }
            $sortFc = array();
            foreach ($flightCache as $fc) {
                //$k = strtotime($fc->dateFrom);
                $sortFc[] = array('price' => $fc->priceBestPrice, 'date' => $fc->dateFrom, 'dateBack' => $fc->dateBack);
            }
            $citiesFrom[$cityId]['flightCache'] = $sortFc;
            //print_r($flightCache);die();


            //select bast price for all future time
            $criteria = new CDbCriteria();
            $criteria->addCondition('`to` = ' . $city->id);
            $criteria->addCondition('`from` = ' . $cityId);

            $criteria->addCondition('`dateFrom` >= ' . date("'Y-m-d'"));
            $criteria->addCondition('`dateBack` >= ' . date("'Y-m-d'"));

            $criteria->order = 'priceBestPrice';

            $flightCacheBestPrice = FlightCache::model()->find($criteria);
            if($flightCacheBestPrice){
                $flightCacheBestPriceArr = array('price' => $flightCacheBestPrice->priceBestPrice, 'date' => $flightCacheBestPrice->dateFrom, 'dateBack' => $flightCacheBestPrice->dateBack);
            }else{
                $flightCacheBestPriceArr = array();
            }
            $citiesFrom[$cityId]['flightCacheBestPriceArr'] = $flightCacheBestPriceArr;
        }



        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`cityId`',$cityIds);
        $criteria->addCondition('cityId = ' . $city->id);

        $criteria->limit = 15;

        //print_r($criteria);
        $hotelCache = HotelDb::model()->findAll($criteria);
        $hotelsInfo = array();
        foreach ($hotelCache as $hc) {
            $hotelInfo = $hotelClient->hotelDetail($hc->id);
            if ($hotelInfo) {
                $hotelInfo->price = $hc->minPrice;
                $hotelInfo->hotelName = $hc->name;
                $hotelInfo->hotelId = $hc->id;
                $hotelsInfo[] = $hotelInfo;
            }
        }

        /*$criteria = new CDbCriteria();
        //$criteria->addInCondition('`to`',$cityIds);
        $criteria->addCondition('`from` = ' . $currentCity->id);
        $criteria->addCondition('`dateFrom` > \'' . date('Y-m-d') . "'");
        $criteria->order = 'priceBestPrice';
        $criteria->limit = 14;
        $criteria->group = '`to`';

        $flightCacheFromCurrent = FlightCache::model()->findAll($criteria);
        print_r($flightCacheFromCurrent);*/
        $flightCacheFromCurrent = array();
        $connection = Yii::app()->db;
        $sql = 'SELECT `from`,`to`,`dateFrom`,`dateBack`,`priceBestPrice` from (SELECT * FROM `flight_cache` WHERE `from` = \'' . $currentCity->id . '\' AND `dateFrom` > \'' . date('Y-m-d') . '\' AND `dateFrom` < \'' . date('Y-m-d', time() + 3600 * 24 * 60) . '\' ORDER BY priceBestPrice) as tbl1 GROUP BY `to` ORDER BY priceBestPrice  limit 14';
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        while (($row = $dataReader->read()) !== false) {
            $flightCacheFromCurrent[] = (object)$row;
        }


        $this->layout = 'static';
        //$this->render('landing');
        if($fromCity){
            $this->breadLinks['из '.$fromCity->caseGen] = '/land/'.$country->code.'/'.$fromCity->code.'/'.$city->code.'/';
            $this->assignTitle('landFromTo',array('##cityFrom##'=>$fromCity->caseNom,'##cityTo##'=>$city->caseNom,'##cityFromCaseGen##'=>$fromCity->caseGen,'##cityToCaseAcc##'=>$city->caseAcc));
        }else{
            $this->assignTitle('landTo',array('##cityTo##'=>$city->caseNom,'##cityToCaseAcc##'=>$city->caseAcc));
        }
        $this->render('rtflight', array('city' => $city, 'citiesFrom' => $citiesFrom, 'hotelsInfo' => $hotelsInfo, 'fromCity' => $fromCity, 'currentCity' => $currentCity,
            'flightCacheFromCurrent' => $flightCacheFromCurrent,
        ));
    }

}