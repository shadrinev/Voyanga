<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 20.06.12
 * Time: 13:00
 */
class HotelController extends ABaseAdminController
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
                $resultSearch = $HotelClient->fullHotelSearch($hotelSearchParams);
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
