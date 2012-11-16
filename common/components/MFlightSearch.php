<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 18.05.12
 * Time: 17:02
 */
class MFlightSearch extends CComponent
{
    /**
     * @static
     * @param FlightForm $flightForm
     * @return FlightSearchParams
     */
    private static function buildSearchParams(FlightForm $flightForm)
    {
        $flightSearchParams = new FlightSearchParams();
        foreach ($flightForm->routes as $route)
        {
            $departureDate = date('d.m.Y', strtotime($route->departureDate));
            $flightSearchParams->addRoute(array(
                'adult_count' => $flightForm->adultCount,
                'child_count' => $flightForm->childCount,
                'infant_count' => $flightForm->infantCount,
                'departure_city_id' => $route->departureCityId,
                'arrival_city_id' => $route->arrivalCityId,
                'departure_date' => $route->departureDate,
            ));
            if ($route->isRoundTrip)
            {
                $returnDate = date('d.m.Y', strtotime($route->backDate));
                $flightSearchParams->addRoute(array(
                    'adult_count' => $flightForm->adultCount,
                    'child_count' => $flightForm->childCount,
                    'infant_count' => $flightForm->infantCount,
                    'departure_city_id' => $route->arrivalCityId,
                    'arrival_city_id' => $route->departureCityId,
                    'departure_date' => $returnDate
                ));
            }
            $flightSearchParams->flight_class = $flightForm->flightClass;
        }
        return $flightSearchParams;
    }

    /**
     * @static
     * @param FlightForm $flightForm
     * @return mixed
     */
    public static function getAllPricesAsJson($flightForm)
    {
        if (!$flightForm instanceof FlightForm)
            throw new CHttpException(500, 'MFlightSearch requires instance of FlightForm as incoming param');
        $flightSearchParams = self::buildSearchParams($flightForm);
        $cacheId = md5(serialize($flightSearchParams));
        Yii::app()->pCache->set('flightSearchParams' . $cacheId, $flightSearchParams, appParams('flight_search_cache_time'));
        $fs = new FlightSearch();
        $fs->status = 1;
        $fs->requestId = '1';
        $fs->data = '{}';
        $variants = $fs->sendRequest($flightSearchParams, false);
        $json = $variants->getAsJson(array('pCacheId'=>$cacheId));
        return $json;
    }

    public static function getOptimalPrice($fromCityId, $toCityId, $date, $returnDate=false, $forceUpdate = false)
    {
        $flightForm = new FlightForm();
        $flightForm->adultCount = 1;
        $flightForm->childCount = 0;
        $flightForm->infantCount = 0;
        $route = new RouteForm();
        $route->departureCityId = $fromCityId;
        $route->arrivalCityId = $toCityId;
        $route->departureDate = $date;
        if ($returnDate)
        {
            $route->isRoundTrip = true;
            $route->backDate = $returnDate;
        }
        $flightForm->routes[] = $route;
        $flightSearchParams = self::buildSearchParams($flightForm);
        $fs = new FlightSearch();
        $fs->status = 1;
        $fs->requestId = '1';
        $fs->data = '{}';
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array('`from`'=>$fromCityId, '`to`'=>$toCityId));
        $criteria->addCondition('`dateFrom` = STR_TO_DATE("'.$date.'", "%d.%m.%Y")');
        if ($returnDate)
        {
            $criteria->addCondition('`dateBack` = STR_TO_DATE("'.$returnDate.'", "%d.%m.%Y")');
        }
        else
        {
            $criteria->addCondition('`dateBack` = "0000-00-00"');
        }
        if ($forceUpdate)
        {
            $result = $fs->sendRequest($flightSearchParams);
        }
        else
        {
            $result = FlightCache::model()->find($criteria);
        }
        if ($result)
        {
            $return = (int)$result->priceBestPriceTime;
            if ($return == 0)
                $return = (int)$result->priceBestPrice;
            return $return;
        }
        else
            throw new CException('Can\'t get best pricetime');
    }
}
