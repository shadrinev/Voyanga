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
        /*$criteria = new CDbCriteria();
        $criteria->addSearchCondition('roomNameCanonical', '%suite%', false);
        $criteria->addSearchCondition('roomSizeId',6);
        //$criteria->group = 'sizeName,typeName,roomNameCanonical';
        //$roomSizes = array('DBL'=>2,'SGL'=>1,'TWIN'=>3,'TWIN for Single use'=>4,'TRPL'=>5,'QUAD'=>6,'DBL for Single use'=>7,'DBL OR TWIN'=>8);
        echo 'найдено комбинаций: '.RoomNamesNemo::model()->count($criteria).'<br />';
        $rooms = RoomNamesNemo::model()->findAll($criteria);

        $rusRoomName = RoomNamesRus::model()->findByAttributes(array('roomNameRus'=>'Четырехместный большой номер'));
        VarDumper::dump($rusRoomName);

        /** @var $rooms RoomNamesNemo[] */
        /*foreach($rooms as $room){
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
        Yii::import('site.common.modules.hotel.models.*');
        $hbc = new HotelBookClient();
        $rts = $hbc->getRoomTypes();
        $roomTypes = array();
        foreach($rts as $rt){
            $roomTypes[$rt['id']] = $rt['name'];
        }

        VarDumper::dump($roomTypes);

    }

    /**
     * list all models
     */
    public function actionManage($filterName = '',$rusId = 1){
        //$dataProvider=new EMongoDocumentDataProvider('GeoNames',array('criteria'=>array('conditions'=>array('iataCode'=>array('type'=>2)) )));
        //$dataProvider=new EMongoDocumentDataProvider('GeoNames',array('criteria'=>array('conditions'=>array('iataCode'=>array('type'=>2)) )));
        //echo "fn:{$filterName}  ri: {$rusId} <br />";
        if(isset($_POST['roomNameIds']) and $_POST['roomNameIds']){
        }
        if(isset($_POST['smbset']) and $_POST['smbset']){
            //echo "smbset<br />";
            if(isset($_POST['roomNameIds']) and $_POST['roomNameIds']){
                $updateCriteria = new CDbCriteria();
                $updateCriteria->addCondition('id IN('.join(',',$_POST['roomNameIds']).')');
                if(isset($_POST['rusNameId']) and $_POST['rusNameId']){
                    RoomNamesNemo::model()->updateAll(array('roomNameRusId'=>$_POST['rusNameId']),$updateCriteria);
                }
            }
        }
        if(isset($_POST['smbunset']) and $_POST['smbunset']){
            //echo "smbunset<br />";
            if(isset($_POST['roomNameIds']) and $_POST['roomNameIds']){
                $updateCriteria = new CDbCriteria();
                $updateCriteria->addCondition('id IN('.join(',',$_POST['roomNameIds']).')');
                RoomNamesNemo::model()->updateAll(array('roomNameRusId'=>null),$updateCriteria);
            }
        }
        $selectCriteria = new CDbCriteria();
        if($filterName){
            $selectCriteria->addSearchCondition('roomNameCanonical', $filterName, false);
        }
        if($rusId){
            switch($rusId){
                case 2:
                    //$selectCriteria->addSearchCondition('roomNameCanonical', '%apartment%', false);
                    $selectCriteria->addCondition('roomNameRusId IS NULL');
                    break;
                case 3:
                    $selectCriteria->addCondition('roomNameRusId IS NOT NULL');
                    break;
            }
        }
        if(isset($_POST['roomNameIds']) and $_POST['roomNameIds']){
            //VarDumper::dump($_POST['roomNameIds']);
            //$selectCriteria->addCondition('id IN('.join(',',$_POST['roomNameIds']).')');
        }

        $dataProvider=new CActiveDataProvider(
            'RoomNamesNemo',
            array(
                'criteria'=>$selectCriteria,
                'pagination'=>array(
                    'pageSize'=>40,
                )
            )
        );
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
            'filterName'=>$filterName,
            'rusId'=>$rusId,

        ));
    }

    public function actionRusNamesManage($filterName = '')
    {

        if(isset($_POST['smbset']) and $_POST['smbset']){
            if(isset($_POST['rusNameId']) and $_POST['rusNameId'] and $_POST['roomNameRusField']){
                $roomNameRus = RoomNamesRus::model()->findByPk($_POST['rusNameId']);
                if($roomNameRus){
                    $roomNameRus->roomNameRus = $_POST['roomNameRusField'];
                    $roomNameRus->save();
                }
            }else{
                $roomNameRus = new RoomNamesRus();
                if($_POST['roomNameRusField']){
                    $roomNameRus->roomNameRus = $_POST['roomNameRusField'];
                    $roomNameRus->save();
                }
            }
        }

        $selectCriteria = new CDbCriteria();
        if($filterName){

            $words = explode(' ',$filterName);
            $words = array_map('trim',$words);
            $aWords = array();
            $i = 0;
            foreach($words as $word){
                if($word){
                    $aWords[] = $word;
                    $i++;
                    if($i > 6){
                        $aWords = array($filterName);
                        break;
                    }
                }
            }
            $queries = array();

            $combWords = self::recombineArray($aWords);
            foreach($combWords as $comb){
                $queries[] = '%'.implode('%',$comb).'%';
            }

            foreach($queries as $key=>$query1){
                $selectCriteria->params[':roomNameRus'.$key] = $query1;
                $selectCriteria->addCondition('t.roomNameRus LIKE :roomNameRus'.$key,'OR');
            }
            //$selectCriteria->addSearchCondition('roomNameRus', $filterName, false);
        }
        $dataProvider=new CActiveDataProvider(
            'RoomNamesRus',
            array(
                'criteria'=>$selectCriteria,
                'pagination'=>array(
                    'pageSize'=>40,
                )
            )
        );
        $this->render('manageRusNames',array(
            'dataProvider'=>$dataProvider,
            'filterName'=>$filterName
        ));
    }

    public function actionDelete($id){
        $roomNameRus = RoomNamesRus::model()->findByPk($id);
        if($roomNameRus){
            $criteria = new CDbCriteria();
            $criteria->addCondition('roomNameRusId = :rnri');
            $criteria->params = array(':rnri'=>$id);
            RoomNamesNemo::model()->updateAll(array('roomNameRusId'=>null),$criteria);
            $roomNameRus->delete();
            $this->redirect('/admin/hotels/roomNames/rusNamesManage');
        }else{
            echo "Элемента не найдено!";
        }
    }

    public static function recombineArray($arr1,$firstElem = null) {
        $ret = array();
        if(count($arr1) > 1){
            foreach($arr1 as $key=>$elem){
                $param = $arr1;
                unset($param[$key]);
                $res = self::recombineArray($param,$elem);
                foreach($res as $line){
                    if($firstElem){
                        array_unshift($line,$firstElem);
                    }
                    $ret[] = $line;
                }
            }
        }else{

                if($firstElem){
                    array_unshift($arr1,$firstElem);
                }
                $ret[] = $arr1;
                return $ret;
        }
        return $ret;
    }

    public function actionRusRoomNames($query, $return = false)
    {


        $currentLimit = appParams('autocompleteLimit');
        $currentLimit = 100;
        $cacheKey = md5($query);
        $items = Yii::app()->cache->get('autocompleteRusRoomNames'.$cacheKey);
        //$query = str_replace(' ','%',$query);
        $words = explode(' ',$query);
        $words = array_map('trim',$words);
        $aWords = array();
        $i = 0;
        foreach($words as $word){
            if($word){
                $aWords[] = $word;
                $i++;
                if($i > 6){
                    $aWords = array($query);
                    break;
                }
            }
        }
        $queries = array();

        $combWords = self::recombineArray($aWords);
        foreach($combWords as $comb){
            $queries[] = '%'.implode('%',$comb).'%';
        }

        $items = array();
        if(!$items)
        {
            $items = array();
            $roomNames = array();


            $criteria = new CDbCriteria();
            $criteria->limit = $currentLimit;
            foreach($queries as $key=>$query1){
                $criteria->params[':roomNameRus'.$key] = $query1;
                $criteria->addCondition('t.roomNameRus LIKE :roomNameRus'.$key,'OR');
            }
            /** @var  RusNamesRus[] $roomNamesRus  */
            $roomNamesRus = RoomNamesRus::model()->findAll($criteria);

            if($roomNamesRus)
            {
                foreach($roomNamesRus as $roomNameRus)
                {
                    $items[] = array(
                        'id'=>$roomNameRus->primaryKey,
                        'label'=>$roomNameRus->roomNameRus.', '.$roomNameRus->id,
                        'value'=>$roomNameRus->roomNameRus,
                    );
                    $roomNames[$roomNameRus->id] = $roomNameRus->id;
                }
            }
            $currentLimit -= count($items);


            Yii::app()->cache->set('autocompleteRusRoomNames'.$cacheKey,$items,appParams('autocompleteCacheTime'));
        }
        header('Content-type: application/json');
        echo json_encode($items);
        die();
    }

    public function actionGetCanonicalName($roomName)
    {

        $response = array();
        header('Content-type: application/json');
        $response = HotelRoom::parseRoomNameStatic($roomName);
        echo json_encode($response);
        die();
    }

}
