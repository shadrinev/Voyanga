<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 20.06.12
 * Time: 13:00
 */
class HotelController extends Controller
{
    public function actionIndex()
    {
        Yii::import('site.common.modules.hotel.models.*');
        $hotelForm = new HotelForm;
        if(isset($_POST['ajax']) && $_POST['ajax']==='hotel-form')
        {
            echo CActiveForm::validate($hotelForm);
            Yii::app()->end();
        }
        if (isset($_POST['HotelForm']))
        {
            $hotelForm->attributes = $_POST['HotelForm'];
            $rooms = array();
            if (isset($_POST['HotelRoomForm']))
            {
                foreach ($_POST['HotelRoomForm'] as $i=>$info)
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
                $hotelSearchParams->city = City::model()->findByPk($hotelForm->cityId);
                $hotelSearchParams->duration = $hotelForm->duration;
                foreach ($hotelForm->rooms as $room)
                {
                    if ($room->childCount==1)
                        $hotelSearchParams->addRoom($room->adultCount, $room->cots, $room->childAge);
                    else
                        $hotelSearchParams->addRoom($room->adultCount, $room->cots, false);
                }
                $HotelClient = new HotelBookClient();
                $resultSearch = $HotelClient->fullHotelSearch($hotelSearchParams);
                $cacheId = substr(md5(uniqid('', true)), 0, 10);
                //
                Yii::app()->cache->set('hotelResult'.$cacheId, $resultSearch,appParams('hotel_search_cache_time'));
                Yii::app()->cache->set('hotelSearchParams'.$cacheId, $hotelSearchParams,appParams('hotel_search_cache_time'));
                Yii::app()->cache->set('hotelForm'.$cacheId, $hotelForm,appParams('hotel_search_cache_time'));
                //echo $cacheId.'||||'.appParams('hotel_search_cache_time');
                //VarDumper::dump(Yii::app()->cache);
                //VarDumper::dump(Yii::app()->cache->set('testCache',123, 1800));
                //VarDumper::dump(Yii::app()->cache->get('testCache'));
                //VarDumper::dump(Yii::app()->cache->get('hotelResult'.$cacheId));
                //VarDumper::dump(Yii::app()->cache->get('hotelSearchParams'.$cacheId));
                //
                //VarDumper::dump(Yii::app()->cache->get('hotelForm'.$cacheId));
                //die();
                $this->redirect('/booking/hotel/result/cacheId/'.$cacheId);
                $hotelStack = new HotelStack($resultSearch);
                $results = $hotelStack->groupBy('hotelId')->groupBy('roomSizeId')->groupBy('rubPrice')->sortBy('rubPrice',2)->getAsJson();
                $this->render('result', array('items'=>$this->generateItems(), 'autosearch'=>false, 'cityName'=>$hotelSearchParams->city->localRu, 'results'=>$results, 'hotelForm'=>$hotelForm));
            }
        }
        else
        {
            $this->render('index', array(
                'items'=>$this->generateItems(),
                'hotelForm'=>$hotelForm,
                'autosearch'=>false,
                'cityName'=>'',
                'duration'=>1
            ));
        }
    }

    public function actionResult($cacheId)
    {
        Yii::import('site.common.modules.hotel.models.*');
        $resultSearch = Yii::app()->cache->get('hotelResult'.$cacheId);
        $hotelSearchParams = Yii::app()->cache->get('hotelSearchParams'.$cacheId);
        $hotelForm = Yii::app()->cache->get('hotelForm'.$cacheId);
        //VarDumper::dump($hotelForm);die();

        $hotelStack = new HotelStack($resultSearch);
        $results = $hotelStack->groupBy('hotelId')->groupBy('roomSizeId')->groupBy('rubPrice')->sortBy('rubPrice',2)->getAsJson();
        $this->render('result', array('items'=>$this->generateItems(), 'autosearch'=>false, 'cityName'=>$hotelSearchParams->city->localRu, 'results'=>$results, 'hotelForm'=>$hotelForm,'cacheId'=>$cacheId));
    }

    public function actionInfo($cacheId,$hotelId)
    {
        Yii::import('site.common.modules.hotel.models.*');
        $hotelSearchParams = Yii::app()->cache->get('hotelSearchParams'.$cacheId);
        $resultSearch = Yii::app()->cache->get('hotelResult'.$cacheId);
        $hotelStack = new HotelStack($resultSearch);
        $hotelStack->groupBy('hotelId')->groupBy('roomSizeId')->groupBy('rubPrice')->sortBy('rubPrice',2)->getAsJson();
        $resultsRecommended = $hotelStack->hotelStacks[$hotelId]->getAsJson();
        $HotelClient = new HotelBookClient();
        $hotels = $HotelClient->hotelSearchFullDetails($hotelSearchParams,$hotelId);
        $hotelStackFull = new HotelStack(array('hotels'=>$hotels));
        $resultsAll = $hotelStackFull->getAsJson();
        $this->render('resultInfo', array('items'=>$this->generateItems(), 'autosearch'=>false, 'cityName'=>$hotelSearchParams->city->localRu, 'resultsRecommended'=>$resultsRecommended, 'resultsAll'=>$resultsAll,'cacheId'=>$cacheId));
    }

    public function actionSearch()
    {

    }

    public function generateItems()
    {
        $elements = Yii::app()->user->getState('lastHotelSearches');
        if (!is_array($elements))
            return false;
        $items = array();
        foreach ($elements as $element)
        {
            $item = array(
                'label' => City::model()->getCityByPk($element[0])->localRu . ', ' . $element[1] . ' - ' . $element[2],
                'url' => '/admin/booking/hotel/search/city/'.$element[0].'/from/'.$element[1].'/duration/'.$element[2],
                'encodeLabel' => false
            );
            $items[] = $item;
        }
        return $items;
    }

    private function storeSearches($city, $date, $duration)
    {
        $hash = $city.$city.$duration;
        $element = array($city, $city, $duration, time());
        $elements = Yii::app()->user->getState('lastSearches');
        $elements[$hash] = $element;
        uasort($elements, array($this, 'compareByTime'));
        $last = array_splice($elements, 0, 10);
        Yii::app()->user->setState('lastSearches', $last);
    }
}
