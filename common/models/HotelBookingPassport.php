<?php

/**
 * This is the model class for table "hotel_booking_passport".
 *
 * The followings are the available columns in table 'hotel_booking_passport':
 * @property integer $id
 * @property string $firstName
 * @property string $lastName
 * @property string $birthday
 * @property integer $hotelBookingId
 * @property integer $countryId
 * @property integer $genderId
 * @property integer $roomKey
 * @property string $timestamp
 *
 * The followings are the available model relations:
 * @property Country $country
 * @property HotelBooking $hotelBooking
 */
class HotelBookingPassport extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HotelBookingPassport the static model class
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
        return 'hotel_booking_passport';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('hotelBookingId, countryId, roomKey', 'numerical', 'integerOnly'=>true),
            array('firstName, lastName', 'length', 'max'=>45),
            array('birthday', 'required', 'on'=>'child'),
            array('genderId', 'required', 'on'=>'adult'),
            array('genderId', 'numerical', 'integerOnly'=>true, 'on'=>'adult'),
            array('timestamp', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, firstName, lastName, birthday, hotelBookingId, countryId, genderId, timestamp', 'safe', 'on'=>'search'),
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
            'country' => array(self::BELONGS_TO, 'Country', 'countryId'),
            'hotelBooking' => array(self::BELONGS_TO, 'HotelBooking', 'hotelBookingId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'birthday' => 'Birthday',
            'hotelBookingId' => 'Hotel Booking',
            'countryId' => 'Country',
            'genderId' => 'Gender',
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
        $criteria->compare('firstName',$this->firstName,true);
        $criteria->compare('lastName',$this->lastName,true);
        $criteria->compare('birthday',$this->birthday,true);
        $criteria->compare('hotelBookingId',$this->hotelBookingId);
        $criteria->compare('countryId',$this->countryId);
        $criteria->compare('genderId',$this->genderId);
        $criteria->compare('roomKey',$this->genderId);
        $criteria->compare('timestamp',$this->timestamp,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}