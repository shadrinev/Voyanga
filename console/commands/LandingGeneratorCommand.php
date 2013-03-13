<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 06.03.13
 * Time: 11:37
 * To change this template use File | Settings | File Templates.
 */


class LandingGeneratorCommand extends CConsoleCommand
{

    public $morphy;
    public function getHelp()
    {
        return <<<EOD
USAGE UpdateGeoip [OPTIONS]
   ...
Options:
--type=(value) - Default value airports
   ...
EOD;
    }

    /**
     * Execute the action.
     * @param array command line parameters specific for this command
     */
    public function actionIndex()
    {
        $path =  YiiBase::getPathOfAlias('frontend.www.meteorit');

        Yii::import('site.common.components.statistic.reports.*');
        $report = new PopularityOfFlightsSearch();
        $model = ReportExecuter::run($report);
        $directs = $model->findAll();
        $citiesDirects = array();
        $allCityIds = array();
        foreach($directs as $direct){
            $fromId = intval($direct->_id['departureCityId']);
            $toId = intval($direct->_id['arrivalCityId']);
            $allCityIds[$fromId] = $fromId;
            $allCityIds[$toId] = $toId;
            if(!isset($citiesDirects[$toId])){
                $citiesDirects[$toId] = array();
            }
            $citiesDirects[$toId][$fromId] = true;
        }



        $connection=Yii::app()->db;
        /*$sql = 'SELECT city.id, city.code, city.countryId, ctr.code AS countryCode,caseGen,caseAcc
FROM  `city`
INNER JOIN  `country` AS ctr ON ctr.`id` =  `city`.`countryId`
WHERE  `countAirports` >0 AND city.hotelbookId >0';*/
        $sql = 'SELECT city.id, city.code, city.countryId, ctr.code AS countryCode,caseGen,caseAcc,caseNom
FROM  `city`
INNER JOIN  `country` AS ctr ON ctr.`id` =  `city`.`countryId`
WHERE  `city`.`id` IN ('.implode(',',$allCityIds).')';
        $outFilename = $path.'/landingFromTo.csv';
        $fp = fopen($outFilename,'w');
        if(!$fp){
            echo 'Cant open file '.$outFilename;
            return '';
        }

        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        fwrite($fp,"id;codeFrom;codeTo;from;to;ctrCodeTo;url;desc;price\r\n");

        $citiesInfo = array();
        $ruCityIds = array();
        while(($row=$dataReader->read())!==false) {
            $citiesInfo[$row['id']] = array('cityCode'=>$row['code'],'countryCode'=>$row['countryCode'],'caseGen'=>$row['caseGen'],'caseAcc'=>$row['caseAcc'],'caseNom'=>$row['caseNom']);
            if($row['countryCode'] == 'RU'){
                $ruCityIds[$row['id']] = $row['id'];
            }
        }

        $sql = 'SELECT `from`,`to`,`dateFrom`,`dateBack`,`priceBestPrice` from (SELECT * FROM `flight_cache` WHERE `dateFrom` > \'' . date('Y-m-d') . '\' AND `dateBack` > \'' . date('Y-m-d') . '\' ORDER BY priceBestPrice) as tbl1 GROUP BY `from`,`to` ORDER BY priceBestPrice';
        $bestPricesRt = array();
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        while(($row=$dataReader->read())!==false) {
            if(!isset($bestPricesRt[$row['to']])){
                $bestPricesRt[$row['to']] = array();
            }
            $bestPricesRt[$row['to']][$row['from']] = $row['priceBestPrice'];
        }

        $bestPricesOw = array();
        $sql = 'SELECT `from`,`to`,`dateFrom`,`dateBack`,`priceBestPrice` from (SELECT * FROM `flight_cache` WHERE `from` NOT IN ('.implode(',',$ruCityIds).') AND `to` IN ('.implode(',',$ruCityIds).') AND `dateFrom` > \'' . date('Y-m-d') . '\' AND `dateBack` = \'0000-00-00\' ORDER BY priceBestPrice) as tbl1 GROUP BY `from`,`to` ORDER BY priceBestPrice';
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        while(($row=$dataReader->read())!==false) {
            if(!isset($bestPricesOw[$row['to']])){
                $bestPricesOw[$row['to']] = array();
            }
            $bestPricesOw[$row['to']][$row['from']] = $row['priceBestPrice'];
        }

        $j = 0;
        foreach($citiesDirects as $cityToKey=>$citiesFrom){
            foreach($citiesFrom as $cityFromKey=>$boolVar){
                if($cityFromKey != $cityToKey){
                    //if(isset($ruCityIds[$cityFromKey]) || isset($ruCityIds[$cityToKey])){
                        $cityToInfo = $citiesInfo[$cityToKey];
                        $cityFromInfo = $citiesInfo[$cityFromKey];
                        $j++;
                        $minPrice = '';
                        $url = 'https://voyanga.com/land/'.$cityToInfo['countryCode'].'/'.$cityFromInfo['cityCode'].'/'.$cityToInfo['cityCode'].'/';
                        if(isset($ruCityIds[$cityToKey]) && !isset($ruCityIds[$cityFromKey])){
                            $url.='trip/OW';
                            if(isset($bestPricesOw[$cityToKey],$bestPricesOw[$cityToKey][$cityFromKey])){
                                $minPrice = $bestPricesOw[$cityToKey][$cityFromKey];
                            }
                        }else{
                            if(isset($bestPricesRt[$cityToKey],$bestPricesRt[$cityToKey][$cityFromKey])){
                                $minPrice = $bestPricesRt[$cityToKey][$cityFromKey];
                            }
                        }
                        $desc = "из {$cityFromInfo['caseGen']} в {$cityToInfo['caseAcc']}";
                        $str = "$j;{$cityFromInfo['cityCode']};{$cityToInfo['cityCode']};{$cityFromInfo['caseNom']};{$cityToInfo['caseNom']};{$cityToInfo['countryCode']};{$url};{$desc};{$minPrice}\r\n";
                        fwrite($fp,$str);
                    //}
                }
                //echo "xml:".$sxe->asXML();
                //break;
            }
            //break;
        }


        echo "RU:".implode(',',$ruCityIds)."\n";

        echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
        echo "count:".count($citiesInfo);
        fclose($fp);

        // -------------------------- Only to one City
        $outFilename = $path.'/landingTo.csv';
        $fp = fopen($outFilename,'w');
        if(!$fp){
            echo 'Cant open file '.$outFilename;
            return '';
        }
        fwrite($fp,"id;codeTo;ctrCodeTo;url;desc;price\r\n");

        $j = 0;
        foreach($citiesInfo as $cityToKey=>$cityToInfo){
            $j++;
            $minPrice = '';
            $url = 'https://voyanga.com/land/'.$cityToInfo['countryCode'].'/'.$cityToInfo['cityCode'].'/';
            if(isset($ruCityIds[$cityToKey])){

                if(isset($bestPricesOw[$cityToKey])){
                    foreach($bestPricesOw[$cityToKey] as $minPrice)
                        break;
                    //$minPrice = $bestPricesOw[$cityFromKey][$cityToKey];
                }
            }else{
                if(isset($bestPricesRt[$cityToKey])){
                    foreach($bestPricesRt[$cityToKey] as $minPrice)
                        break;
                    //$minPrice = $bestPricesRt[$cityFromKey][$cityToKey];
                }
            }
            $desc = "в {$cityToInfo['caseAcc']}";
            //$sxe['url'] = $url;
            $str = "$j;{$cityToInfo['cityCode']};{$cityToInfo['countryCode']};{$url};{$desc};{$minPrice}\r\n";
            fwrite($fp,$str);
        }

        fclose($fp);
        echo "ok";

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

    public function actionHotels(){
        $path =  YiiBase::getPathOfAlias('frontend.www.meteorit');
        $connection=Yii::app()->db;
        $sql = 'SELECT city.id, city.code, city.countryId, ctr.code AS countryCode,casePre,caseAcc
FROM  `city`
INNER JOIN  `country` AS ctr ON ctr.`id` =  `city`.`countryId`
WHERE  `countAirports` >0 AND city.hotelbookId >0';

        $command=$connection->createCommand($sql);
        $dataReader=$command->query();

        $citiesInfo = array();
        $ruCityIds = array();
        while(($row=$dataReader->read())!==false) {
            $citiesInfo[$row['id']] = array('cityCode'=>$row['code'],'countryCode'=>$row['countryCode'],'casePre'=>$row['casePre'],'caseAcc'=>$row['caseAcc']);
            if($row['countryCode'] == 'RU'){
                $ruCityIds[$row['id']] = $row['id'];
            }
        }

        $sql = 'SELECT minPrice, cityId FROM (SELECT * FROM `hotel` ORDER BY minPrice) as tbl1 GROUP BY `cityId`';
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $hotelPrices = array();
        while(($row=$dataReader->read())!==false) {
            if(isset($citiesInfo[$row['cityId']])){
                $hotelPrices[$row['cityId']] = $row['minPrice'];
            }
        }

        // -------------------------- Hotels in City
        $outFilename = $path.'/landingHotelsInCity.csv';
        $fp = fopen($outFilename,'w');
        if(!$fp){
            echo 'Cant open file '.$outFilename;
            return '';
        }
        fwrite($fp,"id;city;ctrCode;url;desc;price\r\n");

        $j=0;
        foreach($citiesInfo as $cityToKey=>$cityToInfo){
            $j++;
            $minPrice = '';
            $url = 'https://voyanga.com/land/hotels/'.$cityToInfo['countryCode'].'/'.$cityToInfo['cityCode'].'/';
            if(isset($hotelPrices[$cityToKey])){
                $minPrice = $hotelPrices[$cityToKey];
            }
            $desc = "Отели в {$cityToInfo['casePre']}";
            //$sxe['url'] = $url;
            $str = "$j;{$cityToInfo['cityCode']};{$cityToInfo['countryCode']};{$url};{$desc};{$minPrice}\r\n";
            fwrite($fp,$str);
        }

        fclose($fp);

        $this->morphy = Yii::app()->morphy;



        $sql = 'SELECT * FROM `country`';

        $command=$connection->createCommand($sql);
        $dataReader=$command->query();

        $countriesInfo = array();
        while(($row=$dataReader->read())!==false) {
            $countryUp = mb_strtoupper($row['localRu'], 'utf-8');
            $countryMorph = array('caseAcc' => $this->getCase($countryUp, 'ВН'), 'casePre' => $this->getCase($countryUp, 'ПР'));
            $countriesInfo[$row['id']] = array('code'=>$row['code'],'casePre'=>$countryMorph['casePre']);
        }

        $sql = 'SELECT minPrice, countryId FROM (SELECT * FROM `hotel` ORDER BY minPrice) as tbl1 GROUP BY `countryId`';
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $hotelPrices = array();
        while(($row=$dataReader->read())!==false) {
            if(isset($countriesInfo[$row['countryId']])){
                $hotelPrices[$row['countryId']] = $row['minPrice'];
            }
        }

        // -------------------------- Hotels in Country
        $outFilename = $path.'/landingHotelsInCountry.csv';
        $fp = fopen($outFilename,'w');
        if(!$fp){
            echo 'Cant open file '.$outFilename;
            return '';
        }
        fwrite($fp,"id;ctrCode;url;desc;price\r\n");

        $j=0;
        foreach($countriesInfo as $countryId=>$countryInfo){
            $j++;
            $minPrice = '';
            $url = 'https://voyanga.com/land/hotels/'.$countryInfo['code'].'/';
            if(isset($hotelPrices[$countryId])){
                $minPrice = $hotelPrices[$countryId];
            }
            $desc = "Отели в {$countryInfo['casePre']}";
            //$sxe['url'] = $url;
            $str = "$j;{$countryInfo['code']};{$url};{$desc};{$minPrice}\r\n";
            fwrite($fp,$str);
        }

        fclose($fp);
        echo 'ok';
    }
}
