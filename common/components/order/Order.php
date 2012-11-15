<?php

/**
 * This is the model class for table "order".
 *
 * The followings are the available columns in table 'order':
 * @property integer $id
 * @property string $name
 * @property integer $userId
 * @property string $createdAt
 *
 * @property OrderFlightVoyage[] $flightItems
 * @property OrderHotel[] $hotelItems
 */
class Order extends FrontendActiveRecord
{
    public function behaviors(){
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'createdAt',
                'updateAttribute' => null,
            )
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Order the static model class
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
        return 'order';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('userId', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>255),
            array('createdAt', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, userId, createdAt', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'flightItems' => array(self::MANY_MANY, 'OrderFlightVoyage', 'order_has_flight_voyage(orderId,orderFlightVoyage)'),
            'hotelItems' => array(self::MANY_MANY, 'OrderHotel', 'order_has_hotel(orderId,orderHotel)')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'userId' => 'User',
            'createdAt' => 'Created At',
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
        $criteria->compare('name',$this->name,true);
        $criteria->compare('userId',$this->userId);
        $criteria->compare('createdAt',$this->createdAt,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}