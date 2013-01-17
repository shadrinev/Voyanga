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
        $this->layout = 'static';
        $this->render('hotels');
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

    public function actionCity($countryCode = '',$cityCode = '')
    {
        $this->layout = 'static';
        $this->render('city');
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

    public function actionCountry($countryCode = '')
    {
        if(!($country = $this->testCountry($countryCode)))
            return false;
        $this->morphy = Yii::app()->morphy;
        $countryUp = mb_strtoupper($country->localRu, 'utf-8');
        $countryMorph = array('caseAcc'=>$this->getCase($countryUp,'ВН'));
        $currentCity = City::getCityByCode('LED');

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

        $flightCache = FlightCache::model()->findAll($criteria);

        //getting cities with hotelbookId
        $criteria = new CDbCriteria();
        $criteria->addCondition('`hotelbookId` IS NOT NULL');
        $criteria->addCondition('countryId = '.$country->id);
        $criteria->limit = 14;
        $cities = City::model()->findAll($criteria);
        $cityIds = array();
        foreach($cities as $city){
            $cityIds[$city->id] = $city->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('`cityId`',$cityIds);

        $criteria->limit = 14;

        $hotelCache = HotelDb::model()->findAll($criteria);
        $hotelsInfo = array();
        foreach($hotelCache as $hc){
            $hotelInfo = $hotelClient->hotelDetail($hc->hotelId);
            $hotelInfo->price = $hc->minPrice;
            $hotelsInfo[] = $hotelInfo;
        }

        $this->layout = 'static';
        $this->render('country',array('country',$country,'countryMorph'=>$countryMorph,'flightCache'=>$flightCache,'hotelsInfo'=>$hotelsInfo));
    }


    public function actionCountries()
    {
        $countries = Country::model()->findAll();
        $this->layout = 'static';
        $this->render('countries',array('countries'=>$countries));
    }

    public function actionOWFlight($countryCode = '',$cityCodeFrom = '',$cityCodeTo = '')
    {
        $this->layout = 'static';
        $this->render('owflight');
    }

    public function actionRTFlight($countryCode = '',$cityCodeFrom = '',$cityCodeTo = '')
    {
        $this->layout = 'static';
        $this->render('owflight');
    }

}