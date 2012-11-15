<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:12
 */
class HotelSearchAction extends CAction
{
    public function run()
    {
        Yii::import('site.common.modules.hotel.models.*');

        $hotelForm = new HotelForm;
        if (isset($_REQUEST['HotelForm']))
        {
            $hotelForm->attributes = $_REQUEST['HotelForm'];
            $rooms = array();
            if (isset($_REQUEST['HotelRoomForm']))
            {
                foreach ($_REQUEST['HotelRoomForm'] as $i=>$info)
                {
                    $room = new HotelRoomForm;
                    $room->attributes = $info;
                    if ($room->validate())
                        $rooms[] = $room;
                }
            }
            $hotelForm->rooms = $rooms;
            if ($hotelForm->validate())
            {
                $hotelSearchParams = new HotelSearchParams();
                $hotelSearchParams->checkIn = date('Y-m-d', strtotime($hotelForm->fromDate));
                $hotelSearchParams->city = City::getCityByPk($hotelForm->cityId);
                $hotelSearchParams->duration = $hotelForm->duration;
                foreach ($hotelForm->rooms as $room)
                {
                    if ($room->childCount==1)
                        $hotelSearchParams->addRoom($room->adultCount, $room->cots, $room->childAge);
                    else
                        $hotelSearchParams->addRoom($room->adultCount, $room->cots, false);
                }
                $HotelClient = new HotelBookClient();
                $cacheId = md5(serialize($hotelSearchParams));
                Yii::app()->pCache->set('hotelSearchParams' . $cacheId, $hotelSearchParams, appParams('hotel_search_cache_time'));
                $resultSearch = $HotelClient->fullHotelSearch($hotelSearchParams);
                Yii::app()->hotelsRating->injectRating($resultSearch->hotels, $hotelSearchParams->city);
                $cacheId = substr(md5(uniqid('', true)), 0, 10);
                Yii::app()->cache->set('hotelResult'.$cacheId, $resultSearch,appParams('hotel_search_cache_time'));
                Yii::app()->cache->set('hotelSearchParams'.$cacheId, $hotelSearchParams,appParams('hotel_search_cache_time'));
                Yii::app()->cache->set('hotelForm'.$cacheId, $hotelForm,appParams('hotel_search_cache_time'));
                //Yii::app()->user->setState('hotel.cacheId', $cacheId);
                //$this->redirect('/booking/hotel/result/cacheId/'.$cacheId);
                if($resultSearch['hotels'])
                {
                    $hotelStack = new HotelStack($resultSearch);
                    $results = $hotelStack->groupBy('hotelId')->groupBy('roomSizeId')
                        ->groupBy('rubPrice')->sortBy('rubPrice',2)->getJsonObject();
                    //VarDumper::dump($hotelStack);die();

                    echo json_encode(array('cacheId'=>$cacheId,'hotels'=>$results));
                }
                else
                {
                    echo json_encode(array('cacheId'=>$cacheId,'hotels'=>array()));
                    //throw new CHttpException(500, CHtml::errorSummary($hotelForm));
                }

            }
            else
            {
                //invalid form
                throw new CHttpException(500, CHtml::errorSummary($hotelForm));
            }

            Yii::app()->end();
        }
        else
            throw new CHttpException(404);
    }

    function compareByTime($a, $b)
    {
        if ($a[3] == $b[3]) {
            return 0;
        }
        return ($a[3] > $b[3]) ? -1 : 1;
    }

    private function storeSearches($from, $to, $date, $adultCount, $childCount, $infantCount)
    {
        $hash = $from.$to.$date;
        $element = array($from, $to, $date, time(), $adultCount, $childCount, $infantCount);
        $elements = Yii::app()->user->getState('lastSearches');
        $elements[$hash] = $element;
        uasort($elements, array($this, 'compareByTime'));
        $last = array_splice($elements, 0, 10);
        Yii::app()->user->setState('lastSearches', $last);
    }
}
