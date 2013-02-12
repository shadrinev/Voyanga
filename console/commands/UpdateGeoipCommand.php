<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 21.05.12
 * Time: 16:34
 * To change this template use File | Settings | File Templates.
 */
class UpdateGeoipCommand extends CConsoleCommand
{

    public static $sng = array('RU', 'UA', 'BY', 'AZ', 'AM', 'BG', 'GE', 'KZ', 'KG', 'LV', 'LT', 'MD', 'PL', 'SK', 'SI', 'TJ', 'TM', 'UZ');

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
    public function actionIndex($type = 'hotels', $filename = '', $countryStartId = null, $cityStartId = null)
    {
        $cityGeoDB = 'cities.txt';
        $cityGeoIDs = array();
        if(file_exists($cityGeoDB))
        {
            $cityFp = fopen($cityGeoDB, "r");
            while (!feof($cityFp)) {
                $line = fgets($cityFp);
                if($line){
                    preg_match('#^(\d+)\t+(\W+?)\t+.+$#', $line, $match);
                    //print_r($match);
                    //break;
                    $id = intval($match[1]);
                    $cityName = $match[2];
                    $cityGeoIDs[$id] = iconv('CP1251','UTF8',$cityName);
                }
            }
            echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
            $connection=Yii::app()->db;
            $sql = 'truncate geoip';
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $fileRuBase = "cidr_optim.txt";
            $fp = fopen($fileRuBase, "r");
            $addedCities = 0;
            $skippedCities = 0;
            $addedCountries = 0;
            $page = "";
            while (!feof($fp)) {
                $page .= fgets($fp, 1024);
                $pos = strpos($page, "\n");
                while ($pos !== false) {
                    if ($pos > 0) {
                        $line = substr ($page, 0, $pos);
                        $page = substr($page, $pos + 1);
                        if (preg_match("#^(\d+)\s+(\d+)\s+[0-9\.]+ - [0-9\.]+\s+([A-Z]+)\s+([-0-9]+)$#", $line, $match)) {
                            $begin_ip = $match[1];
                            $end_ip = $match[2];
                            $country_code = $match[3];

                            $city_id = $match[4];
                            if ($city_id != "-") {
                                $cityName = $cityGeoIDs[intval($city_id)];
                                try{
                                    $country = Country::getCountryByCode($country_code);
                                }catch (Exception $e){
                                    $country = (object) array('id'=>false);
                                }
                                if($country->id){
                                    $cities = CityManager::getCities($cityName,$country->id);
                                }else{
                                    $cities = CityManager::getCities($cityName);
                                }
                            }else{
                                $pos = strpos ($page, "\n");
                                continue;
                            }
                            if(isset($cities) && $cities){
                                $geoip = new Geoip();
                                $geoip->beginIp = $begin_ip;
                                $geoip->endIp = $end_ip;
                                $geoip->cityId = $cities[0]['id'];
                                $geoip->countryId = City::getCityByPk($cities[0]['id'])->country->id;
                                $addedCities++;
                                $geoip->save();
                                unset($geoip);
                            }elseif($country->id){
                                $geoip = new Geoip();
                                $geoip->beginIp = $begin_ip;
                                $geoip->endIp = $end_ip;
                                $geoip->countryId = $country->id;
                                $addedCountries++;
                                if($geoip->save()){

                                }else{

                                }
                                unset($geoip);
                            }else{
                                $skippedCities++;
                            }
                            if(isset($cities))
                                unset($cities);
                        }
                    }
                    $pos = strpos ($page, "\n");
                }
            }
            echo "Added countries blocks: $addedCountries\n";
            echo "Added cities blocks: $addedCities\n";
            echo "Skipped blocks: $skippedCities\n";
            unset($pos);
            unset($page);
        }else{
            echo "file $cityGeoDB not found";
        }

    }

    public function findCity($cityName,$countryId,$region,$lat,$lng){
        $cityIds = array();

        $criteria = new CDbCriteria();
        $criteria->limit = 12;
        $criteria->params[':localRu'] = $cityName . '%';
        $criteria->params[':localEn'] = $cityName . '%';
        if($countryId){
            $criteria->addCondition('t.countryId = :countryId');
            $criteria->params[':countryId'] = $countryId;
        }
        if($region){
            $criteria->addCondition('t.stateCode = :stateCode');
            $criteria->params[':stateCode'] = $region;
        }
        $criteria->addCondition('t.maxmindId IS NULL');


        $criteria->addCondition('t.localRu LIKE :localRu OR t.localEn LIKE :localEn');
        if ($cityIds)
        {
            $criteria->addCondition('t.id NOT IN (' . join(',', $cityIds) . ')');
        }
        //$criteria->with = 'country';
        $criteria->order = 't.position desc';
        /** @var $cities City[] */
        $cities = City::model()->findAll($criteria);
        $coords = (abs($lat) > 0.05) || (abs($lng) > 0.05);

        if ($cities)
        {
            foreach ($cities as $city)
            {
                if($coords){
                    if(abs($city->latitude) > 0.05 && abs($city->longitude) > 0.05){
                        $dist = UtilsHelper::calculateTheDistance($lat,$lng,$city->latitude,$city->longitude);
                        if($dist < 178000){
                            return $city;
                        }
                    }
                }
            }
            return $cities[0];
        }


        $criteria = new CDbCriteria();
        $criteria->limit = 12;
        if (UtilsHelper::countRussianCharacters($cityName) > 0)
        {
            $nameRu = $cityName;
        }
        else
        {
            $nameRu = UtilsHelper::cityNameToRus($cityName);
        }
        $metaphoneRu = UtilsHelper::ruMetaphone($nameRu);

        if ($metaphoneRu)
        {
            $criteria->params[':metaphoneRu'] = $metaphoneRu;

            $criteria->addCondition('t.metaphoneRu = :metaphoneRu');
            $criteria->addCondition('t.maxmindId IS NULL');

            if($countryId){
                $criteria->addCondition('t.countryId = :countryId');
                $criteria->params[':countryId'] = $countryId;
            }
            //$criteria->with = 'country';
            $criteria->order = 't.position desc';
            $cities = City::model()->findAll($criteria);

            if ($cities)
            {
                foreach ($cities as $city)
                {
                    if($coords){
                        if(abs($city->latitude) > 0.05 && abs($city->longitude) > 0.05){
                            $dist = UtilsHelper::calculateTheDistance($lat,$lng,$city->latitude,$city->longitude);
                            if($dist < 178000){
                                return $city;
                            }
                        }
                    }
                }
                return $cities[0];
            }
        }
        return false;
    }

    public function actionMaxmindCities(){

        echo "next base maxmind\n";
        $cityGeoDB = 'GeoLiteCity-Location.csv';
        $cityGeoIDs = array();
        $citiesFound = 0;
        $stateFile = 'maxmind_cities.txt';
        try{
            $executionState = json_decode(@file_get_contents($stateFile));
        }catch (Exception $e){
            $executionState = false;
        }
        if(!$executionState || $executionState->endState){
            $executionState = (object) array('cityId'=>null,'endState'=>false,'stateDesc'=>'');
        }
        $startParse = false;
        $citiesNotFound = 0;
        if(file_exists($cityGeoDB))
        {
            $cityFp = fopen($cityGeoDB, "r");
            $line = fgets($cityFp);
            $line = fgets($cityFp);
            $i=0;

            echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
            while (!feof($cityFp)) {
                $line = fgets($cityFp);
                $i++;
                if($line){
                    preg_match('#^(\d+),"(\w+)","(\w+)","(.*?)",".*?",([-\.0-9]*),([-\.0-9]*),.*?,.*?$#', $line, $match);
                    //print_r($match);
                    //break;
                    if($match){

                        $id = intval($match[1]);
                        if(!$startParse && $executionState->cityId && $executionState->cityId != $id){
                            continue;
                        }else{
                            $executionState->cityId = $id;
                            $startParse = true;
                            file_put_contents($stateFile,json_encode($executionState));
                        }
                        $country_code = $match[2];
                        $region = $match[3];
                        $cityName = iconv('CP1251','UTF8',$match[4]);
                        $lat = $match[5];
                        $long = $match[6];
                        //$cityGeoIDs[$id] = array('cc'=>$country_code,'re'=>$region,'cn'=>$cityName,'lat'=>$lat,'lng'=>$long);
                        if(!$country_code){
                            continue;
                        }
                        try{
                            $country = Country::getCountryByCode($country_code);
                            //$countriesHave[$country_code] = true;
                        }catch (Exception $e){
                            $country = (object) array('id'=>false);
                            //$countriesDont[$country_code] = true;
                        }
                        if($country->id){
                            $city = $this->findCity($cityName,$country->id,$region,$lat,$long);
                            if($city){
                                $city->maxmindId = $id;
                                $city->save();
                                $citiesFound++;
                                unset($city);
                            }else{
                                $citiesNotFound++;
                            }
                        }

                        if(($i % 1000) == 0){
                            echo "parsed $i lines\n";
                            echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
                        }
                    }
                }
            }
        }
        echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
        echo "city found: {$citiesFound} city undef: {$citiesNotFound}\n";
    }

    public function actionMaxmindBase(){

        echo "parse base maxmind\n";
        $cityGeoDB = 'GeoLiteCity-Blocks.csv';
        $cityGeoIDs = array();
        $citiesFound = 0;
        $maxCntr = 'O1,AP,EU,AD,AE,AF,AG,AI,AL,AM,AN,AO,AQ,AR,AS,AT,AU,AW,AZ,BA,BB,BD,BE,BF,BG,BH,BI,BJ,BM,BN,BO,BR,BS,BT,BV,BW,BY,BZ,CA,CC,CD,CF,CG,CH,CI,CK,CL,CM,CN,CO,CR,CU,CV,CX,CY,CZ,DE,DJ,DK,DM,DO,DZ,EC,EE,EG,EH,ER,ES,ET,FI,FJ,FK,FM,FO,FR,GA,GB,GD,GE,GF,GH,GI,GL,GM,GN,GP,GQ,GR,GS,GT,GU,GW,GY,HK,HM,HN,HR,HT,HU,ID,IE,IL,IN,IO,IQ,IR,IS,IT,JM,JO,JP,KE,KG,KH,KI,KM,KN,KP,KR,KW,KY,KZ,LA,LB,LC,LI,LK,LR,LS,LT,LU,LV,LY,MA,MC,MD,MG,MH,MK,ML,MM,MN,MO,MP,MQ,MR,MS,MT,MU,MV,MW,MX,MY,MZ,NA,NC,NE,NF,NG,NI,NL,NO,NP,NR,NU,NZ,OM,PA,PE,PF,PG,PH,PK,PL,PM,PR,PS,PT,PW,PY,QA,RE,RO,RU,RW,SA,SB,SC,SD,SE,SG,SH,SI,SJ,SK,SL,SM,SN,SO,SR,ST,SV,SY,SZ,TC,TD,TF,TG,TH,TJ,TK,TM,TN,TO,TR,TT,TV,TW,TZ,UA,UG,UM,US,UY,UZ,VA,VC,VE,VG,VI,VN,VU,WF,WS,YE,YT,RS,ZA,ZM,ME,ZW,A1,A2';
        $maxCntrArr = explode(',',$maxCntr);
        $maxCntrs = array();
        foreach($maxCntrArr as $key=>$code){
            $maxCntrs[($key+1)] = $code;
        }
        $maxCntrs = array_flip($maxCntrs);
        //print_r($maxCntrs);
        $connection=Yii::app()->db;


        $sql = 'SELECT id,maxmindId,countryId
            FROM `city`
            WHERE `maxmindId` IS NOT NULL ';

        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $maxmindCityMap = array();
        $maxmindCountryMap = array();
        while(($row=$dataReader->read())!==false) {
            $maxId = intval($row['maxmindId']);
            $cityId = intval($row['id']);
            $countryId = intval($row['countryId']);
            $maxmindCityMap[$maxId] = $cityId;
            $maxmindCountryMap[$maxId] = $countryId;
        }

        $sql = 'SELECT id,code
            FROM `country`';

        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        while(($row=$dataReader->read())!==false) {
            $countryId = intval($row['id']);
            if(isset($maxCntrs[$row['code']])){
                $maxmindCountryMap[$maxCntrs[$row['code']]] = $countryId;
            }
        }
        echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
        echo "count:".count($maxmindCountryMap);
        die();
        $stateFile = 'maxmind_base.txt';
        try{
            $executionState = json_decode(@file_get_contents($stateFile));
        }catch (Exception $e){
            $executionState = false;
        }
        if(!$executionState || $executionState->endState){
            $executionState = (object) array('line'=>null,'endState'=>false,'stateDesc'=>'');
        }

        $startParse = false;
        $citiesNotFound = 0;
        if(file_exists($cityGeoDB))
        {
            $cityFp = fopen($cityGeoDB, "r");
            $line = fgets($cityFp);
            $line = fgets($cityFp);
            $i=0;

            echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
            while (!feof($cityFp)) {
                $line = fgets($cityFp);
                $i++;
                if(!$startParse && $executionState->line && $executionState->line != $i){
                    continue;
                }else{
                    $executionState->line = $i;
                    $startParse = true;
                    file_put_contents($stateFile,json_encode($executionState));
                }
                if($line){
                    preg_match('#^"(\w+)","(\w+)","(\w+)"$#', $line, $match);
                    //print_r($match);
                    //break;
                    if($match){

                        $begin_ip = intval($match[1]);
                        $end_ip = intval($match[2]);
                        $loc_id = intval($match[3]);
                        //$cityGeoIDs[$id] = array('cc'=>$country_code,'re'=>$region,'cn'=>$cityName,'lat'=>$lat,'lng'=>$long);
                        if(!isset($maxmindCountryMap[$loc_id])){
                            continue;
                        }else{
                            $attrs = array('beginIp','endIp','cityId','countryId','position');
                            $fields = array('beginIp'=>$begin_ip,'endIp'=>$end_ip,'position'=>0);
                            $fields['countryId'] = $maxmindCountryMap[$loc_id];
                            if(isset($maxmindCityMap[$loc_id])){
                                $fields['cityId'] = $maxmindCityMap[$loc_id];
                                $citiesFound++;
                            }else{
                                $fields['cityId'] = null;
                                $citiesNotFound++;
                            }

                            $vals = array();
                            foreach($attrs as $attrName){
                                $attrVal = $fields[$attrName];
                                if($attrVal !== null){
                                    $vals[] = "'".addslashes($attrVal)."'";
                                }else{
                                    $vals[] = 'NULL';
                                }
                            }
                            $values[] = "(".implode(',',$vals).")";

                            if(count($values) > 1000){
                                $sql = 'INSERT INTO geoip ('.implode(',',$attrs).') VALUES '.implode(',',$values);
                                //$sql .= " (".implode(',',$in).")";
                                $command=$connection->createCommand($sql);
                                $command->execute();
                            }
                        }


                        if(($i % 1000) == 0){
                            echo "parsed $i lines\n";
                            echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
                        }
                    }
                }
            }
        }
        echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
        echo "city found: {$citiesFound} city undef: {$citiesNotFound}\n";
    }
}
