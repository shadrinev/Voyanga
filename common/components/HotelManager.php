<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 23.10.12
 * Time: 12:04
 */
class HotelManager
{
    static function sendRequestToHotelProvider($hotelSearchParams)
    {
        Yii::import('site.frontend.models.*');
        Yii::import('site.frontend.components.*');
        $hotelClient = new HotelBookClient();
        $startTime = microtime(true);
        $variants = $hotelClient->fullHotelSearch($hotelSearchParams);
        $endTime = microtime(true);

        $startTime = microtime(true);
        self::storeToCache($hotelSearchParams, $variants);
        $endTime = microtime(true);

        $startTime = microtime(true);
        Yii::app()->hotelsRating->injectRating($variants->hotels, $hotelSearchParams->city);
        $endTime = microtime(true);

        //}
        $results = array();
        if ($variants->responseStatus == ResponseStatus::ERROR_CODE_NO_ERRORS)
        {
            $startTime = microtime(true);
            $stack = new HotelStack($variants);
            $results = $stack->groupBy('hotelId', 117501)->mergeStepV2()->groupBy('rubPrice')->getJsonObject(1);
            $nStack = new HotelStack();
            $nStack->_hotels = $stack->getHotels(1);

            $resultsHotels = $stack->getJsonObject();
            $query = array();
            foreach ($resultsHotels['hotels'] as $i => $info)
            {
                $query[$info['hotelId']] = $hotelClient->hotelDetail($info['hotelId'], true);
            }

            $hotelClient->processAsyncRequests();
            $endTime = microtime(true);

            Header('ExecutionTimeSortAndDetails:' . ($endTime - $startTime));

            $hotelsDetails = array();
            foreach ($query as $hotelId => $responseId)
            {
                if (isset($hotelClient->requests[$responseId]['result']))
                {
                    $hotelsDetails[$hotelId . 'd'] = self::prepare($hotelClient->requests[$responseId]['result']);
                    Yii::app()->cache->set('HotelDetails-' . $hotelId, $hotelsDetails[$hotelId . 'd']);
                }
            }

            $results['hotelsDetails'] = $hotelsDetails;

            return $results;
        }
        elseif ($variants->responseStatus == ResponseStatus::ERROR_CODE_EMPTY)
        {
            $results['hotels'] = array();
            return $results;
        }
        else
        {
            return false;
        }
    }

    static function inject(&$results, $hotelId, $additional)
    {
        $newResults = array();
        $additional = self::prepare($additional);
        foreach ($results['hotels'] as $result)
        {
            if ($result['hotelId'] == $hotelId)
            {
                $element = CMap::mergeArray($result, $additional);
            }
            else
            {
                $element = $result;
            }
            $newResults[] = $element;
        }
        $results['hotels'] = $newResults;
    }

    static function prepare($additional)
    {
        if (is_object($additional) && $additional instanceof HotelInfo)
        {
            //$objectVars = get_object_vars($additional);
            $objectVars = array('address', 'description', 'distances', 'earliestCheckInTime', 'email', 'facilities', 'fax', 'hotelGroupServices', 'hotelServices', 'images', 'latitude', 'longitude', 'phone', 'roomAmenities', 'site', 'locations', 'builtIn', 'numberFloors', 'metroList');
            $return = new stdClass();
            foreach ($objectVars as $objVar)
            {
                if (is_object($additional->$objVar))
                {
                    $return->$objVar = self::prepare($additional->$objVar);
                }
                elseif (is_array($additional->$objVar))
                {
                    $return->$objVar = self::prepare($additional->$objVar);
                }
                elseif (is_string($additional->$objVar))
                {
                    $return->$objVar = strip_tags($additional->$objVar);
                }
                if ($objVar == 'description')
                {
                    $pattern = '/\s?[^.]*?:\s?/';
                    $replace = "<br>\n";
                    $return->$objVar = preg_replace($pattern, $replace, $return->$objVar);
                    if (strpos($return->$objVar, '<br>') === 0)
                    {
                        $return->$objVar = substr($return->$objVar, 4);
                    }
                }
            }
            return $return;
        }
        elseif (is_object($additional))
        {
            $objectVars = get_object_vars($additional);

            foreach ($objectVars as $objVar => $val)
            {
                if (is_object($additional->$objVar))
                {
                    $additional->$objVar = self::prepare($additional->$objVar);
                }
                elseif (is_array($additional->$objVar))
                {
                    $additional->$objVar = self::prepare($additional->$objVar);
                }
                elseif (is_string($additional->$objVar))
                {
                    $additional->$objVar = strip_tags($additional->$objVar);
                }
            }
        }
        return $additional;
    }

    static private function storeToCache($hotelSearchParams, $variants)
    {
        $cacheId = md5(serialize($hotelSearchParams));

        Yii::app()->pCache->set('hotelSearchResult' . $cacheId, $variants, appParams('hotel_search_cache_time'));
        Yii::app()->pCache->set('hotelSearchParams' . $cacheId, $hotelSearchParams, appParams('hotel_search_cache_time'));

        return $cacheId;
    }
}
