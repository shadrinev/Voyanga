<?php

class LandingController extends Controller
{
    public $morphy;

    public function actionIndex()
    {
        $this->layout = 'static';
        $this->render('landing');
    }

    public function actionBestHotels()
    {
        $currentCity = Geoip::getCurrentCity();

        $sql = 'Select count(*) as cnt, cityId  from `hotel`  group by `cityId` order by cnt desc limit 3';
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
        $this->render('bestHotels', array('hotelsCaches' => $hotelsCaches, 'currentCity' => $currentCity,
            'flightCacheFromCurrent' => $flightCacheFromCurrent
        ));
    }


    public function actionHotels($countryCode = '', $cityCode = '')
    {
        if (!($country = $this->testCountry($countryCode)))
            return false;

        $this->morphy = Yii::app()->morphy;
        $countryUp = mb_strtoupper($country->localRu, 'utf-8');
        $countryMorph = array('caseAcc' => $this->getCase($countryUp, 'ВН'));
        //if(!$cityFromCode){
        $currentCity = Geoip::getCurrentCity();
        //}else{
        //    $currentCity = City::getCityByCode($cityFromCode);
        //}
        if ($cityCode) {
            $city = City::getCityByCode($cityCode);
        } else {
            $criteria = new CDbCriteria();
            $criteria->addCondition('`countryId` = ' . $country->id);
            $criteria->order = 'position desc';
            $city = City::model()->find($criteria);
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
        $this->render('hotels', array('city' => $city, 'citiesFrom' => $citiesFrom, 'hotelsInfo' => $hotelsInfo, 'currentCity' => $currentCity, 'flightCache' => $sortFc,
            'flightCacheFromCurrent' => $flightCacheFromCurrent
        ));
    }

    public function actionHotelInfo($hotelId, $update = '')
    {
        Yii::import('site.common.modules.hotel.models.*');
        $hotelClient = new HotelBookClient();

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
        if ($hc) {
            $hotelInfo = $hotelClient->hotelDetail($hc->id);
            $hotelInfo->price = $hc->minPrice;
            $hotelInfo->hotelName = $hc->name;
            $hotelInfo->hotelId = $hc->id;
            $city = City::getCityByPk($hc->cityId);
        } else {
            $hotelInfo = $hotelClient->hotelDetail($hotelId);
            if ($hotelInfo->city) {
                $city = $hotelInfo->city;
            }
        }
        if ($city) {
            $rating = new HotelRating();
            $ratings = $rating->findByNames(array($hotelInfo->hotelName), $city);
            if ($ratings) {
                foreach ($ratings as $rate) break;
                $hotelInfo->rating = $rate;
            }
        }
        $serviceGroupIcons = array('Сервис' => 'service', 'Спорт и отдых' => 'sport', 'Туристам' => 'turist', 'Интернет' => 'internet', 'Развлечения и досуг' => 'dosug', 'Парковка' => 'parkovka', 'Дополнительно' => 'dop', 'В отеле' => 'in-hotel');
        $serviceList = array();
        if ($hotelInfo->hotelGroupServices) {
            foreach ($hotelInfo->hotelGroupServices as $grName => $group) {
                $serviceList[] = array('name' => $grName, 'icon' => $serviceGroupIcons[$grName], 'elements' => $group);
            }
        }
        //print_r($serviceList);die();

        $this->layout = 'static';
        $this->render('hotelInfo', array('hotelInfo' => $hotelInfo, 'serviceList' => $serviceList
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

        if ($city->id == $currentCity->id) {
            if ($currentCity->id != 4466) {
                $currentCity = City::getCityByPk(4466);
            } elseif ($currentCity->id != 5185) {
                $currentCity = City::getCityByPk(5185);
            }
        }

        $citiesFrom = array();
        if (!$fromCity) {
            if ($city->id != 4466) {
                $this->addCityFrom($citiesFrom, 4466); // Moscow
            }
            if ($city->id != 5185) {
                $this->addCityFrom($citiesFrom, 5185); // Spb
            }
            if ($currentCity->id != $city->id) {
                $this->addCityFrom($citiesFrom, $currentCity->id);
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
        $criteria = new CDbCriteria();
        $criteria->addCondition('`to` = ' . $city->id);
        if ($fromCity) {
            $criteria->addCondition('`from` = ' . $fromCity->id);
        } else {
            $criteria->addCondition('`from` = ' . $currentCity->id);
        }
        $criteria->group = 'dateFrom';
        //$criteria->addCondition('`from` = ' . $currentCity->id);
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


        //Will be best price
        /*$criteria = new CDbCriteria();
        $criteria->addCondition('`to` = '.$city->id);
        $criteria->addCondition('`from` = '.$currentCity->id);
        $criteria->group = 'dateFrom';
        $criteria->addCondition('`from` = '.$currentCity->id);
        $criteria->addCondition('`dateFrom` >= '.date("'Y-m-d'"));
        $criteria->addCondition('`dateTo` <= '.date("'Y-m-d'", time()+ 3600*24*30));
        $criteria->order = 'priceBestPrice';
        //$criteria->limit = 18;

        $flightMinPrice = FlightCache::model()->findAll($criteria);*/


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

        //select bast price for all future time
        $criteria = new CDbCriteria();
        $criteria->addCondition('`to` = ' . $city->id);
        if ($fromCity) {
            $criteria->addCondition('`from` = ' . $fromCity->id);
        } else {
            $criteria->addCondition('`from` = ' . $currentCity->id);
        }
        $criteria->addCondition('`dateFrom` >= ' . date("'Y-m-d'"));

        $criteria->order = 'priceBestPrice';

        $flightCacheBestPrice = FlightCache::model()->find($criteria);
        if ($flightCacheBestPrice) {
            $flightCacheBestPriceArr = array('price' => $flightCacheBestPrice->priceBestPrice, 'date' => $flightCacheBestPrice->dateFrom);
        } else {
            $flightCacheBestPriceArr = array();
        }
        $this->layout = 'static';
        $this->render('city', array('city' => $city, 'citiesFrom' => $citiesFrom, 'hotelsInfo' => $hotelsInfo, 'fromCity' => $fromCity, 'currentCity' => $currentCity, 'flightCache' => $sortFc,
            'flightCacheFromCurrent' => $flightCacheFromCurrent,
            'flightCacheBestPrice' => $flightCacheBestPriceArr
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

        $this->morphy = Yii::app()->morphy;
        $countryUp = mb_strtoupper($country->localRu, 'utf-8');
        $countryMorph = array('caseAcc' => $this->getCase($countryUp, 'ВН'));
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
            $cityIds[$city->id] = $city->id;
        }

        if (isset($cityIds[$currentCity->id])) {
            unset($cityIds[$currentCity->id]);
        }
        $criteria = new CDbCriteria();
        $criteria->addInCondition('`to`', $cityIds);
        $criteria->addCondition('`from` = ' . $currentCity->id);
        $criteria->order = 'updatedAt';
        $criteria->limit = 14;
        $criteria->group = '`to`';

        $flightCache = FlightCache::model()->findAll($criteria);

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

        $this->layout = 'static';
        $this->render('country', array('country', $country, 'countryMorph' => $countryMorph, 'flightCache' => $flightCache, 'flightCacheFromCurrent' => $flightCacheFromCurrent, 'hotelsInfo' => $hotelsInfo,
            'currentCity' => $currentCity,
            'citiesFrom' => $citiesFrom
        ));
    }


    public function actionCountries()
    {
        $countries = Country::model()->findAll();
        $this->layout = 'static';
        $this->render('countries', array('countries' => $countries));
    }

    public function actionOWFlight($countryCode = '', $cityCodeFrom = '', $cityCodeTo = '')
    {
        $this->actionCity($countryCode, $cityCodeTo, $cityCodeFrom);
    }

    public function actionRTFlight($countryCode = '', $cityCodeFrom = '', $cityCodeTo = '')
    {
        if (!($country = $this->testCountry($countryCode)))
            return false;

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

        if ($city->id == $currentCity->id) {
            if ($currentCity->id != 4466) {
                $currentCity = City::getCityByPk(4466);
            } elseif ($currentCity->id != 5185) {
                $currentCity = City::getCityByPk(5185);
            }
        }

        $citiesFrom = array();
        if (!$fromCity) {
            if ($city->id != 4466) {
                $this->addCityFrom($citiesFrom, 4466); // Moscow
            }
            if ($city->id != 5185) {
                $this->addCityFrom($citiesFrom, 5185); // Spb
            }
            if ($currentCity->id != $city->id) {
                $this->addCityFrom($citiesFrom, $currentCity->id);
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

        // selection flight cache for show best price grafik
        $criteria = new CDbCriteria();
        $criteria->addCondition('`to` = ' . $city->id);
        if ($fromCity) {
            $criteria->addCondition('`from` = ' . $fromCity->id);
        } else {
            $criteria->addCondition('`from` = ' . $currentCity->id);
        }
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
        //print_r($flightCache);die();


        //select bast price for all future time
        $criteria = new CDbCriteria();
        $criteria->addCondition('`to` = ' . $city->id);
        if ($fromCity) {
            $criteria->addCondition('`from` = ' . $fromCity->id);
        } else {
            $criteria->addCondition('`from` = ' . $currentCity->id);
        }
        $criteria->addCondition('`dateFrom` >= ' . date("'Y-m-d'"));
        $criteria->addCondition('`dateBack` >= ' . date("'Y-m-d'"));

        $criteria->order = 'priceBestPrice';

        $flightCacheBestPrice = FlightCache::model()->find($criteria);
        $flightCacheBestPriceArr = array('price' => $flightCacheBestPrice->priceBestPrice, 'date' => $flightCacheBestPrice->dateFrom, 'dateBack' => $flightCacheBestPrice->dateBack);


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
        $this->render('rtflight', array('city' => $city, 'citiesFrom' => $citiesFrom, 'hotelsInfo' => $hotelsInfo, 'fromCity' => $fromCity, 'currentCity' => $currentCity, 'flightCache' => $sortFc, 'maxPrice' => $maxPrice, 'minPrice' => $minPrice, 'activeMin' => $activeMin,
            'flightCacheFromCurrent' => $flightCacheFromCurrent,
            'flightCacheBestPrice' => $flightCacheBestPriceArr
        ));
    }

}