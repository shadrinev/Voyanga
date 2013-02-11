<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 21.05.12
 * Time: 16:34
 * To change this template use File | Settings | File Templates.
 */
class MapHotelbookCitiesCommand extends CConsoleCommand
{

    public static $sng = array('RU', 'UA', 'BY', 'AZ', 'AM', 'BG', 'GE', 'KZ', 'KG', 'LV', 'LT', 'MD', 'PL', 'SK', 'SI', 'TJ', 'TM', 'UZ');

    public function getHelp()
    {
        return <<<EOD
USAGE yiic MapHotelbookCities
EOD;
    }

    /**
     * Execute the action.
     * @param array command line parameters specific for this command
     */
    public function actionIndex()
    {
        Yii::import('site.common.modules.hotel.models.*');
        $out = Yii::getPathOfAlias('console.runtime.mapping');
        if (!is_dir($out))
            mkdir($out);
        $countryFile = $out . '/countries';
        $outdir = $out . '/cities';
        $HotelClient = new HotelBookClient();
        $HotelClient->synchronize(true);
        if (!is_file($countryFile))
        {
            $countries = $HotelClient->getCountries();
            file_put_contents($countryFile, serialize($countries));
        }
        else
        {
            $countries = unserialize(file_get_contents($countryFile));
        }
        foreach ($countries as $country)
        {
            if (!is_dir($outdir))
                mkdir($outdir);
            $outfile = $outdir . '/' . $country['nameEn'];
            if (!is_file($outfile))
            {
                $hotelCities = $HotelClient->getCities($country['id']);
                file_put_contents($outfile, serialize($hotelCities));
            }
        }

        $missingCountry = array();
        $missingCities = array();
        $ambigousCities = array();
        $result = array();
        foreach ($countries as $i=>$country)
        {
            $hotelBookIdCountryId = $country['id'];
            $ourCountry = Country::model()->findByAttributes(array('hotelbookId'=>$hotelBookIdCountryId));
            if (!$ourCountry)
            {
                $missingCountry[] = $country['nameEn'];
                continue;
            }
            $ourId = $ourCountry->id;
            $infile = $outdir . '/' . $country['nameEn'];
            if (is_file($infile))
            {
                $cities = unserialize(file_get_contents($infile));
                $j = 0;
                foreach ($cities as $city)
                {
                    echo ($i)." of ".sizeof($countries).", ".(++$j)." of ".sizeof($cities)." {$country['nameRu']}/{$city['nameRu']}\n";
                    $ourCity = City::model()->findAllByAttributes(array('localRu'=>$city['nameRu'], 'countryId'=>$ourId));
                    if (sizeof($ourCity)==0)
                        $missingCities[] = $country['nameRu'] . ' / ' . $city['nameRu'];
                    elseif (sizeof($ourId)>1)
                        $ambigousCities = $country['nameRu'] . ' / ' . $city['nameRu'];
                    else
                    {
                        $oldHbid = intval($ourCity[0]->hotelbookId);
                        $newHbid = $city['id'];
                        if ($oldHbid != $newHbid)
                        {
                            $result[] = $country['nameRu'] . ' / ' .$ourCity[0]->localRu.' - old hotelbookid='.$oldHbid.' and new hotelbookid='.$newHbid;
                            $ourCity[0]->hotelbookId = $newHbid;
                            $ourCity[0]->update(array('hotelbookId'));
                        }
                    }
                }
            }
        }

        $missingCountryFile = $out . '/' . 'missingCountry';
        $missingCitiesFile = $out . '/' . 'missingCities';
        $ambigousCitiesFile = $out . '/' . 'ambigousCities';
        $resultFile = $out . '/' . 'result';

        file_put_contents($missingCountryFile, implode("\n", $missingCountry));
        file_put_contents($missingCitiesFile, implode("\n", $missingCities));
        file_put_contents($ambigousCitiesFile, implode("\n", $ambigousCities));
        file_put_contents($resultFile, implode("\n", $result));
    }
}
