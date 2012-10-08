<?php

/**
 * This is the model class for table "event_order".
 *
 * The followings are the available columns in table 'event_order':
 * @property integer $eventId
 * @property integer $startCityId
 * @property integer $orderId
 */
class EventOrder extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return EventOrder the static model class
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
        return 'event_order';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('eventId, startCityId, orderId', 'required'),
            array('eventId, startCityId, orderId', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('eventId, startCityId, orderId', 'safe', 'on'=>'search'),
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
            'order' => array(self::BELONGS_TO, 'Order', 'orderId'),
            'event' => array(self::BELONGS_TO, 'Event', 'eventId'),
            'startCity' => array(self::BELONGS_TO, 'City', 'startCityId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'eventId' => 'Event',
            'startCityId' => 'Start City',
            'orderId' => 'Order',
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

        $criteria->compare('eventId',$this->eventId);
        $criteria->compare('startCityId',$this->startCityId);
        $criteria->compare('orderId',$this->orderId);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function getJsonObject()
    {
        return array(
            'name' => $this->order->name,
        );
    }
}