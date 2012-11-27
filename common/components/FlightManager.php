<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 14.11.12
 * Time: 14:19
 */
class FlightManager
{
    static public function injectForBe($flightVoyages, $injectSearchParams=false)
    {
        $newFlights = array();
        try{
            foreach ($flightVoyages as $key => $flight)
            {
                $newFlight = $flight;
                if ($injectSearchParams)
                {
                    $newFlight['serviceClass'] = $injectSearchParams['serviceClass'];
                    $newFlight['freeWeight'] = ($newFlight['serviceClass'] == 'E') ? $flight['economFreeWeight'] : $flight['businessFreeWeight'];
                    $newFlight['freeWeightDescription'] = ($newFlight['serviceClass'] == 'E') ? $flight['economDescription'] : $flight['businessDescription'];
                    unset($newFlight['economFreeWeight']);
                    unset($newFlight['businessFreeWeight']);
                    unset($newFlight['economDescription']);
                    unset($newFlight['businessDescription']);
                }
                $newFlights[] = $newFlight;
            }
            return $newFlights;
        }
        catch (Exception $e)
        {
            Yii::log('Error: '.$e->getMessage(),". Data: ".CVarDumper::dumpAsString($flightVoyages), CLogger::LEVEL_ERROR);
            throw $e;
        }
    }

    static public function createSiblingsData(FlightSearchParams $flightSearchParams)
    {
        if ($flightSearchParams->isRoundTrip())
        {
            return self::createSiblingsDataForRoundTrip($flightSearchParams);
        }
        else
        {
            return self::createSiblingsDataForDirectTrip($flightSearchParams);
        }
    }

    static public function createSiblingsDataForDirectTrip(FlightSearchParams $flightSearchParams)
    {
        $result = array();
        $siblingDays = self::buildSiblingDays($flightSearchParams);
        foreach ($siblingDays as $siblingDay)
        {
            $criteria = new CDbCriteria();
            $criteria->select = "priceBestPrice";
            $criteria->compare('`from`', $flightSearchParams->routes[0]->departureCityId);
            $criteria->compare('`to`', $flightSearchParams->routes[0]->arrivalCityId);
            $criteria->compare('`dateFrom`', $siblingDay->format('Y-m-d'));
            $criteria->compare('`dateBack`', '0000-00-00');
            $cacheItem = FlightCache::model()->find($criteria);
            if ($cacheItem)
                $value = $cacheItem->priceBestPrice * $flightSearchParams->totalPassengers;
            else
                $value = false;
            $result[] = $value;
        }
        return $result;
    }

    public static function buildSiblingDays($flightSearchParams)
    {
        $siblingDays = array();
        $siblingsDaysOffset = range(-3, 3);
        $currentDay = DateTime::createFromFormat('Y-m-d', $flightSearchParams->routes[0]->departureDate);
        foreach ($siblingsDaysOffset as $offset)
        {
            if ($offset < 0)
            {
                $absOffset = abs($offset);
                $offsetDate = clone($currentDay);
                $siblingDays[] = $offsetDate->sub(new DateInterval('P' . $absOffset . 'D'));
            }
            if ($offset == 0)
            {
                $siblingDays[] = clone($currentDay);
            }
            if ($offset > 0)
            {
                $absOffset = $offset;
                $offsetDate = clone($currentDay);
                $siblingDays[] = $offsetDate->add(new DateInterval('P' . $absOffset . 'D'));
            }
        }
        return $siblingDays;
    }

    static public function createSiblingsDataForRoundTrip(FlightSearchParams $flightSearchParams)
    {
        $siblingDays = self::buildSiblingDays($flightSearchParams);
        $result = array();
        foreach ($siblingDays as $siblingDayDirect)
        {
            $oneWay = array();
            foreach ($siblingDays as $siblingDayBack)
            {
                $criteria = new CDbCriteria();
                $criteria->select = "priceBestPrice";
                $criteria->compare('`from`', $flightSearchParams->routes[0]->departureCityId);
                $criteria->compare('`to`', $flightSearchParams->routes[0]->arrivalCityId);
                $criteria->compare('`dateFrom`', $siblingDayDirect->format('Y-m-d'));
                $criteria->compare('`dateBack`', $siblingDayBack->format('Y-m-d'));
                $cacheItem = FlightCache::model()->find($criteria);
                if ($cacheItem)
                    $value = ceil(($cacheItem->priceBestPrice * $flightSearchParams->totalPassengers) / 2);
                else
                {
                    $value = false;
                }
                $oneWay[] = $value;
            }
            $result[] = $oneWay;
        }
        return $result;
    }
}
