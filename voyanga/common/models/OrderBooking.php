<?php

/**
 * This is the model class for table "order_booking".
 *
 * The followings are the available columns in table 'order_booking':
 * @property integer $id
 * @property string $email
 * @property string $phone
 * @property string $userId
 * @property string $flightId
 * @property string $hotelId
 * @property string $timestamp
 *
 * The followings are the available model relations:
 * @property FlightBooker[] $flightBookings
 * @property HotelBooker[] $hotelBookings
 */
class OrderBooking extends CActiveRecord
{
    /**
     * The behaviors associated with the user model.
     * @see CActiveRecord::behaviors()
     */
    public function behaviors()
    {
        $behaviors['EAdvancedArBehavior'] = array(
            'class' => 'common.components.EAdvancedArBehavior'
        );
        return $behaviors;
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return OrderBooking the static model class
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
        return 'order_booking';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('timestamp', 'required'),
            array('email, phone, userId, flightId, hotelId', 'length', 'max'=>45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, email, phone, userId, flightId, hotelId, timestamp', 'safe', 'on'=>'search'),
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
            'flightBookers' => array(self::HAS_MANY, 'FlightBooker', 'orderBookingId'),
            'hotelBookers' => array(self::HAS_MANY, 'HotelBooker', 'orderBookingId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'email' => 'Email',
            'phone' => 'Phone',
            'userId' => 'User',
            'flightId' => 'Flight',
            'hotelId' => 'Hotel',
            'timestamp' => 'Timestamp',
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
        $criteria->compare('email',$this->email,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('userId',$this->userId,true);
        $criteria->compare('flightId',$this->flightId,true);
        $criteria->compare('hotelId',$this->hotelId,true);
        $criteria->compare('timestamp',$this->timestamp,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}