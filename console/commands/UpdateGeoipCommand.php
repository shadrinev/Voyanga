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






        return true;
        if ($type == 'hotels') {
            echo Yii::app()->params['HotelBook']['uri']."\n";
            echo Yii::app()->params['HotelBook']['login']."\n";
            echo Yii::app()->params['HotelBook']['password']."\n";
            //die();
            Yii::import('site.common.modules.hotel.models.*');
            $HotelClient = new HotelBookClient();
            $HotelClient->synchronize(true);
            //$HotelClient
            HotelBookClient::$updateProcess = true;
            $countries = $HotelClient->getCountries();
            $countryStart = false;
            $cityStart = false;


            foreach ($countries as $country) {
                if ($countryStartId && !$countryStart) {
                    if ($countryStartId == $country['id']) {
                        $countryStart = true;
                    } else {
                        continue;
                    }
                } else {
                    $countryStart = true;
                }
                echo "process country with id: {$country['id']}\n";
                HotelBookClient::$downCountCacheFill = 1000500;
                $hotelCities = $HotelClient->getCities($country['id']);
                foreach ($hotelCities as $hotelCity) {
                    if ($cityStartId && !$cityStart) {
                        if ($cityStartId == $hotelCity['id']) {
                            $cityStart = true;
                        } else {
                            continue;
                        }
                    } else {
                        $cityStart = true;
                    }
                    echo "process city with id: {$hotelCity['id']}\n";
                    HotelBookClient::$downCountCacheFill = 1000500;
                    echo "Memory usage: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
                    $cityHotels = $HotelClient->getHotels($hotelCity['id']);
                    foreach ($cityHotels as $hotel) {
                        $tryAgain = 3;
                        while ($tryAgain) {
                            $hotelDetail = $HotelClient->hotelDetail($hotel['id']);
                            if (!$hotelDetail) {
                                echo "Cant get hotelDetail for hotelId:{$hotel['id']} cityId:{$hotelCity['id']}\n";
                                $tryAgain--;
                                $cachePath = Yii::getPathOfAlias('cacheStorage');
                                $cacheSubDir = md5('HotelDetail' . $hotel['id']);
                                $cacheSubDir = substr($cacheSubDir,-3);
                                $cacheFilePath = $cachePath . '/' . $cacheSubDir .'/HotelDetail' . $hotel['id'] . '.xml';
                                if (file_exists($cacheFilePath)) {
                                    unlink($cacheFilePath);
                                }
                                unset($cachePath);
                                unset($cacheSubDir);
                                unset($cacheFilePath);
                                if(!$tryAgain){
                                    echo "HotelError hotelId:{$hotel['id']} cityId:{$hotelCity['id']}\n";
                                }

                            } else {
                                $tryAgain = 0;
                                if(!$tryAgain){
                                    echo "HotelOK hotelId:{$hotel['id']} cityId:{$hotelCity['id']}\n";
                                }
                            }
                            if(!HotelBookClient::$saveCache){
                                $cachePath = Yii::getPathOfAlias('cacheStorage');
                                //echo 'input str: '.bin2hex('HotelDetail' . $hotel['id']).' ('.'HotelDetail' . $hotel['id'] .')';
                                $cacheSubDir = md5('HotelDetail' . $hotel['id']);
                                $cacheSubDir = substr($cacheSubDir,-3);
                                $cacheFilePath = $cachePath . '/' . $cacheSubDir .'/HotelDetail' . $hotel['id'] . '.xml';
                                echo "file don't old:".date('Y-m-d H:i:s',(filectime($cacheFilePath) + 3600*24*14)).(HotelBookClient::$updateProcess ? ' true' : ' false')." {$cacheFilePath}\n";
                            }
                            usleep(200000);
                        }
                    }
                    unset($cityHotels);
                    //echo count($cityHotels) . " hotel completed\n";
                }
                unset($hotelCities);
                echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
            }

        }

    }

    public function actionMaxmindCities(){

        echo "next base maxmind\n";
        $cityGeoDB = 'GeoLiteCity-Location.csv';
        $cityGeoIDs = array();
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
                        $country_code = $match[2];
                        $region = $match[3];
                        $cityName = iconv('CP1251','UTF8',$match[4]);
                        $lat = $match[5];
                        $long = $match[6];
                        $cityGeoIDs[$id] = array('cc'=>$country_code,'re'=>$region,'cn'=>$cityName,'lat'=>$lat,'lng'=>$long);
                        if(($i % 10000) == 0){
                            echo "parsed $i lines\n";
                            echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
                        }
                    }
                }
            }
        }
        echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
    }
}
