<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 29.06.12
 * Time: 12:01
 * To change this template use File | Settings | File Templates.
 */
class HotelInfo extends CApplicationComponent
{

    /*
     * <City id="...">название города</City>
        <Cat id="...">категория</Cat>
        <Locations>
            <Location id="...">локация</Location>
        </Locations>
        <Address>адрес отеля</Address>
        <Phone>телефон отеля</Phone>
        <Fax>факс отеля</Fax>
        <Email>email отеля</Email>
        <WWW>адрес веб-сайта отеля</WWW>
        <Latitude>широта</Latitude>
        <Longitude>долгота</Longitude>
        <BuiltIn>год постройки</BuiltIn>
        <BuildingType>тип здания</BuildingType>
        <NumberLifts>число лифтов</NumberLifts>
        <NumberFloors>число этажей</NumberFloors>
        <Conference>число конференц-залов</Conference>
        <Voltage>напряжение</Voltage>
        <ChildAgeFrom>минимальный возраст ребенка</ChildAgeFrom>
        <ChildAgeTo>максимальный возраст ребенка</ChildAgeTo>
        <Classification>классификация</Classification>
        <EarlestCheckInTime>время заезда</EarlestCheckInTime>
        <RoomServiceFrom>начала обслуживания</RoomServiceFrom>
        <RoomServiceTo>окончание обслуживания</RoomServiceTo>
        <RoomService24h>круглосуточное обслуживание</RoomService24h>
        <PorterageFrom>начало работы носильщиков</PorterageFrom>
        <PorterageTo>окончание работы носильщиков</PorterageTo>
        <Porterage24h>круглосуточная работа носильщиков</Porterage24h>
        <IndoorPool>закрытые бассейны</IndoorPool>
        <OutdoorPool>открытые бассейны</OutdoorPool>
        <ChildrensPool>детские бассейны</ChildrensPool>
        <Description>описание отеля</Description>
        <Distances>расстояния</Distances>
        <HotelFacility>
            <Facility id="...">услуга</Facility> - список услуг отеля
        </HotelFacility>
        <RoomAmenity>
            <Amenity id="...">удобство</Amenity> - список удобств номера
        </RoomAmenity>
        <HotelType>
            <Type id="..">тип отеля</Type>
        </HotelType>
        <Images> - список фотографий отеля и его внутренних помещений
            <Image>
                <Info>описание фотографии</Info>
                <Small width="..." height="...">url фотографии маленького размера</Small>
                <Large width="..." height="...">url фотографии большого размера</Large>
            </Image>
        </Images>
        <GTAHotelCode>код отеля</GTAHotelCode>
        <GTACityCode>код города</GTACityCode>
        <Updated>дата обновления</Updated>
     */
    /** @var City */
    public $city;

    public $categoryId;
    public $address;
    public $phone;
    public $fax;
    public $email;
    public $site;
    public $latitude;
    public $longitude;
    public $builtIn;
    public $buildingType;
    public $numberLifts;
    public $numberFloors;
    public $conference;
    public $voltage;
    public $childAgeFrom;
    public $childAgeTo;
    public $classification;
    public $earliestCheckInTime;
    public $roomServiceFrom;
    public $roomServiceTo;
    public $roomService24h;
    public $indoorPool;
    public $outdoorPool;
    public $childrenPool;
    public $description;
    public $distances;
    public $facilities;
    public $roomAmenities;
    public $hotelType;

    /** @var HotelImage[] */
    public $images;
    public $gtaHotelCode;
    public $gtaCityCode;
    public $updated;

    function __construct($params = null)
    {
        $attrs = get_object_vars($this);
        $exclude = array('images');
        foreach($attrs as $attrName=>$attrVal)
        {
            if(!in_array($attrName, $exclude))
            {
                if(isset($params[$attrName])){
                    $this->{$attrName} = $params[$attrName];
                }
            }
        }
        if(isset($params['images']))
        {
            foreach($params['images'] as $imageParams)
            {
                $this->images[] = new HotelImage($imageParams);
            }
        }
    }
}
