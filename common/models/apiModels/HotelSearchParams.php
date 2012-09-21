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
    /**
     * @var string Date in format Y-m-d
     */
    public $checkIn;
    /**
     * @var string Duration in days
     */
    public $duration;
    /**
     * @var City City object for search
     */
    public $city;
    /**
     * @var Array Array of rooms in format array('adultCount'=>$adultCount,'cots'=>$cots,'childAge'=>$childAge,'childCount'=> $childAge === false ? 0 : 1,'roomCount'=>1);
     * integer adultCount Amount of adults in one room
     * integer cots Amount cots for infants in one room (need to validate : 0 .. 2)
     * integer childAge Age of a child in room (need to validate : 0 .. 21)
     * integer childCount  Amount of children in one room. possible values 0 or 1
     */
    public $rooms;

    public function addRoom($adultCount, $cots = 0, $childAge = false)
    {
        $sameRoom = false;
        $room = array('adultCount' => $adultCount, 'cots' => $cots, 'childAge' => $childAge, 'childCount' => $childAge === false ? 0 : 1, 'roomCount' => 1);
        if (!$sameRoom)
        {
            $this->rooms[] = $room;
        }
    }

    public function getJsonObject()
    {
        $jsonObject = array(
            'checkIn' => $this->checkIn,
            'duration' => $this->duration,
            'city' => $this->city->code,
            'rooms' => array()
        );
        foreach ($this->rooms as $room)
        {
            $newElement = array(
                'adultCount' => $room['adultCount'],
                'cots' => $room['cots'],
                'childAge' => $room['childAge'],
                'childCount' => $room['childCount'],
            );
            $jsonObject['rooms'][] = $newElement;
        }
        return $jsonObject;
    }
}
