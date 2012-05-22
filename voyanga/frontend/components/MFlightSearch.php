<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 18.05.12
 * Time: 17:02
 */
class MFlightSearch extends CComponent
{
    public static function getOptimalPrice($fromCityId, $toCityId, $date, $returnDate=false, $forceUpdate = false)
    {
        $flightSearchParams = new FlightSearchParams();
        $departureDate = date('d.m.Y', strtotime($date));
        $flightSearchParams->addRoute(array(
            'adult_count' => 1,
            'child_count' => 0,
            'infant_count' => 0,
            'departure_city_id' => $fromCityId,
            'arrival_city_id' => $toCityId,
            'departure_date' => $departureDate,
        ));
        if ($returnDate)
        {
            $returnDate = date('d.m.Y', strtotime($returnDate));
            $flightSearchParams->addRoute(array(
                'adult_count' => 1,
                'child_count' => 0,
                'infant_count' => 0,
                'departure_city_id' => $toCityId,
                'arrival_city_id' => $fromCityId,
                'departure_date' => $returnDate
            ));
        }
        $flightSearchParams->flight_class = 'E';
        $fs = new FlightSearch();
        $fs->status = 1;
        $fs->requestId = '1';
        $fs->data = '{}';
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array('departureCityId'=>$fromCityId, 'arrivalCityId'=>$toCityId, 'isOptimal'=>1));
        $criteria->addCondition('departureDate BETWEEN STR_TO_DATE("'.$date.' 00:00:00", "%d.%m.%Y %H:%i:%s") and STR_TO_DATE("'.$date.' 23:59:59", "%d.%m.%Y %H:%i:%s")');
        if ($returnDate)
        {
            $criteria->addCondition('returnDate BETWEEN STR_TO_DATE("'.$returnDate.' 00:00:00", "%d.%m.%Y %H:%i:%s") and STR_TO_DATE("'.$returnDate.' 23:59:59", "%d.%m.%Y %H:%i:%s")');
            $criteria->addSearchCondition('withReturn', 1);
        }
        if ($forceUpdate)
        {
            //$result = FlightCache::model()->deleteAll($criteria);
            $fs->sendRequest($flightSearchParams);
        }
        $result = FlightCache::model()->find($criteria);
        if ($result)
        {
            return (int)$result->price;
        }
        else
            throw new CException('Can\'t get best pricetime');
    }
}
