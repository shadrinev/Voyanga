<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 07.08.12
 * Time: 12:17
 */
class SearchController extends ApiController
{
    public $defaultAction = 'default';
    private $results;

    /**
     * @param string city
     * @param string checkIn d.m.Y date
     * @param int duration # of nights inside hotel
     * @param array $rooms
     *  [Х][adt] - amount of adults inside room,
     *  [Х][chd] - amount of childs inside room,
     *  [Х][chdAge] - age of child inside room,
     *  [Х][cots] - cots inside room (0 or 1),
     */
    public function actionDefault($city, $checkIn, $duration, array $rooms, $format = 'json')
    {
        $hotelSearchParams = new HotelSearchParams();
        $hotelSearchParams->checkIn = date('Y-m-d', strtotime($checkIn));
        $possibleCities = City::model()->guess($city);
        if (empty($possibleCities))
            $this->sendError(404, 'Given city not found');
        $hotelSearchParams->city = $possibleCities[0];
        $hotelSearchParams->duration = $duration;
        foreach ($rooms as $i => $room)
        {
            if ($room['chd'] == 1)
                $hotelSearchParams->addRoom($room['adt'], $room['cots'], $room['chdAge']);
            elseif ($room['chd'] == 0)
                $hotelSearchParams->addRoom($room['adt'], $room['cots'], false);
            else
            {
                $this->sendError(200, 'Only 0 or 1 child at one hotel room accepted');
                Yii::app()->end();
            }
        }

        $this->results = HotelManager::sendRequestToHotelProvider($hotelSearchParams);
        if (!$this->results)
        {
            $this->sendError(500, 'Error while send Request To Hotel Provider');
            Yii::app()->end();
        }
        $cacheId = $this->storeToCache($hotelSearchParams);

        $this->results['cacheId'] = $cacheId;
        $this->results['searchParams'] = $hotelSearchParams->getJsonObject();

        if ($format == 'json')
            $this->sendJson($this->results);
        elseif ($format == 'xml')
            $this->sendXml($this->results, 'hotelSearchResults');
        else
        {
            $this->sendError(400, 'Incorrect response format');
            Yii::app()->end();
        }
    }

    public function actionInfo($hotelId, $cacheId, $format = 'json')
    {
        $hotelSearchResult = Yii::app()->pCache->get('hotelSearchResult' . $cacheId);
        $hotelSearchParams = Yii::app()->pCache->get('hotelSearchParams' . $cacheId);
        if ((!$hotelSearchResult) || (!$hotelSearchParams))
        {
            $this->sendError(200, 'Cache invalidated already.');
            Yii::app()->end();
        }
        $hotelClient = new HotelBookClient();

        foreach ($hotelSearchResult['hotels'] as $hotel)
        {
            if ($hotel['hotelId'] == $hotelId)
            {
                if (!isset($response['hotel']))
                {
                    $response['hotel'] = $hotel;
                    $response['hotel']['details'] = $hotelClient->hotelSearchFullDetails($hotelSearchParams, $hotelId);
                    $response['searchParams'] = $hotelSearchParams->getJsonObject();
                    $response['hotel']['oldHotels'] = array();
                    $response['hotel']['oldHotels'][] = new Hotel($hotel);
                }
                else
                {
                    $response['hotel']['oldHotels'][] = new Hotel($hotel);
                }
            }
        }
        if (isset($response))
        {
            $null = null;
            $hotelClient->hotelSearchDetails($null, $response['hotel']['oldHotels']);
            if ($format == 'json')
                $this->sendJson($response);
            elseif ($format == 'xml')
                $this->sendXml($response, 'hotelSearchResults');
            Yii::app()->end();
        }
        $this->sendError(200, 'No hotel with given hotelId found');
    }

    private function storeToCache($hotelSearchParams)
    {
        $cacheId = md5(serialize($hotelSearchParams));

        Yii::app()->pCache->set('hotelSearchResult' . $cacheId, $this->results, appParams('hotel_search_cache_time'));
        Yii::app()->pCache->set('hotelSearchParams' . $cacheId, $hotelSearchParams, appParams('hotel_search_cache_time'));

        return $cacheId;
    }
}
