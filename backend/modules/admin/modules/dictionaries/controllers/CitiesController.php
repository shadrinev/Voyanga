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
            $city = City::getCityByCode($data['city']['code']);
        }
        $ruNameChange = true;
        $data['airport'] = $_REQUEST['airport'];
        if($code){
            $matches = $this->getUrlParse('http://avia-exams.com/airports/'.($code).'.html','|<div class="main">\s+<h2>(.*?)</h2>.*?<table class="table_litaku">\s+<tr><td width="250">.*?</td>\s+<td width="200"><b>(.*?)</b></td>\s+</tr><tr><td>.*?</td><td><b>(.*?)</b></td>\s+</tr><tr><td>.*?</td><td><b><b>(.*?)</b> \((.*?)\) / <b>(.*?)</b> \((.*?)\)</b></td>\s*</tr><tr>.*?</tr><tr><td>.*?</td><td>(.*?)</td>|ims');
            if($matches[2] && $matches[2][0]){
                if(!$data['airport']['localRu']){
                    list($airportName,$other) = explode($code,$matches[1][0]);
                    $airportName = trim($airportName);
                    $data['airport']['localRu'] = $airportName;

                }
                if(!$data['airport']['icaoCode']){
                    $data['airport']['icaoCode'] = $matches[3][0];
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
                if($country){

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
                    $data['city']['localRu'] = $city->localEn;
                    $data['city']['countryCode'] = $city->country->code;
                    $data['city']['countryName'] = $city->country->localRu;
                    $data['city']['hotelbookId'] = $city->hotelbookId;
                    $data['city']['hotelbookId'] = $city->hotelbookId;
                }else{
                    if($matches[6][0] && !$data['city']['localRu']){
                        $data['city']['localRu'] = $matches[6][0];
                    }
                    if($matches[7][0] && !$data['city']['localEn']){
                        $data['city']['localEn'] = $matches[7][0];
                    }
                    $ruNameChange = true;

                }
                if($ruNameChange){
                    if($data['city']['localRu']){
                        $data['city']['caseNom'] = $data['city']['localRu'];
                        $data['city']['caseGen'] = $this->getCase($data['city']['localRu'],'РД');
                        $data['city']['caseDat'] = $this->getCase($data['city']['localRu'],'ДТ');
                        $data['city']['caseAcc'] = $this->getCase($data['city']['localRu'],'ВН');
                        $data['city']['caseIns'] = $this->getCase($data['city']['localRu'],'ТВ');
                        $data['city']['casePre'] = $this->getCase($data['city']['localRu'],'ПР');
                        $data['city']['metaphoneRu'] = UtilsHelper::ruMetaphone($data['city']['localRu']);
                    }
                }

            }
        }


        echo json_encode($data);
        die();
    }

    private function getUrlParse($url,$regexp, $returnBody = false)
    {
        $matches = array();
        try{
            list($headers, $data) = Yii::app()
                ->httpClient->get($url);
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
        $city = City::getCityByCode($code);
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
        $country = Country::getCountryByCode($code);
        $data = false;
        if($country){
            $data = array('countryCode'=>$country->code,'countryName'=>$country->localRu);
        }
        echo json_encode($data);
        die();
    }
}
