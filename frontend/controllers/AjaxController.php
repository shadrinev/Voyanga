<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 12.07.12
 * Time: 10:58
 * To change this template use File | Settings | File Templates.
 */
class AjaxController extends BaseAjaxController
{
    public function actionCityForFlight($query, $return = false)
    {
        $cities = CityManager::getCitiesWithAirports($query);
        if ($return)
            return $cities;
        else
            $this->send($cities);
    }

    public function actionCityForHotel($query, $return = false)
    {
        $cities = CityManager::getCitiesWithHotels($query);
        if ($return)
            return $cities;
        else
            $this->send($cities);
    }

    public function actionCityForHotelOrFlight($query, $return=false)
    {
        $cities = CityManager::getCitiesWithHotelsAndAirports($query);
        if ($return)
            return $cities;
        else
            $this->send($cities);
    }

    public function actionCityForFlightOrHotel($query, $return=false)
    {
        $cities = CityManager::getCitiesWithAirportsAndHotels($query);
        if ($return)
            return $cities;
        else
            $this->send($cities);
    }

    public function actionGetOptimalPrice($from, $to, $dateStart, $dateEnd, $forceUpdate=true)
    {
        try
        {
            $dateStart = Event::getFlightFromDate($dateStart);
            $dateEnd = Event::getFlightToDate($dateEnd);

            $fromTo = FlightSearcher::getOptimalPrice($from, $to, $dateStart, false, $forceUpdate);
            $toFrom = FlightSearcher::getOptimalPrice($to, $from, $dateEnd, false, $forceUpdate);
            $fromBack = FlightSearcher::getOptimalPrice($from, $to, $dateStart, $dateEnd, $forceUpdate);
            $response = array(
                'priceTo' => (int)$fromTo,
                'priceBack' => (int)$toFrom,
                'priceToBack' => (int)$fromBack
            );
            $this->send($response);
        }
        catch (Exception $e)
        {
            $this->sendError(500, $e->getMessage());
        }
    }

    public function actionRusRoomNames($query, $return = false)
    {
        $currentLimit = appParams('autocompleteLimit');
        $items = Yii::app()->cache->get('autocompleteRusRoomNames'.$query);

        $items = array();
        if(!$items)
        {
            $items = array();
            $roomNames = array();


            $criteria = new CDbCriteria();
            $criteria->limit = $currentLimit;
            $criteria->params[':roomNameRus'] = '%'.$query.'%';
            //$criteria->params[':localEn'] = $query.'%';

            $criteria->addCondition('t.roomNameRus LIKE :roomNameRus');
            /** @var  RusNamesRus[] $roomNamesRus  */
            $roomNamesRus = RoomNamesRus::model()->findAll($criteria);

            if($roomNamesRus)
            {
                foreach($roomNamesRus as $roomNameRus)
                {
                    $items[] = array(
                        'id'=>$roomNameRus->primaryKey,
                        'label'=>$this->parseTemplate('{roomNameRus}, {id}',$roomNameRus),
                        'value'=>$this->parseTemplate('{roomNameRus}',$roomNameRus),
                    );
                    $roomNames[$roomNameRus->id] = $roomNameRus->id;
                }
            }
            $currentLimit -= count($items);


            Yii::app()->cache->set('autocompleteRusRoomNames'.$query,$items,appParams('autocompleteCacheTime'));
        }

        if ($return)
            return $items;
        else
            $this->send($items);
    }

    public function actionStartCityForEvent($eventId)
    {
        $event = Event::model()->with('startCity')->findByPk($eventId);
        if (!$event)
            $this->sendError(404);
        $cities = $event->startCity;
        $response = array();
        foreach ($cities as $city)
        {
            $element = array(
                'id' => $city->id,
                'name' => $city->name
            );
            $response[] = $element;
        }
        $this->send($response);
    }

    public function actionGetShortUrl($checkIfUrl=true)
    {
        try
        {
            if (!isset($_POST['long']))
                throw new CHttpException(400);
            $longUrl = $_POST['long'];
            if ($checkIfUrl)
            {
                $validator = new CUrlValidator();
                if (!$validator->validateValue($longUrl))
                    throw CHttpException(500, 'Incorrect url to short');
            }
            $short = new ShortUrl();
            $short = $short->createShortUrl($longUrl);
            $response = array('short'=>Yii::app()->params['baseUrl'].'/'.$short);
            $this->send($response);
        }
        catch (Exception $e)
        {
            $this->sendError(500, $e->getMessage());
        }
    }
}
