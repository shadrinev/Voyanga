<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 18.03.13
 * Time: 18:34
 * To change this template use File | Settings | File Templates.
 */
class CitiesController extends ABaseAdminController
{
    public $morphy;
    public $defaultAction = 'admin';

    private function getCase($word, $case)
    {
        if($word && UtilsHelper::countRussianCharacters($word) > 0){
            $word = mb_convert_case($word, MB_CASE_UPPER, "UTF-8");
            $info = $this->morphy->castFormByGramInfo($word, 'С', array($case, 'ЕД'), false);
            if (isset($info[0]))
                return $this->mb_ucwords($info[0]['form']);
            return $this->mb_ucwords($word);
        }else{
            return $word;
        }
    }

    function mb_ucwords($str)
    {
        $str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
        return ($str);
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {

    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $this->render('index',array(


        ));
    }

    /**
     * Manages all models.
     */
    public function actionGetInfoByIata()
    {
        $this->morphy = Yii::app()->morphy;
        $data = array();
        if(isset($_REQUEST['code'])){
            $code = $_REQUEST['code'];
        }
        if(isset($_REQUEST['countryCode'])){
            $country = Country::getCountryByCode($_REQUEST['countryCode']);
        }
        $data['city'] = $_REQUEST['city'];
        if($data['city']['code']){
            try{
                $city = City::getCityByCode($data['city']['code']);
            }catch (Exception $e){
                $city = false;
            }
        }
        $ruNameChange = true;
        $data['airport'] = $_REQUEST['airport'];
        if($data['airport']['code']){
            try{
                $airport = Airport::getAirportByCode($data['airport']['code']);
            }catch (Exception $e){
                $airport = false;
            }
            if($airport){
                $data['airport']['id'] = $airport->id;
            }
        }
        if($code){
            $matches = $this->getUrlParse('http://avia-exams.com/airports/'.($code).'.html','|<div class="main">\s+<h2>(.*?)</h2>.*?<table class="table_litaku">\s+<tr><td width="250">.*?</td>\s+<td width="200"><b>(.*?)</b></td>\s+</tr><tr><td>.*?</td><td><b>(.*?)</b></td>\s+</tr><tr><td>.*?</td><td><b><b>(.*?)</b> \((.*?)\) / <b>(.*?)</b> \((.*?)\)</b></td>\s*</tr><tr>.*?</tr><tr><td>.*?</td><td>(.*?)</td>|ims');
            $country = false;
            if($matches[2] && $matches[2][0] && trim($matches[2][0])){
                if(!$data['airport']['localRu']){
                    list($airportName,$other) = explode($code,$matches[1][0]);
                    $airportName = trim($airportName);
                    $data['airport']['localRu'] = $airportName;

                }
                if(!$data['airport']['icaoCode']){
                    $data['airport']['icaoCode'] = $matches[3][0];
                }
                if(!$data['airport']['code']){
                    $data['airport']['code'] = $matches[2][0];
                }

                if($matches[4][0]){
                    $country = Country::model()->findByAttributes(array('localRu'=>$matches[4][0]));
                }
                if($matches[5][0] && !$country){
                    $country = Country::model()->findByAttributes(array('localEn'=>$matches[5][0]));
                }
                if($matches[5][0] && !$city){
                    $attrs = array('localRu'=>$matches[6][0]);
                    if($country){
                        $attrs['countryId'] = $country->id;
                    }
                    $city = City::model()->findByAttributes($attrs);
                }
                if($matches[7][0] && !$city){
                    $attrs = array('localEn'=>$matches[7][0]);
                    if($country){
                        $attrs['countryId'] = $country->id;
                    }
                    $city = City::model()->findByAttributes($attrs);
                }

                if(!$city){
                    if($matches[6][0] && !$data['city']['localRu']){
                        $data['city']['localRu'] = $matches[6][0];
                    }
                    if($matches[7][0] && !$data['city']['localEn']){
                        $data['city']['localEn'] = $matches[7][0];
                    }
                    $data['airport']['cityCode'] = $data['city']['code'];
                    $ruNameChange = true;

                }

                if($matches[8][0]){
                    list($latitude,$logitude) = explode(' ',$matches[8][0]);
                    $data['airport']['latitude'] = $latitude;
                    $data['airport']['longitude'] = $logitude;
                }
            }
            if($city){
                if(!$ruNameChange){
                    $data['city']['localRu'] = $city->localRu;
                    $data['city']['caseNom'] = $city->caseNom;
                    $data['city']['caseGen'] = $city->caseGen;
                    $data['city']['caseDat'] = $city->caseDat;
                    $data['city']['caseAcc'] = $city->caseAcc;
                    $data['city']['caseIns'] = $city->caseIns;
                    $data['city']['casePre'] = $city->casePre;
                    $data['city']['metaphoneRu'] = $city->metaphoneRu;
                }
                $data['city']['id'] = $city->id;
                $data['city']['localEn'] = $city->localEn;
                $data['city']['countryCode'] = $city->country->code;
                $data['city']['countryName'] = $city->country->localRu;
                $data['city']['hotelbookId'] = $city->hotelbookId;
                $data['city']['latitude'] = $city->latitude;
                $data['city']['longitude'] = $city->longitude;
                $data['city']['countAirports'] = $city->countAirports;
                $country = $city->country;
            }
            if($country){
                $data['city']['countryCode'] = $country->code;
                $data['city']['countryName'] = $country->localRu;
                if($data['city']['localRu']){
                    Yii::import('site.common.modules.hotel.models.*');
                    $hotelClient = new HotelBookClient();
                    $hotelbookCities = $hotelClient->getCities($country->hotelbookId);

                    $data['hotelbookIds'] = array();
                    $localRu = $data['city']['localRu'];
                    if(mb_strlen($localRu) > 3){
                        $localRu = mb_substr($localRu,0,-2);
                    }
                    $localEn = $data['city']['localEn'];
                    if(mb_strlen($localEn) > 3){
                        $localEn = mb_substr($localEn,0,-2);
                    }

                    foreach($hotelbookCities as $cityInfo){
                        $add = false;
                        if( mb_stripos($cityInfo['nameRu'],$localRu,0, "UTF-8") !== false ){
                            $data['hotelbookIds'][] = array('id'=>$cityInfo['id'],'name'=>$cityInfo['nameRu']."({$cityInfo['nameEn']})");
                            $add = true;
                        }
                        if(!$add && (mb_stripos($cityInfo['nameEn'],$localEn,0, "UTF-8") !== false)){
                            $data['hotelbookIds'][] = array('id'=>$cityInfo['id'],'name'=>$cityInfo['nameRu']."({$cityInfo['nameEn']})");
                            $add = true;
                        }
                        if(!$add){
                            //$data['hotelbookIds'][] = array('id'=>$cityInfo['id'],'name'=>$cityInfo['nameRu']."({$cityInfo['nameEn']})!".mb_stripos($cityInfo['nameRu'],$localRu,0, "UTF-8"));
                        }
                    }
                }

            }
            if($ruNameChange){
                if($data['city']['localRu']){
                    $data['city']['caseNom'] = $data['city']['localRu'];
                    $data['city']['caseGen'] = $this->getCase($data['city']['localRu'],'РД');//$this->getCase($data['city']['localRu'],'РД');
                    $data['city']['caseDat'] = $this->getCase($data['city']['localRu'],'ДТ');
                    $data['city']['caseAcc'] = $this->getCase($data['city']['localRu'],'ВН');
                    $data['city']['caseIns'] = $this->getCase($data['city']['localRu'],'ТВ');
                    $data['city']['casePre'] = $this->getCase($data['city']['localRu'],'ПР');
                    $data['city']['metaphoneRu'] = UtilsHelper::ruMetaphone($data['city']['localRu']);
                }
            }
        }

        echo json_encode($data);
        die();
    }

    private function getUrlParse($url,$regexp, $returnBody = false,$inWin = true)
    {
        $matches = array();
        try{
            list($headers, $data) = Yii::app()
                ->httpClient->get($url);
            if($inWin){
                $data = iconv('CP1251','UTF-8',$data);
            }
            preg_match_all($regexp,$data,$matches);
            if($returnBody){
                $matches['body'] = $data;
            }
        }catch (Exception $e){

        }
        return $matches;
    }

    /**
     * Get city info by code
     */
    public function actionGetCityByCode()
    {
        $code = $_REQUEST['cityCode'];
        try{
            $city = City::getCityByCode($code);
        }catch (Exception $e){
            $city = false;
        }
        $data = false;
        if($city){
            $data = array('cityCode'=>$city->code,'cityName'=>$city->localRu.', '.$city->country->localRu);
        }

        echo json_encode($data);
        die();
    }

    /**
     * Get country info by code
     */
    public function actionGetCountryByCode()
    {
        $code = $_REQUEST['countryCode'];
        try{
            $country = Country::getCountryByCode($code);
        }catch (Exception $e){
            $country = false;
        }
        $data = false;
        if($country){
            $data = array('countryCode'=>$country->code,'countryName'=>$country->localRu);
        }
        echo json_encode($data);
        die();
    }

    /**
     * Get country info by code
     */
    public function actionSave()
    {
        $data = array();
        $ret = array();
        $ret['saveReturn'] = '';
        $data['city'] = $_REQUEST['city'];

        $data['airport'] = $_REQUEST['airport'];

        if($data['city']['code']){
            try{
                $city = City::getCityByCode($data['city']['code']);
            }catch (Exception $e){
                $city = false;
            }
        }
        if($data['city']['set'] != 'false'){
            try{
                $country = Country::getCountryByCode($data['city']['countryCode']);
                if($data['city']['id']){
                    $city = City::getCityByPk($data['city']['id']);
                }else{
                    $city = new City;
                }

                $ret['cityCountryId'] = $country->id;
                $attributes = $city->getAttributes();
                foreach($attributes as $attrName=>$attrVal){
                    if(isset($data['city'][$attrName])){
                        $city->{$attrName} = $data['city'][$attrName];
                    }
                }
                $city->countryId = $country->id;


                //$attributes = $city->getAttributes();
                if($city->save()){
                    if(!$city->id){
                        $city->id = Yii::app()->db->getLastInsertID();
                    }
                    $ret['cityId'] = $city->id;
                    $ret['cityAttrs'] = $city->getAttributes();
                }else{
                    $ret['saveReturn'] .= print_r($city->errors,true);
                }
            }catch (Exception $e){
                $ret['saveReturn'] .= $e->getMessage();
            }

        }else{

        }
        if($data['airport']['set'] != 'false'){
            try{
                $airportCity = City::getCityByCode($data['airport']['cityCode']);
                if($data['airport']['id']){
                    $airport = Airport::getAirportByPk($data['airport']['id']);
                }else{
                    $airport = new Airport;
                }
                $attributes = $airport->getAttributes();
                foreach($attributes as $attrName=>$attrVal){
                    if(isset($data['airport'][$attrName])){
                        $airport->{$attrName} = $data['airport'][$attrName];
                    }
                }
                $airport->cityId = $airportCity->id;
                $ret['airportCityId'] = $airportCity->id;
                if($airport->save()){
                    if(!$airport->id){
                        $airport->id = Yii::app()->db->getLastInsertID();
                    }
                    $ret['airportId'] = $airport->id;
                    $ret['airportAttrs'] = $airport->getAttributes();
                }else{
                    $ret['saveReturn'] .= print_r($airport->errors,true);
                }
            }catch (Exception $e){
                $ret['saveReturn'] .= 'Cant save airport '.$e->getMessage();
            }
        }
        echo json_encode($ret);
        die();
    }
}
