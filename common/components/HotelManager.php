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
        $variants = $hotelClient->fullHotelSearch($hotelSearchParams);
        self::storeToCache($hotelSearchParams, $variants);
        Yii::app()->hotelsRating->injectRating($variants->hotels, $hotelSearchParams->city);
        $results = array();
        if ($variants->responseStatus == ResponseStatus::ERROR_CODE_NO_ERRORS)
        {
            $stack = new HotelStack($variants);
            $results = $stack->groupBy('hotelId')->mergeSame()->sortBy('rubPrice', 5)->getJsonObject(4);
            $query = array();
            foreach ($results['hotels'] as $i => $info)
            {
                $r = rand(1, 100);
                $query[$info['hotelId']] = $hotelClient->hotelDetail($info['hotelId'], true, $r==1);
            }
            $hotelClient->processAsyncRequests();
            foreach ($query as $hotelId => $responseId)
            {
                if (isset($hotelClient->requests[$responseId]['result']))
                    self::inject($results, $hotelId, $hotelClient->requests[$responseId]['result']);
            }
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
        if (is_object($additional))
        {
            $objectVars = get_object_vars($additional);
            foreach ($objectVars as $objVar => $objProperties)
            {
                if (is_object($additional->$objVar))
                    $additional->$objVar = self::prepare($additional->$objVar);
                elseif (is_array($additional->$objVar))
                {
                    $additional->$objVar = self::prepare($additional->$objVar);
                }
                elseif (is_string($additional->$objVar))
                    $additional->$objVar = strip_tags($additional->$objVar);
                if ($objVar == 'description')
                {
                    $pattern = '/\s?[^.]*?:\s?/';
                    $replace = "<br>\n";
                    $additional->$objVar = preg_replace($pattern, $replace, $additional->$objVar);
                    if (strpos($additional->$objVar, '<br>') === 0)
                    {
                        $additional->$objVar = substr($additional->$objVar, 4);
                    }
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
