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
                $hotelSearchParams->addRoom($room['adt'], $room['cots'], $room['chdAge']);
            elseif ($room['chd']==0)
                $hotelSearchParams->addRoom($room['adt'],  $room['cots'], false);
            else
                $this->sendError(500, 'Only 0 or 1 child at one hotel room accepted');
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
        $additional = $this->prepare($additional);
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

    private function prepare($additional)
    {
        if (is_object($additional))
        {
            $objectVars = get_object_vars($additional);
            foreach ($objectVars as $objVar=>$objProperties)
            {
                if (is_object($additional->$objVar))
                    $additional->$objVar = $this->prepare($additional->$objVar);
                elseif (is_array($additional->$objVar))
                {
                    $additional->$objVar = $this->prepare($additional->$objVar);
                }
                elseif (is_string($additional->$objVar))
                    $additional->$objVar = strip_tags($additional->$objVar);
            }
        }
        return $additional;
    }

    public function actionInfo($cacheId, $hotelId, $format='json')
    {
        $hotelSearchParams = Yii::app()->cache->get('hotelSearchParams'.$cacheId);
        $resultSearch = Yii::app()->cache->get('hotelResult'.$cacheId);
        if($resultSearch)
        {
            $hotelStack = new HotelStack($resultSearch);
            $hotelStack->groupBy('hotelId')->groupBy('roomShowName')->groupBy('rubPrice')->sortBy('rubPrice',2);
            $resultsRecommended = $hotelStack->hotelStacks[$hotelId]->getAsJson();
            $HotelClient = new HotelBookClient();
            $hotels = $HotelClient->hotelSearchFullDetails($hotelSearchParams,$hotelId);
            $hotelStackFull = new HotelStack(array('hotels'=>$hotels));
            $resultsAll = $hotelStackFull->getAsJson();
            $hotelInfo = $HotelClient->hotelDetail($hotelId);
            //$this->render('resultInfo', array('items'=>$this->generateItems(), 'autosearch'=>false, 'cityName'=>$hotelSearchParams->city->localRu,'hotelInfo'=>$hotelInfo,'resultsRecommended'=>$resultsRecommended, 'resultsAll'=>$resultsAll,'cacheId'=>$cacheId));
        }else{
            $this->sendError(400, 'Incorrect response format');
        }
        $response = array('cityName'=>$hotelSearchParams->city->localRu,'hotelInfo'=>$hotelInfo,'resultsRecommended'=>$resultsRecommended, 'resultsAll'=>$resultsAll,'cacheId'=>$cacheId);
        if ($format=='json')
            $this->sendJson($response);
        elseif ($format=='xml')
            $this->sendXml($response, 'hotelSearchResults');
        else
            $this->sendError(400, 'Incorrect response format');

    }
}
