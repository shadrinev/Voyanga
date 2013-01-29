<?php

class LandingController extends Controller {
    public $morphy;

    public function actionIndex()
    {
        $this->layout = 'static';
        $this->render('landing');
    }

    public function actionHotels($countryCode = '',$cityCode = '')
    {
        if(!($country = $this->testCountry($countryCode)))
            return false;

        $this->morphy = Yii::app()->morphy;
        $countryUp = mb_strtoupper($country->localRu, 'utf-8');
        $countryMorph = array('caseAcc'=>$this->getCase($countryUp,'ВН'));
        //if(!$cityFromCode){
        $currentCity = City::getCityByCode('LED');
        //}else{
        //    $currentCity = City::getCityByCode($cityFromCode);
        //}
        $city = City::getCityByCode($cityCode);

        $citiesFrom = array();
        $this->addCityFrom($citiesFrom,4466);// Moscow
        $this->addCityFrom($citiesFrom,5185);// Spb
        $this->addCityFrom($citiesFrom,$currentCity->id);



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
        $criteria->addCondition('`to` = '.$city->id);
        $criteria->addCondition('`from` = '.$currentCity->id);
        $criteria->group = 'dateFrom';
        $criteria->addCondition('`from` = '.$currentCity->id);
        $criteria->addCondition('`dateFrom` >= '.date("'Y-m-d'"));
        $criteria->addCondition('`dateFrom` <= '.date("'Y-m-d'", time()+ 3600*24*30));
        $criteria->order = 'priceBestPrice';
        //$criteria->limit = 18;

        $flightCache = FlightCache::model()->findAll($criteria);
        $sortFc = array();
        foreach($flightCache as $fc){
            $k = strtotime($fc->dateFrom);
            $sortFc[$k] = $fc;
        }
        ksort($sortFc);
        $sortFc = array_slice($sortFc,0,18);
        $minPrice = false;
        $maxPrice = false;
        $activeMin = null;
        $activeMax = null;
        foreach($sortFc as $k=>$fc){
            //$k = strtotime($fc->dateFrom);
            if(!$minPrice){
                $minPrice = $fc->priceBestPrice;
                $maxPrice = $fc->priceBestPrice;
            }else{
                if($fc->priceBestPrice < $minPrice){
                    $minPrice = $fc->priceBestPrice;
                    $activeMin = $k;
                }
                if($fc->priceBestPrice > $maxPrice){
                    $maxPrice = $fc->priceBestPrice;
                    $activeMax = $k;
                }
            }
        }
        //print_r($flightCache);die();

        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`cityId`',$cityIds);
        $criteria->addCondition('cityId = '.$city->id);

        $criteria->limit = 15;

        $hotelCache = HotelDb::model()->findAll($criteria);
        $hotelsInfo = array();
        foreach($hotelCache as $hc){
            $hotelInfo = $hotelClient->hotelDetail($hc->id);
            $hotelInfo->price = $hc->minPrice;
            $hotelInfo->hotelName = $hc->name;
            $hotelsInfo[] = $hotelInfo;
        }

        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`to`',$cityIds);
        $criteria->addCondition('`from` = '.$currentCity->id);
        $criteria->addCondition('`dateFrom` > \''.date('Y-m-d')."'");
        $criteria->order = 'priceBestPrice';
        $criteria->limit = 14;
        $criteria->group = '`to`';

        $flightCacheFromCurrent = FlightCache::model()->findAll($criteria);

        $this->layout = 'static';
        $this->render('hotels', array('city'=>$city,'citiesFrom'=>$citiesFrom,'hotelsInfo'=>$hotelsInfo,'currentCity'=>$currentCity,'flightCache'=>$sortFc,'maxPrice'=>$maxPrice,'minPrice'=>$minPrice,'activeMin'=>$activeMin,
            'flightCacheFromCurrent' => $flightCacheFromCurrent
        ));
    }

    public function actionHotelInfo($hotelId){
        Yii::import('site.common.modules.hotel.models.*');
        $hotelClient = new HotelBookClient();

        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`cityId`',$cityIds);
        $criteria->addCondition('id = '.$hotelId);


        $hc = HotelDb::model()->find($criteria);
        $hotelsInfo = array();
        if($hc){
            $hotelInfo = $hotelClient->hotelDetail($hc->id);
            $hotelInfo->price = $hc->minPrice;
            $hotelInfo->hotelName = $hc->name;
            $city = City::getCityByPk($hc->cityId);
        }else{
            $hotelInfo = $hotelClient->hotelDetail($hotelId);
            if($hotelInfo->city){
                $city = $hotelInfo->city;
            }
        }
        if($city){
            $rating = new HotelRating();
            $ratings = $rating->findByNames(array($hotelInfo->hotelName),$city);
            if($ratings){
                foreach($ratings as $rate) break;
                $hotelInfo->rating = $rate;
            }
        }
        $serviceGroupIcons = array('Сервис'=>'service','Спорт и отдых'=>'sport','Туристам'=>'turist','Интернет'=>'internet','Развлечения и досуг'=>'dosug','Парковка'=>'parkovka','Дополнительно'=>'dop','В отеле'=>'in-hotel');
        $serviceList = array();
        if($hotelInfo->hotelGroupServices){
            foreach($hotelInfo->hotelGroupServices as $grName=>$group){
                $serviceList[] = array('name'=>$grName,'icon'=>$serviceGroupIcons[$grName],'elements'=>$group);
            }
        }
        //print_r($serviceList);die();

        $this->layout = 'static';
        $this->render('hotelInfo', array('hotelInfo'=>$hotelInfo,'serviceList'=>$serviceList
        ));
    }

    private function testCountry($countryCode)
    {
        if($countryCode){
            try{
                $country = Country::getCountryByCode($countryCode);
            }catch (CException $e){
                $this->layout = 'static';
                $this->render('notfound');
                return false;
            }
            return $country;
        }else{
            return false;
        }
    }

    public function actionCity($countryCode = '',$cityCode = '',$cityFromCode='')
    {

        if(!($country = $this->testCountry($countryCode)))
            return false;

        $this->morphy = Yii::app()->morphy;
        $countryUp = mb_strtoupper($country->localRu, 'utf-8');
        $countryMorph = array('caseAcc'=>$this->getCase($countryUp,'ВН'));
        if(!$cityFromCode){
            $currentCity = City::getCityByCode('LED');
        }else{
            $currentCity = City::getCityByCode($cityFromCode);
        }
        $city = City::getCityByCode($cityCode);

        $citiesFrom = array();
        $this->addCityFrom($citiesFrom,4466);// Moscow
        $this->addCityFrom($citiesFrom,5185);// Spb
        $this->addCityFrom($citiesFrom,$currentCity->id);



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
        $criteria->addCondition('`to` = '.$city->id);
        $criteria->addCondition('`from` = '.$currentCity->id);
        $criteria->group = 'dateFrom';
        $criteria->addCondition('`from` = '.$currentCity->id);
        $criteria->addCondition('`dateFrom` >= '.date("'Y-m-d'"));
        $criteria->addCondition('`dateFrom` <= '.date("'Y-m-d'", time()+ 3600*24*30));
        $criteria->order = 'priceBestPrice';
        //$criteria->limit = 18;

        $flightCache = FlightCache::model()->findAll($criteria);
        $sortFc = array();
        foreach($flightCache as $fc){
            $k = strtotime($fc->dateFrom);
            $sortFc[$k] = $fc;
        }
        ksort($sortFc);
        $sortFc = array_slice($sortFc,0,18);
        $minPrice = false;
        $maxPrice = false;
        $activeMin = null;
        $activeMax = null;
        foreach($sortFc as $k=>$fc){
            //$k = strtotime($fc->dateFrom);
            if(!$minPrice){
                $minPrice = $fc->priceBestPrice;
                $maxPrice = $fc->priceBestPrice;
            }else{
                if($fc->priceBestPrice < $minPrice){
                    $minPrice = $fc->priceBestPrice;
                    $activeMin = $k;
                }
                if($fc->priceBestPrice > $maxPrice){
                    $maxPrice = $fc->priceBestPrice;
                    $activeMax = $k;
                }
            }
        }
        //print_r($flightCache);die();

        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`cityId`',$cityIds);
        $criteria->addCondition('cityId = '.$city->id);

        $criteria->limit = 15;

        $hotelCache = HotelDb::model()->findAll($criteria);
        $hotelsInfo = array();
        foreach($hotelCache as $hc){
            $hotelInfo = $hotelClient->hotelDetail($hc->id);
            $hotelInfo->price = $hc->minPrice;
            $hotelInfo->hotelName = $hc->name;
            $hotelsInfo[] = $hotelInfo;
        }

        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`to`',$cityIds);
        $criteria->addCondition('`from` = '.$currentCity->id);
        $criteria->addCondition('`dateFrom` > \''.date('Y-m-d')."'");
        $criteria->order = 'priceBestPrice';
        $criteria->limit = 14;
        $criteria->group = '`to`';

        $flightCacheFromCurrent = FlightCache::model()->findAll($criteria);

        $this->layout = 'static';
        $this->render('city', array('city'=>$city,'citiesFrom'=>$citiesFrom,'hotelsInfo'=>$hotelsInfo,'currentCity'=>$currentCity,'flightCache'=>$sortFc,'maxPrice'=>$maxPrice,'minPrice'=>$minPrice,'activeMin'=>$activeMin,
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

    private function addCityFrom(&$cityFromArray,$cityFromId)
    {
        if(!isset($cityFromArray[$cityFromId])){
            $city = City::getCityByPk($cityFromId);
            $cityFromArray[$cityFromId] = array('cityId'=>$cityFromId,'cityName'=>$city->localRu,'cityAcc'=>$city->caseAcc);
        }
    }

    public function actionCountry($countryCode = '')
    {
        if(!($country = $this->testCountry($countryCode)))
            return false;

        $this->morphy = Yii::app()->morphy;
        $countryUp = mb_strtoupper($country->localRu, 'utf-8');
        $countryMorph = array('caseAcc'=>$this->getCase($countryUp,'ВН'));
        $currentCity = City::getCityByCode('LED');

        $citiesFrom = array();
        $this->addCityFrom($citiesFrom,4466);// Moscow
        $this->addCityFrom($citiesFrom,5185);// Spb
        $this->addCityFrom($citiesFrom,$currentCity->id);



        Yii::import('site.common.modules.hotel.models.*');
        $hotelClient = new HotelBookClient();

        $criteria = new CDbCriteria();
        $criteria->addCondition('countAirports > 0');
        $criteria->addCondition('countryId = '.$country->id);
        $cities = City::model()->findAll($criteria);
        $cityIds = array();
        foreach($cities as $city){
            $cityIds[$city->id] = $city->id;
        }

        if(isset($cityIds[$currentCity->id])){
            unset($cityIds[$currentCity->id]);
        }
        $criteria = new CDbCriteria();
        $criteria->addInCondition('`to`',$cityIds);
        $criteria->addCondition('`from` = '.$currentCity->id);
        $criteria->order = 'updatedAt';
        $criteria->limit = 14;
        $criteria->group = '`to`';

        $flightCache = FlightCache::model()->findAll($criteria);

        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`cityId`',$cityIds);
        $criteria->addCondition('countryId = '.$country->id);

        $criteria->limit = 15;

        $hotelCache = HotelDb::model()->findAll($criteria);
        $hotelsInfo = array();
        foreach($hotelCache as $hc){
            $hotelInfo = $hotelClient->hotelDetail($hc->id);
            $hotelInfo->price = $hc->minPrice;
            $hotelInfo->hotelName = $hc->name;
            $hotelsInfo[] = $hotelInfo;
        }
        //echo 'count'.count($hotelCache);

        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`to`',$cityIds);
        $criteria->addCondition('`from` = '.$currentCity->id);
        $criteria->addCondition('`dateFrom` > \''.date('Y-m-d')."'");
        $criteria->order = 'priceBestPrice';
        $criteria->limit = 14;
        $criteria->group = '`to`';

        $flightCacheFromCurrent = FlightCache::model()->findAll($criteria);

        $this->layout = 'static';
        $this->render('country',array('country',$country,'countryMorph'=>$countryMorph,'flightCache'=>$flightCache,'flightCacheFromCurrent'=>$flightCacheFromCurrent,'hotelsInfo'=>$hotelsInfo,
        'currentCity'=>$currentCity,
            'citiesFrom'=>$citiesFrom
        ));
    }


    public function actionCountries()
    {
        $countries = Country::model()->findAll();
        $this->layout = 'static';
        $this->render('countries',array('countries'=>$countries));
    }

    public function actionOWFlight($countryCode = '',$cityCodeFrom = '',$cityCodeTo = '')
    {
        $this->actionCity($countryCode,$cityCodeTo,$cityCodeFrom);
    }

    public function actionRTFlight($countryCode = '',$cityCodeFrom = '',$cityCodeTo = '')
    {
        if(!($country = $this->testCountry($countryCode)))
            return false;

        $this->morphy = Yii::app()->morphy;
        $countryUp = mb_strtoupper($country->localRu, 'utf-8');
        $countryMorph = array('caseAcc'=>$this->getCase($countryUp,'ВН'));
        if(!$cityCodeFrom){
            $currentCity = City::getCityByCode('LED');
        }else{
            $currentCity = City::getCityByCode($cityCodeFrom);
        }
        $city = City::getCityByCode($cityCodeTo);

        $citiesFrom = array();
        $this->addCityFrom($citiesFrom,4466);// Moscow
        $this->addCityFrom($citiesFrom,5185);// Spb
        $this->addCityFrom($citiesFrom,$currentCity->id);



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
        $criteria->addCondition('`to` = '.$city->id);
        $criteria->addCondition('`from` = '.$currentCity->id);
        //$criteria->group = 'dateBack';
        //$criteria->addCondition('`from` = '.$currentCity->id);
        $criteria->addCondition('`dateFrom` >= '.date("'Y-m-d'"));
        $criteria->addCondition('`dateFrom` <= '.date("'Y-m-d'", time()+ 3600*24*18));
        $criteria->addCondition('`dateBack` >= '.date("'Y-m-d'"));
        $criteria->addCondition('`dateBack` <= '.date("'Y-m-d'", time()+ 3600*24*18));

        //$criteria->addCondition("`dateBack` <> '0000-00-00'");
        $criteria->order = 'priceBestPrice';
        //$criteria->limit = 18;

        $flightCache = FlightCache::model()->findAll($criteria);
        /*$sortFc = array();
        foreach($flightCache as $fc){
            $k = strtotime($fc->dateFrom);
            $sortFc[$k] = $fc;//array('price'=>$fc->priceBestPrice,'date'=>$fc->dateFrom,'dateBack'=>$fc->dateBack);
        }
        ksort($sortFc);*/
        //$sortFc = array_slice($sortFc,0,18);
        $minPrice = false;
        $maxPrice = false;
        $activeMin = null;
        $activeMax = null;
        foreach($flightCache as $k=>$fc){
            $k = strtotime($fc->dateFrom);
            if(!$minPrice){
                $minPrice = $fc->priceBestPrice;
                $maxPrice = $fc->priceBestPrice;
            }else{
                if($fc->priceBestPrice < $minPrice){
                    $minPrice = $fc->priceBestPrice;
                    $activeMin = $k;
                }
                if($fc->priceBestPrice > $maxPrice){
                    $maxPrice = $fc->priceBestPrice;
                    $activeMax = $k;
                }
            }
        }
        $sortFc = array();
        foreach($flightCache as $fc){
            //$k = strtotime($fc->dateFrom);
            $sortFc[] = array('price'=>$fc->priceBestPrice,'date'=>$fc->dateFrom,'dateBack'=>$fc->dateBack);
        }
        //print_r($flightCache);die();

        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`cityId`',$cityIds);
        $criteria->addCondition('cityId = '.$city->id);

        $criteria->limit = 15;

        $hotelCache = HotelDb::model()->findAll($criteria);
        $hotelsInfo = array();
        foreach($hotelCache as $hc){
            $hotelInfo = $hotelClient->hotelDetail($hc->id);
            $hotelInfo->price = $hc->minPrice;
            $hotelInfo->hotelName = $hc->name;
            $hotelsInfo[] = $hotelInfo;
        }

        $criteria = new CDbCriteria();
        //$criteria->addInCondition('`to`',$cityIds);
        $criteria->addCondition('`from` = '.$currentCity->id);
        $criteria->addCondition('`dateFrom` > \''.date('Y-m-d')."'");
        $criteria->order = 'priceBestPrice';
        $criteria->limit = 14;
        $criteria->group = '`to`';

        $flightCacheFromCurrent = FlightCache::model()->findAll($criteria);


        $this->layout = 'static';
        //$this->render('landing');
        $this->render('rtflight', array('city'=>$city,'citiesFrom'=>$citiesFrom,'hotelsInfo'=>$hotelsInfo,'currentCity'=>$currentCity,'flightCache'=>$sortFc,'maxPrice'=>$maxPrice,'minPrice'=>$minPrice,'activeMin'=>$activeMin,
            'flightCacheFromCurrent' => $flightCacheFromCurrent
        ));
    }

}