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
    public $defaultAction = 'admin';
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
        $data = array();
        if(isset($_REQUEST['code'])){
            $code = $_REQUEST['code'];
        }
        if(isset($_REQUEST['countryCode'])){
            $country = Country::getCountryByCode($_REQUEST['countryCode']);
        }
        if($code){
            $matches = $this->getUrlParse('http://avia-exams.com/airports/'.($code).'.html','|<div class="main">\s+<h2>(.*?)</h2>.*?<table class="table_litaku">\s+<tr><td width="250">.*?</td>\s+<td width="200"><b>(.*?)</b></td>\s+</tr><tr><td>.*?</td><td><b>(.*?)</b></td>\s+</tr><tr><td>.*?</td><td><b><b>(.*?)</b> \((.*?)\) / <b>(.*?)</b> \((.*?)\)</b></td>\s*</tr><tr>.*?</tr><tr><td>.*?</td><td>(.*?)</td>|ims');
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
