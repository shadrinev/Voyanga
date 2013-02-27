<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 12.11.12
 * Time: 16:14
 * To change this template use File | Settings | File Templates.
 */
class EventInfoController extends FrontendController
{
    public function actionIndex()
    {
        $this->layout = 'static';
        $this->render('info');
    }

    public function actionInfo($eventId)
    {
        $event = Event::model()->findByPk($eventId);
        if (!$event)
            throw new CHttpException(404, 'Событие не найдено');

        $defaultCityId = 4466;
        $pricesData = array();
        $this->assignTitle('event', array('##eventTitle##' => $event->title));
        $this->layout = 'static';
        foreach ($event->prices as $price) {
            $pricesData[$price->city->id] = array('price' => floor($price->bestPrice), 'cityName' => $price->city->localRu, 'cityId' => $price->city->id, 'updateTime' => str_replace(' ', 'T', $price->updated));
        }

        $tours = array();
        $dataProvider = new TripDataProvider();
        $cities = array();
        $minPrice = 2e6; // ohuliard
        foreach ($event->tours as $tour) {

            $tours[$tour->startCityId] = array();
            $dataProvider->restoreFromDb($tour->orderId);
            $items = $dataProvider->getWithAdditionalInfo($dataProvider->getSortedCartItemsOnePerGroup(false));
            $tours[$tour->startCityId] = $items;
            $tours[$tour->startCityId]['city'] = City::getCityByPk($tour->startCityId)->getAttributes();
            $eventPrice = EventPrice::model()->findByAttributes(array('eventId' => $eventId, 'cityId' => $tour->startCityId));
            $first = reset($items);
            $first = $first[0];
            $peopleAmount = isset($first['searchParams']['adt']) ? $first['searchParams']['adt'] + $first['searchParams']['chd'] + $first['searchParams']['inf'] : false;
            $ttl = 0;
            if ($peopleAmount === false)
            {
                //first element is hotel
                foreach ($first['searchParams']['rooms'] as $room)
                {
                    $ttl += $room['adultCount'] + $room['childCount'] + $room['cots'];
                }
                $peopleAmount = $ttl;
            }
            if ($eventPrice) {
                $tours[$tour->startCityId]['price'] = ceil($eventPrice->bestPrice);
                if ($tours[$tour->startCityId]['price'] < $minPrice)
                    $minPrice = $tours[$tour->startCityId]['price'];
            }
            $cities[$tour->startCityId] = City::getCityByPk($tour->startCityId)->getAttributes();
        }

        if (!isset($cities[$defaultCityId])) {
            foreach ($cities as $defaultCityId => $city)
                break;
        }
        //need search params
        $twoCities = array();
        $twoCities[$defaultCityId] = $cities[$defaultCityId];
        foreach ($cities as $cityId => $city)
            if (!isset($twoCities[$cityId])) {
                $twoCities[$cityId] = $city;
                break;
            }

        $pictures = array();
        foreach ($event->pictures as $picture) {
            $pictures[] = array('url' => $picture->getUrl());
        }

        $this->render('info', array(
            'peopleAmountReadable'=>UtilsHelper::peopleReadable($peopleAmount),
            'minPrice' => $minPrice,
            'event' => $event,
            'priceData' => $pricesData,
            'defaultCity' => $defaultCityId,
            'tours' => $tours,
            'cities' => $cities,
            'twoCities' => $twoCities,
            'pictures' => $pictures
        ));
    }
}
