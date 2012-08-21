<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 17.08.12
 * Time: 12:16
 * To change this template use File | Settings | File Templates.
 */
class RoomNamesController extends ABaseAdminController
{
    public $defaultAction = 'admin';

    /**
     * Lists all models.
     */
    public function actionIndex()
    {

    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        /*$roomSizes = array(
            'Одноместный %s',
            'Двухместный %s',
            'Двухместный Твин %s',
            'Двухместный %s с одноместным размещением',
            'Трехместный %s',
            'Четырехместный %s',
        );
        $roomTypes = array(
            'большой номер',
            'номер студия',
            'семейный номер',
            'семейный номер студия',
            'номер Сьюит',
            'улучшеный номер',
            'Эконом',
            'Бизнес',
            'номер De luxe',
            'номер для молодожёнов',
            'номер с балконом',
        );
        foreach($roomTypes as $roomType){
            foreach($roomSizes as $roomSize){
                $name = sprintf($roomSize,$roomType);
                $roomName = new RoomNamesRus();
                $roomName->roomNameRus = $name;
                $roomName->save();
            }
        }*/
        /*$criteria = new CDbCriteria();
        $criteria->group = 'sizeName,typeName,roomNameCanonical';
        $roomSizes = array('DBL'=>2,'SGL'=>1,'TWIN'=>3,'TWIN for Single use'=>4,'TRPL'=>5,'QUAD'=>6,'DBL for Single use'=>7,'DBL OR TWIN'=>8);
        echo 'найдено комбинаций: '.HotelRoomDb::model()->count($criteria);
        $rooms = HotelRoomDb::model()->findAll($criteria);

        /** @var $rooms HotelRoomDb[] */
        /*foreach($rooms as $room){
            if($room->roomNameCanonical){
                $nemoRoom = new RoomNamesNemo();
                $nemoRoom->roomTypeId = $room->typeId;
                $nemoRoom->roomSizeId = $roomSizes[$room->sizeName];
                $nemoRoom->roomNameCanonical = $room->roomNameCanonical;
                $nemoRoom->save();
            }
        }*/

        /*
         * связи для таблиц
         */

        //двухместные апартаменты
        /*$criteria = new CDbCriteria();
        $criteria->addSearchCondition('roomNameCanonical', '%apartment%', false);
        $criteria->addSearchCondition('roomSizeId',6);
        //$criteria->group = 'sizeName,typeName,roomNameCanonical';
        //$roomSizes = array('DBL'=>2,'SGL'=>1,'TWIN'=>3,'TWIN for Single use'=>4,'TRPL'=>5,'QUAD'=>6,'DBL for Single use'=>7,'DBL OR TWIN'=>8);
        echo 'найдено комбинаций: '.RoomNamesNemo::model()->count($criteria).'<br />';
        $rooms = RoomNamesNemo::model()->findAll($criteria);

        $rusRoomName = RoomNamesRus::model()->findByAttributes(array('roomNameRus'=>'Четырехместные апартаменты'));
        VarDumper::dump($rusRoomName);*/

        /** @var $rooms RoomNamesNemo[] */
        /*foreach($rooms as $room){
            echo "{$rusRoomName->roomNameRus} {$rusRoomName->id}<br />";
            if($room->roomNameCanonical){
                echo $room->roomSizeId.' '.$room->roomNameCanonical.' <br />';
                $room->roomNameRusId = $rusRoomName->id;
                $room->save();
                //$nemoRoom = new RoomNamesNemo();
                //$nemoRoom->roomTypeId = $room->typeId;
                //$nemoRoom->roomSizeId = $roomSizes[$room->sizeName];
                //$nemoRoom->roomNameCanonical = $room->roomNameCanonical;
                //$nemoRoom->save();
            }
        }*/

        //семейные
        /*$criteria = new CDbCriteria();
        $criteria->addSearchCondition('roomNameCanonical', '%family%', false);
        $criteria->addSearchCondition('roomSizeId',3);
        //$criteria->group = 'sizeName,typeName,roomNameCanonical';
        //$roomSizes = array('DBL'=>2,'SGL'=>1,'TWIN'=>3,'TWIN for Single use'=>4,'TRPL'=>5,'QUAD'=>6,'DBL for Single use'=>7,'DBL OR TWIN'=>8);
        echo 'найдено комбинаций: '.RoomNamesNemo::model()->count($criteria).'<br />';
        $rooms = RoomNamesNemo::model()->findAll($criteria);

        $rusRoomName = RoomNamesRus::model()->findByAttributes(array('roomNameRus'=>'Двухместный Твин семейный номер'));
        VarDumper::dump($rusRoomName);*/

        /** @var $rooms RoomNamesNemo[] */
        /*foreach($rooms as $room){
            echo "{$rusRoomName->roomNameRus} {$rusRoomName->id}<br />";
            if($room->roomNameCanonical){
                echo $room->roomSizeId.' '.$room->roomNameCanonical.' <br />';
                $room->roomNameRusId = $rusRoomName->id;
                $room->save();
            }
        }*/

        //studio
        $criteria = new CDbCriteria();
        $criteria->addSearchCondition('roomNameCanonical', '%suite%', false);
        $criteria->addSearchCondition('roomSizeId',6);
        //$criteria->group = 'sizeName,typeName,roomNameCanonical';
        //$roomSizes = array('DBL'=>2,'SGL'=>1,'TWIN'=>3,'TWIN for Single use'=>4,'TRPL'=>5,'QUAD'=>6,'DBL for Single use'=>7,'DBL OR TWIN'=>8);
        echo 'найдено комбинаций: '.RoomNamesNemo::model()->count($criteria).'<br />';
        $rooms = RoomNamesNemo::model()->findAll($criteria);

        $rusRoomName = RoomNamesRus::model()->findByAttributes(array('roomNameRus'=>'Четырехместный большой номер'));
        VarDumper::dump($rusRoomName);

        /** @var $rooms RoomNamesNemo[] */
        foreach($rooms as $room){
            echo "{$rusRoomName->roomNameRus} {$rusRoomName->id}<br />";
            if($room->roomNameCanonical){
                echo $room->roomSizeId.' '.$room->roomNameCanonical.' <br />';
                //$room->roomNameRusId = $rusRoomName->id;
                //$room->save();
            }
        }

        //studio
        /*$criteria = new CDbCriteria();
        $criteria->addSearchCondition('roomNameCanonical', '%tive suite%', false);
        $criteria->addSearchCondition('roomSizeId',3);
        //$criteria->group = 'sizeName,typeName,roomNameCanonical';
        //$roomSizes = array('DBL'=>2,'SGL'=>1,'TWIN'=>3,'TWIN for Single use'=>4,'TRPL'=>5,'QUAD'=>6,'DBL for Single use'=>7,'DBL OR TWIN'=>8);
        echo 'найдено комбинаций: '.RoomNamesNemo::model()->count($criteria).'<br />';
        $rooms = RoomNamesNemo::model()->findAll($criteria);

        $rusRoomName = RoomNamesRus::model()->findByAttributes(array('roomNameRus'=>'Двухместный Твин улучшеный номер'));
        VarDumper::dump($rusRoomName);*/

        /** @var $rooms RoomNamesNemo[] */
        /*foreach($rooms as $room){
            echo "{$rusRoomName->roomNameRus} {$rusRoomName->id}<br />";
            if($room->roomNameCanonical){
                echo $room->roomSizeId.' '.$room->roomNameCanonical.' <br />';
                //$room->roomNameRusId = $rusRoomName->id;
                //$room->save();
            }
        }*/

        //распечатать все
        /*$rusRoomNames = RoomNamesRus::model()->findAll();
        foreach($rusRoomNames as $room){
            echo "{$room->id}&nbsp;&nbsp;{$room->roomNameRus} <br />";

        }*/

    }

}
