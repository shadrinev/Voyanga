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
                    $cityHotels = $HotelClient->getHotels($hotelCity['id']);
                    foreach ($cityHotels as $hotel) {
                        $tryAgain = 3;
                        while ($tryAgain) {
                            $hotelDetail = $HotelClient->hotelDetail($hotel['id']);
                            if (!$hotelDetail) {
                                echo "Cant get hotelDetail for hotelId:{$hotel['id']} cityId:{$hotelCity['id']}\n";
                                $tryAgain--;
                                $cachePath = Yii::getPathOfAlias('cacheStorage');
                                $cacheFilePath = $cachePath . '/HotelDetail' . $hotel['id'] . '.xml';
                                if (file_exists($cacheFilePath)) {
                                    unlink($cacheFilePath);
                                }

                            } else {
                                $tryAgain = 0;
                            }
                            usleep(300000);
                        }
                    }
                    echo count($cityHotels) . " hotel completed\n";
                }
                echo "Memory usage: " . memory_get_peak_usage() . "\n";
            }

        }

    }
}
