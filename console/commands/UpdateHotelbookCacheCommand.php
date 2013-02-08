<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 21.05.12
 * Time: 16:34
 * To change this template use File | Settings | File Templates.
 */
class UpdateHotelbookCacheCommand extends CConsoleCommand
{

    public static $sng = array('RU', 'UA', 'BY', 'AZ', 'AM', 'BG', 'GE', 'KZ', 'KG', 'LV', 'LT', 'MD', 'PL', 'SK', 'SI', 'TJ', 'TM', 'UZ');

    public function getHelp()
    {
        return <<<EOD
USAGE UpdateHotelbookCacheDictionaries [OPTIONS]
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
        if ($type == 'hotels') {
            echo Yii::app()->params['HotelBook']['uri']."\n";
            echo Yii::app()->params['HotelBook']['login']."\n";
            echo Yii::app()->params['HotelBook']['password']."\n";
            //die();
            Yii::import('site.common.modules.hotel.models.*');
            $HotelClient = new HotelBookClient();
            $HotelClient->synchronize(true);
            //$HotelClient
            $stateFile = 'hoteldetailstate.txt';
            try{
                $executionState = json_decode(@file_get_contents($stateFile));
            }catch (Exception $e){
                $executionState = false;
            }
            if(!$executionState || $executionState->endState){
                /** @var $executionState Object
                 *
                 * @property integer $countryId
                 * @property integer $cityId
                 * @property integer $hotelId
                 * @property integer $endState
                 * @property integer $stateDesc
                 *
                 * */
                $executionState = (object) array('countryId'=>null,'cityId'=>null,'hotelId'=>null,'endState'=>false,'stateDesc'=>'');
            }
            HotelBookClient::$updateProcess = true;
            $countries = false;
            while(!$countries){
                try{
                    $countries = $HotelClient->getCountries();
                }catch(Exception $e){
                    $countries = false;
                    sleep(60);
                    $executionState->stateDesc = 'bad countries req';
                    file_put_contents($stateFile,json_encode($executionState));
                }
            }
            $executionState->stateDesc = '';
            file_put_contents($stateFile,json_encode($executionState));
            $countryStart = false;
            $cityStart = false;
            $hotelStart = false;


            foreach ($countries as $country) {
                if ($executionState->countryId && !$countryStart) {
                    if ($executionState->countryId == $country['id']) {
                        $countryStart = true;
                    } else {
                        continue;
                    }
                } else {
                    $countryStart = true;
                }
                $executionState->countryId = $country['id'];
                //echo "process country with id: {$country['id']}\n";
                HotelBookClient::$downCountCacheFill = 1000500;
                $hotelCities = false;
                while($hotelCities === false){
                    try{
                        $hotelCities = $HotelClient->getCities($country['id']);
                    }catch (Exception $e){
                        $hotelCities = false;
                        sleep(60);
                        $executionState->stateDesc = 'bad cities req';
                        file_put_contents($stateFile,json_encode($executionState));
                    }
                }
                $executionState->stateDesc = '';
                file_put_contents($stateFile,json_encode($executionState));
                foreach ($hotelCities as $hotelCity) {
                    if ($executionState->cityId && !$cityStart) {
                        if ($executionState->cityId == $hotelCity['id']) {
                            $cityStart = true;
                        } else {
                            continue;
                        }
                    } else {
                        $cityStart = true;
                    }
                    $executionState->cityId = $hotelCity['id'];
                    //echo "process city with id: {$hotelCity['id']}\n";
                    HotelBookClient::$downCountCacheFill = 1000500;
                    //echo "Memory usage: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
                    $cityHotels = false;
                    while($cityHotels === false){
                        try{
                            $cityHotels = $HotelClient->getHotels($hotelCity['id']);
                        }catch (Exception $e){
                            $cityHotels = false;
                            sleep(60);
                            $executionState->stateDesc = 'bad hotels req';
                            file_put_contents($stateFile,json_encode($executionState));
                        }
                    }
                    $executionState->stateDesc = '';
                    file_put_contents($stateFile,json_encode($executionState));

                    foreach ($cityHotels as $hotel) {
                        if ($executionState->hotelId && !$hotelStart) {
                            if ($executionState->hotelId == $hotel['id']) {
                                $hotelStart = true;
                            } else {
                                continue;
                            }
                        } else {
                            $hotelStart = true;
                        }
                        $executionState->hotelId = $hotel['id'];
                        $tryAgain = 3;
                        while ($tryAgain) {
                            $hotelDetail = $HotelClient->hotelDetail($hotel['id']);
                            if (!$hotelDetail) {
                                //echo "Cant get hotelDetail for hotelId:{$hotel['id']} cityId:{$hotelCity['id']}\n";
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
                                    //echo "HotelError hotelId:{$hotel['id']} cityId:{$hotelCity['id']}\n";
                                }

                            } else {
                                $tryAgain = 0;
                                if(!$tryAgain){
                                    //echo "HotelOK hotelId:{$hotel['id']} cityId:{$hotelCity['id']}\n";
                                }
                            }
                            if(!HotelBookClient::$saveCache){
                                //$cachePath = Yii::getPathOfAlias('cacheStorage');
                                //echo 'input str: '.bin2hex('HotelDetail' . $hotel['id']).' ('.'HotelDetail' . $hotel['id'] .')';
                                //$cacheSubDir = md5('HotelDetail' . $hotel['id']);
                                //$cacheSubDir = substr($cacheSubDir,-3);
                                //$cacheFilePath = $cachePath . '/' . $cacheSubDir .'/HotelDetail' . $hotel['id'] . '.xml';
                                //echo "file don't old:".date('Y-m-d H:i:s',(filectime($cacheFilePath) + 3600*24*14)).(HotelBookClient::$updateProcess ? ' true' : ' false')." {$cacheFilePath}\n";
                            }
                            usleep(200000);
                            file_put_contents($stateFile,json_encode($executionState));
                        }
                    }
                    unset($cityHotels);
                    //echo count($cityHotels) . " hotel completed\n";
                }
                unset($hotelCities);
                //echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
            }
            $executionState->endState = 'end';
            echo "AllOK";

        }

    }
}
