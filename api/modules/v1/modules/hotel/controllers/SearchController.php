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
     * @param string checkIn Y-m-d date
     * @param int duration #of nights inside hotel
     * @param array $rooms
     *  [Х][adt] - amount of adults inside room,
     *  [Х][chd] - amount of childs inside room,
     *  [Х][chdAge] - age of child inside room,
     *  [Х][cots] - cots inside room (0 or 1),
     */
    public function actionDefault($city, $checkIn, $duration, array $rooms, $format='json')
    {
        $hotelSearchParams = new HotelSearchParams();
        $hotelSearchParams->checkIn = $checkIn;
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
        $HotelClient = new HotelBookClient();
        $variants = $HotelClient->fullHotelSearch($hotelSearchParams);
        if ($variants['errorStatus']==1)
        {
            $stack = new HotelStack($variants);
            $results = $stack->sortBy('rubPrice',5)->getJsonObject();
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
}
