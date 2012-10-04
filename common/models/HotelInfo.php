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
    public $hotelServices;
    public $hotelGroupServices;

    /** @var HotelImage[] */
    public $images;
    public $gtaHotelCode;
    public $gtaCityCode;
    public $updated;

    private static $facMap = array();
    private static $ameMap = array();
    private static $servListMap = array();
    private static $servGrMap = array();
    private static $fGrMap = array();
    private static $aGrMap = array();
    private static $sGrMap = array();

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
        $this->makeServices();
    }

    private static function addToServiceGroup(&$serviceGroups,$groupName,$servName){
        if(!isset($serviceGroups[$groupName])){
            $serviceGroups[$groupName] = array();
        }
        $serviceGroups[$groupName][$servName] = 0;
    }

    private static function renameService(&$serviceList,$oldName,$newName){
        if(isset($serviceList[$oldName])){
            unset($serviceList[$oldName]);
            $serviceList[$newName] = 0;
        }
    }

    function makeServices()
    {
        if(!self::$servListMap){
            $serviceListMap = array();
            $serviceListMap[0] = array('name'=>'hide','f'=>array(41,50,53,52,48,46,144,82,96,119,106,58,108,134,92,99,117,94,114,115,89,110,38,68,86,85,44,66,100,79,151,75,129,130),'a'=>array());
            $serviceListMap[1] = array('name'=>'Уборка номеров','f'=> array(32),'a'=>array(194,236));
            $serviceListMap[2] = array('name'=>'Интернет в номере','f'=> array(),'a'=>array(6));
            $serviceListMap[3] = array('name'=>'Интернет на территории отеля','f'=> array(87,102),'a'=>array());
            $serviceListMap[4] = array('name'=>'Горячие ванны','f'=> array(126,127),'a'=>array());
            $serviceListMap[5] = array('name'=>'Услуги прачечной','f'=> array(97,29),'a'=>array(315,263));
            $serviceListMap[6] = array('name'=>'Экскурсии','f'=> array(74,42),'a'=>array(254,184));
            $serviceListMap[7] = array('name'=>'Бассейн','f'=> array(25,31),'a'=>array());
            $serviceListMap[8] = array('name'=>'Природные горячие источники','f'=> array(128,124),'a'=>array());
            $serviceListMap[9] = array('name'=>'Спа','f'=> array(84,56),'a'=>array(292,293));
            $serviceListMap[10] = array('name'=>'Аренда автомобиля','f'=> array(19,65),'a'=>array());
            $serviceListMap[11] = array('name'=>'Туристическое агентство','f'=> array(135,36),'a'=>array(309));
            $serviceListMap[12] = array('name'=>'Фитнесс-центр','f'=> array(88,23),'a'=>array());
            $serviceListMap[13] = array('name'=>'Пляж','f'=> array(116,104),'a'=>array(240,324));
            $serviceListMap[14] = array('name'=>'Возможен заказ дополнительной кровати','f'=> array(),'a'=>array(172,189,274));
            $serviceListMap[15] = array('name'=>'Кондиционер','f'=> array(61),'a'=>array(1,332));
            $serviceListMap[16] = array('name'=>'Номера для некурящих','f'=> array(),'a'=>array(229));
            $serviceListMap[17] = array('name'=>'Электричество','f'=> array(141),'a'=>array(13));
            $serviceListMap[18] = array('name'=>'Допускаются домашние животные','f'=> array(123),'a'=>array(267,190));
            $serviceListMap[19] = array('name'=>'Присмотр за детьми','f'=> array(17),'a'=>array(270,269));
            $serviceListMap[20] = array('name'=>'Удобства для инвалидов','f'=> array(63),'a'=>array(191));
            $serviceListMap[21] = array('name'=>'Парковка на территории отеля','f'=> array(18),'a'=>array());

            //$serviceListMap[] = array('name'=>'','f'=> array(),'a'=>array());
            foreach($serviceListMap as $key=>$servInfo)
            {
                if($servInfo['f']){
                    foreach($servInfo['f'] as $id)
                    {
                        self::$facMap[$id] = $key;
                    }
                }
                if($servInfo['a']){
                    foreach($servInfo['a'] as $id)
                    {
                        self::$ameMap[$id] = $key;
                    }
                }
            }
            self::$servListMap = $serviceListMap;

            $serviceGroupsMap = array();
            $serviceGroupsMap[] = array('name'=>'Сервис','s'=>array(1,19,5),'f'=>array(93,64,30,73),'a'=>array(318));
            $serviceGroupsMap[] = array('name'=>'Спорт и отдых','s'=>array(7,4,8,9,12,13),'f'=>array(26,113,91,35,33,15,47,142,139,22,51,107,49,72,98,143,137,109,105,45,39,136,140,118),'a'=>array());
            $serviceGroupsMap[] = array('name'=>'В отеле','s'=>array(17),'f'=>array(27,28,34,16,14),'a'=>array(303,328));
            $serviceGroupsMap[] = array('name'=>'Туристам','s'=>array(10,11,6),'f'=>array(),'a'=>array());
            $serviceGroupsMap[] = array('name'=>'Интернет','s'=>array(2,3),'f'=>array(),'a'=>array());
            $serviceGroupsMap[] = array('name'=>'Развлечения и досуг','s'=>array(),'f'=>array(40,95,69,57,59),'a'=>array());
            $serviceGroupsMap[] = array('name'=>'Парковка','s'=>array(21),'f'=>array(24,67),'a'=>array());
            $serviceGroupsMap[] = array('name'=>'Дополнительно','s'=>array(18,20,16,14),'f'=>array(131,21),'a'=>array());
            $serviceGroupsMap[] = array('name'=>'hide','s'=>array(),'f'=>array(41,50,53,52,48,46,144,82,96,119,106,58,108,134,92,99,117,94,114,115,89,110,38,68,86,85,44,66,100,79,151,75,129,130),'a'=>array());

            foreach($serviceGroupsMap as $key=>$groupInfo)
            {
                if($groupInfo['f']){
                    foreach($groupInfo['f'] as $id)
                    {
                        self::$fGrMap[$id] = $key;
                    }
                }
                if($groupInfo['a']){
                    foreach($groupInfo['a'] as $id)
                    {
                        self::$aGrMap[$id] = $key;
                    }
                }
                if($groupInfo['s']){
                    foreach($groupInfo['s'] as $id)
                    {
                        $name = $serviceListMap[$id]['name'];
                        self::$sGrMap[$name] = $key;
                    }
                }
            }
            self::$servGrMap = $serviceGroupsMap;
        }

        $serviceList = array();
        $serviceGroups = array();
        if($this->facilities)
        {
            foreach($this->facilities as $fId=>$facName){
                if(isset(self::$facMap[$fId])){
                    $name = self::$servListMap[self::$facMap[$fId]]['name'];
                    if($name != 'hide')
                    {
                        $serviceList[$name] = 0;
                        if(isset(self::$sGrMap[$name])){
                            $grName = self::$servGrMap[self::$sGrMap[$name]]['name'];
                            self::addToServiceGroup($serviceGroups,$grName,$name);
                        }
                    }
                }else{
                    $serviceList[$facName] = 0;
                    if(!isset(self::$fGrMap[$fId])){
                        $grName = 'Дополнительно';
                        self::addToServiceGroup($serviceGroups,$grName,$facName);
                    }
                }
                if(isset(self::$fGrMap[$fId])){
                    $grName = self::$servGrMap[self::$fGrMap[$fId]]['name'];
                    self::addToServiceGroup($serviceGroups,$grName,$facName);
                }else{

                }
            }
        }
        if($this->roomAmenities)
        {
            foreach($this->roomAmenities as $aId=>$ameName){
                if(isset(self::$ameMap[$aId])){
                    $name = self::$servListMap[self::$ameMap[$aId]]['name'];
                    if($name != 'hide')
                    {
                        $serviceList[$name] = 0;
                        if(isset(self::$sGrMap[$name])){
                            $grName = self::$servGrMap[self::$sGrMap[$name]]['name'];
                            self::addToServiceGroup($serviceGroups,$grName,$name);
                        }
                    }
                }else{
                    //$serviceList[$ameName] = 0;
                }
                if(isset(self::$aGrMap[$aId])){
                    $grName = self::$servGrMap[self::$aGrMap[$aId]]['name'];
                    self::addToServiceGroup($serviceGroups,$grName,$ameName);
                }
            }
        }
        if($serviceList){
            self::renameService($serviceList,'Интернет в номере','Интернет');
            self::renameService($serviceList,'Интернет на территории отеля','Интернет');
            self::renameService($serviceList,'Парковка на территории отеля','Парковка');
            self::renameService($serviceList,'Фитнесс-центр','Фитнесс');
            foreach($serviceList as $serviceName=>$zero){
                $this->hotelServices[] = $serviceName;
            }
        }
        if($serviceGroups){
            if(isset($serviceGroups['hide'])) unset($serviceGroups['hide']);
            $this->hotelGroupServices = array();
            foreach($serviceGroups as $grName=>$elems){
                $this->hotelGroupServices[$grName] = array();
                foreach($elems as $servName=>$zero){
                    $this->hotelGroupServices[$grName][] = $servName;
                }
            }
        }
    }
}
