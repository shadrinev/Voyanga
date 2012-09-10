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
    public function actionDefault($city, $checkIn, $duration, array $rooms, $format='json')
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
            if ($room['chd']==1)
                $hotelSearchParams->addRoom($room['adt'], $room['cots'], $room['chd']);
            else
                $hotelSearchParams->addRoom($room['adt'],  $room['cots'], false);
        }
        Yii::import('site.frontend.models.*');
        Yii::import('site.frontend.components.*');
        $hotelClient = new HotelBookClient();
        $variants = $hotelClient->fullHotelSearch($hotelSearchParams);
        $results = array();
        if ($variants['errorStatus']==1)
        {
            $stack = new HotelStack($variants);
            $results = $stack->sortBy('rubPrice',5)->getJsonObject();
            $query = array();
            foreach ($results['hotels'] as $i=>$info)
            {
                $query[$info['hotelId']] = $hotelClient->hotelDetail($info['hotelId'], true);
            }
            $hotelClient->processAsyncRequests();
            foreach ($query as $hotelId => $responseId)
            {
                if (isset($hotelClient->requests[$responseId]['result']))
                    $this->inject($results, $hotelId, $hotelClient->requests[$responseId]['result']);
            }
        }
        else
        {
            $this->sendError(500, $variants['errorsDescriptions']);
        }
        if ($format=='json')
            $this->sendJson($results);
        elseif ($format=='xml')
            $this->sendXml($results, 'hotelSearchResults');
        else
            $this->sendError(400, 'Incorrect response format');
    }

    private function inject(&$results, $hotelId, $additional)
    {
        $newResults = array();
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
}
