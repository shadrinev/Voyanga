<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 19.08.12 22:29
 */
class HotelTripElementFrontendProcessor
{
    public static function prepareInfoForTab(HotelTripElement $hotel)
    {
        /** @var $hotel HotelTripElement */
        $from = City::getCityByPk($hotel->city);
        $tab = array();
        $tab['label'] = '<b>Отель в городе '.$from->localRu.'</b><br>'.$hotel->checkIn." &mdash; ".$hotel->checkOut;
        $tab['id'] = $hotel->id.'_tab';
        $tab['info'] = array(
            'type'=>'hotel',
            'cityId'=>$hotel->city,
            'checkIn'=>$hotel->checkIn,
            'checkOut'=>$hotel->checkOut,
            'duration'=>$hotel->getDuration(),
        );
        foreach ($hotel->rooms as $i => $room)
        {
            $tab['info']['room'][$i]['adultCount'] = $room->adultCount;
            $tab['info']['room'][$i]['childCount'] = $room->childCount;
            $tab['info']['room'][$i]['cots'] = $room->cots;
            $tab['info']['room'][$i]['childAge'] = $room->childAge;
        }
        if($hotel->hotel)
        {
            $controller = Yii::app()->getController();
            $tab['content'] = $controller->renderPartial('//tour/constructor/_chosen_hotel_precompiled', array('hotel'=>$hotel->hotel), true);
            $tab['itemOptions']['class'] = 'hotel fill';
            $tab['fill'] = true;
        }
        else
        {
            $tab['content'] = 'loading...';
            $tab['itemOptions']['class'] = 'hotel unfill';
            $tab['fill'] = false;
        }
        return $tab;
    }
}
