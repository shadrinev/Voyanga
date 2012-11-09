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
                                $cacheSubDir = md5('HotelDetail' . $hotel['id'] . '.xml');
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
}
