<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 18.06.12
 * Time: 15:40
 * To change this template use File | Settings | File Templates.
 */
class HotelSearchParams
{
    public $checkIn;
    public $duration;
    public $city;
    public $rooms;


    public function addRoom($adultCount, $cots = 0, $childAge = false)
    {
        $sameRoom = false;
        $room = array('adultCount'=>$adultCount,'cots'=>$cots,'childAge'=>$childAge,'childCount'=> $childAge === false ? 0 : 1,'roomCount'=>1);
        /*$compareParams = array('adultCount','coats','childAge','childCount');
        foreach($this->rooms as $key=>$roomInfo)
        {
            $sameRoom = true;
            foreach($compareParams as $param)
            {
                $sameRoom = $sameRoom && ($roomInfo[$param] === $room[$param]);
                if(!$sameRoom) break;
            }
            if($sameRoom)
            {
                $this->rooms[$key]['roomCount']++;
                break;
            }
        }*/
        if(!$sameRoom){
            $this->rooms[] = $room;
        }
    }
}
