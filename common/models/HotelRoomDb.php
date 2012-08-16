<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 14.08.12
 * Time: 16:21
 * To change this template use File | Settings | File Templates.
 */
/**
 * This is the model class for table "hotel_room_db".
 *
 * The followings are the available columns in table 'hotel_room_db':
 * @property integer $id
 * @property integer $hotelId
 * @property integer $hotelName
 * @property string $providerKey
 * @property string $sizeName
 * @property integer $typeId
 * @property string $typeName
 * @property integer $viewId
 * @property string $viewName
 * @property integer $mealId
 * @property string $mealName
 * @property integer $mealBreakfastId
 * @property string $mealBreakfastName
 * @property integer $sharingBedding
 * @property string $roomName
 * @property integer $rubPrice
 * @property string $resultId
 * @property string $requestId
 */
class HotelRoomDb extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HotelRoomDb the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'hotel_room_db';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('hotelId, typeId, viewId, mealId, mealBreakfastId, sharingBedding, rubPrice', 'numerical', 'integerOnly'=>true),
            array('providerKey', 'length', 'max'=>20),
            array('sizeName, hotelName', 'length', 'max'=>30),
            array('typeName, viewName, mealName', 'length', 'max'=>35),
            array('mealBreakfastName', 'length', 'max'=>45),
            array('roomName', 'length', 'max'=>70),
            array('resultId, requestId', 'length', 'max'=>10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, hotelId, hotelName, providerKey, sizeName, typeId, typeName, viewId, viewName, mealId, mealName, mealBreakfastId, mealBreakfastName, sharingBedding, roomName, rubPrice, resultId, requestId', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'hotelId' => 'Hotel',
            'hotelName' => 'Hotel Name',
            'providerKey' => 'Provider Key',
            'sizeName' => 'Size Name',
            'typeId' => 'Type',
            'typeName' => 'Type Name',
            'viewId' => 'View',
            'viewName' => 'View Name',
            'mealId' => 'Meal',
            'mealName' => 'Meal Name',
            'mealBreakfastId' => 'Meal Breakfast',
            'mealBreakfastName' => 'Meal Breakfast Name',
            'sharingBedding' => 'Sharing Bedding',
            'roomName' => 'Room Name',
            'rubPrice' => 'Rub Price',
            'resultId' => 'Result',
            'requestId' => 'Request',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('hotelId',$this->hotelId);
        $criteria->compare('hotelName',$this->hotelName,true);
        $criteria->compare('providerKey',$this->providerKey,true);
        $criteria->compare('sizeName',$this->sizeName,true);
        $criteria->compare('typeId',$this->typeId);
        $criteria->compare('typeName',$this->typeName,true);
        $criteria->compare('viewId',$this->viewId);
        $criteria->compare('viewName',$this->viewName,true);
        $criteria->compare('mealId',$this->mealId);
        $criteria->compare('mealName',$this->mealName,true);
        $criteria->compare('mealBreakfastId',$this->mealBreakfastId);
        $criteria->compare('mealBreakfastName',$this->mealBreakfastName,true);
        $criteria->compare('sharingBedding',$this->sharingBedding);
        $criteria->compare('roomName',$this->roomName,true);
        $criteria->compare('rubPrice',$this->rubPrice);
        $criteria->compare('resultId',$this->resultId,true);
        $criteria->compare('requestId',$this->requestId,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}