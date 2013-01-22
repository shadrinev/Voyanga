<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 16:42
 */
class CacheCommand extends CConsoleCommand
{
    //skyscanner URLs section
    const SKYSCANNER_CITY = 'http://www.skyscanner.ru/dataservices/geo/v1.0/autosuggest/uk/ru/###?&oppositePlaceId=berl&isDestination=false&ccy=rub';
    const SKYSCANNER_CACHE = 'http://www.skyscanner.ru/api.ashx?mode=Data&output=xml&method=flightsearch&rtn=1&oplace={{from}}&iplace={{to}}&ddate={{ddate}}&rdate={{rdate}}&currency=RUB&adults=1&children=0&infants=0&airlines=&otimeofday=any&itimeofday=any&allowestimates=1&di=0&key=988b5030-6b72-435e-8758-e30b327c2be4';

    private $intRouters;
    private $cities;

    public function actionSync()
    {
        $executor = Yii::app()->syncCacheExecuter;
        $executor->run();
    }

    public function actionClean()
    {
        $query = "DELETE FROM ".FlightCache::model()->tableName()." WHERE `dateFrom` < NOW();";
        $deleted = Yii::app()->db->createCommand($query)->execute();
        echo "Deleted flight rows: ".$deleted."\n";

        $query = "DELETE FROM ".HotelCache::model()->tableName()." WHERE `dateFrom` < NOW();";
        $deleted = Yii::app()->db->createCommand($query)->execute();
        echo "Deleted hotel rows: ".$deleted."\n";
    }

    public function actionParseSkyScanner()
    {
        Yii::import('console.models.*');

        $routesFilename = 'aviastat.txt';
        $routesFolder = 'console.data_files';

        $skyscannerCitiesFile = 'skyscanner_cities.serialized';
        $ourCitiesFile = 'our_cities.serialized';

        $routesPath = Yii::getPathOfAlias($routesFolder) . '/' .$routesFilename;
        $routes = file_get_contents($routesPath);
        $skyscannerCitiesPath = Yii::getPathOfAlias($routesFolder). '/' . $skyscannerCitiesFile;
        $ourCitiesPath = Yii::getPathOfAlias($routesFolder). '/' . $ourCitiesFile;

        //1. first - parse incoming file and determine different cities
        $cities = $this->parseRoutes($routes);
        //2. get internal skyscanner's placeIds for each city
        if (is_file($skyscannerCitiesPath))
            $skyscannerCities = unserialize(file_get_contents($skyscannerCitiesPath));
        else
        {
            $skyscannerCities = $this->getSkyScannerCodes($cities);
            file_put_contents($skyscannerCitiesPath, serialize($skyscannerCities));
        }

        $this->cities = unserialize(file_get_contents($ourCitiesPath));

        //3. build urls for parsing skyscanner with routes
        $urls = $this->buildUrls($routes, $skyscannerCities);

        //4. capture data from skyscanner using urls
        $this->grabSkyScanner($urls);
    }

    public function parseRoutes($routes)
    {
        $cities = array();
        $routes = explode("\n", $routes);
        foreach ($routes as $route)
        {
            $citiesTmp = explode("-", $route);
            $citiesTmp2 = array();
            foreach ($citiesTmp as $city)
            {
                $citiesTmp2 = array_merge($citiesTmp2, explode('/', $city));
            }
            foreach ($citiesTmp2 as $city)
            {
                $city = trim($city);
                if (isset($cities[$city]))
                    $cities[$city]++;
                else
                    $cities[$city] = 1;
            }
        }
        arsort($cities);
        return $cities;
    }

    public function getSkyScannerCodes($cities)
    {
        $codes = array();
        $i = 0;
        foreach ($cities as $cityName => $amount)
        {
            $url = str_replace('###', rawurlencode($cityName), self::SKYSCANNER_CITY);
            echo 'Trying to get code for '.$cityName.". ";
            try
            {
                $response = file_get_contents($url);
                $result = CJSON::decode($response);
                if (($result) and (isset($result[0]['CityId'])))
                {
                    $codes[$cityName] = $result[0]['CityId'];
                }
                echo "Success.\n";
            }
            catch (Exception $e)
            {
                echo "Failed11.\n";
            }
        }
        return $codes;
    }

    public function buildUrls($routes, $skyscannerCities)
    {
        $urls = array();
        $routes = explode("\n", $routes);
        foreach ($routes as $route)
        {
            $citiesTmp = explode("-", $route);
            if (sizeof($citiesTmp) != 3)
                continue;
            $from = explode('/', $citiesTmp[0]);
            $to = explode('/', $citiesTmp[1]);
            foreach ($from as $cityFrom)
            {
                foreach ($to as $cityTo)
                {
                    $nFrom = trim($cityFrom);
                    $nTo = trim($cityTo);
                    if (isset($skyscannerCities[$nFrom]) and (isset($skyscannerCities[$nTo])))
                    {
                        $url = self::SKYSCANNER_CACHE;
                        $from = '1302';
                        $to = '1302';
                        $skyscannerCityFrom = $skyscannerCities[$nFrom];
                        $skyscannerCityTo = $skyscannerCities[$nTo];
                        $url = str_replace('{{from}}', $skyscannerCityFrom, $url);
                        $url = str_replace('{{to}}', $skyscannerCityTo, $url);
                        $url = str_replace('{{ddate}}', $from, $url);
                        $url = str_replace('{{rdate}}', $to, $url);
                        $name = 'url_'.$nFrom.'_'.$nTo.'_'.$from.'_'.$to;
                        $name = str_replace(' ', '+', $name);
                        if (!isset($this->cities[$nFrom]))
                        {
                            continue;
                        }
                        else
                        {
                            $fromCities = $this->cities[$nFrom];
                        }

                        if (!isset($this->cities[$nTo]))
                        {
                            continue;
                        }
                        else
                        {
                            $toCities = $this->cities[$nTo];
                        }

                        $this->intRouters[$url] = array(
                            $fromCities,
                            $toCities,
                        );
                        $urls[$name] = $url;
                    }
                }
            }
        }
        return $urls;
    }

    public function grabSkyScanner($urls)
    {
        $folder = 'console.data_files.skyscanner';
        foreach ($urls as $name => $url)
        {
            $saveTo = Yii::getPathOfAlias($folder). '/' . $name . '.xml';
            try
            {
                if (is_file($saveTo))
                {
                    $this->analyzeFile($saveTo, $url);
                    continue;
                }
                echo 'Grabbing using '.$name.'. ';
                $response = file_get_contents_curl($url);
                usleep(200000);
                if ($response === false)
                    throw new CException('Failed');
                echo "Success.\n";
                file_put_contents($saveTo, $response);
            }
            catch (Exception $e)
            {
                echo "Failed22.\n";
            }
        }
    }

    public function analyzeFile($path, $url)
    {
        echo "Analyzing $path.\n";
        $xml = simplexml_load_file($path);
        echo "XML Loaded\n";
        $set1 = $xml->Set1->Data;
        $success = 0;
        $failed = 0;
        foreach ($set1->Row as $row)
        {
            if (strlen($row->Price) != 0)
            {
                if ($this->addOneWayToCache($row, $url))
                    $success++;
                else
                    $failed++;
            }
        }
        echo "Successfully saved: {$success}. Failed: $failed \n";
    }

    public function addOneWayToCache($row, $url)
    {
        $price = $row->Price;
        $codes = explode('*', $row->WebsiteFlightCode);
        if (sizeof($codes)>1)
        {
            $airline = $codes[2];
            $depAirportDate = $codes[1];
            $arrAirportDate = end($codes);

            $fromAirp = Airport::model()->findByAttributes(array('code'=>substr($depAirportDate,0,3)));
            if (!$fromAirp)
            {
                echo "Airport $depAirportDate not defined \n";
                return false;
            }
            $toAirp = Airport::model()->findByAttributes(array('code'=>substr($arrAirportDate,0,3)));
            if (!$fromAirp)
            {
                echo "Airport $arrAirportDate not defined \n";
                return false;
            }
            $from = $fromAirp->cityId;
            $to = $toAirp->cityId;
        }
        else
        {
            $airline = '';
            $from = $this->intRouters[$url][0];
            $to = $this->intRouters[$url][1];
            if (!$from || !$to)
            {
                echo "Cities not defined \n";
                return false;
            }
        }
        $time = strtotime($row->DepartureDateTime);
        if ($time == 0)
        {
            return true;
        }
        $dateFrom = date('Y-m-d', $time);

        $flightCache = FlightCache::model()->findByAttributes(array(
           'from' => $from,
           'to' => $to,
           'dateFrom' => $dateFrom,
           'dateBack' => '0000-00-00'
        ));
        if (!$flightCache)
            $flightCache = new FlightCache();
        else
        {
            if ($flightCache->priceBestPrice > $price)
                echo "Updating record $from - $to at $dateFrom ...\n";
            else
                return true;
        }

        $flightCache->from = $from;
        $flightCache->to = $to;
        $flightCache->dateFrom = $dateFrom;
        $flightCache->priceBestPrice = $price;
        $flightCache->transportBestPrice = $airline;
        $flightCache->validatorBestPrice = $airline;
        return $flightCache->save();
    }
}
