<?php

/**
 * This is the model class for table "flight_booking_passport".
 *
 * The followings are the available columns in table 'flight_booking_passport':
 * @property integer $id
 * @property string $firstName
 * @property string $lastName
 * @property string $birthday
 * @property string $series
 * @property string $number
 * @property integer $flightBookingId
 * @property integer $documentTypeId
 * @property integer $countryId
 * @property string $expiration
 * @property integer $genderId
 * @property string $timestamp
 *
 * The followings are the available model relations:
 * @property FlightBooker $flightBooking
 * @property Country $country
 */
class FlightBookingPassport extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return FlightBookingPassport the static model class
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
        return 'flight_booking_passport';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('firstName', 'required'),
            array('flightBookingId, documentTypeId, countryId, genderId', 'numerical', 'integerOnly'=>true),
            array('firstName, lastName, birthday, series, number', 'length', 'max'=>45),
            array('expiration, timestamp', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, firstName, lastName, birthday, series, number, flightBookingId, documentTypeId, countryId, expiration, genderId, timestamp', 'safe', 'on'=>'search'),
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
            'flightBooking' => array(self::BELONGS_TO, 'FlightBooking', 'flightBookingId'),
            'country' => array(self::BELONGS_TO, 'Country', 'countryId'),
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
            'series' => 'Series',
            'number' => 'Number',
            'flightBookingId' => 'Flight Booking',
            'documentTypeId' => 'Document Type',
            'countryId' => 'Country',
            'expiration' => 'Expiration',
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
        $criteria->compare('series',$this->series,true);
        $criteria->compare('number',$this->number,true);
        $criteria->compare('flightBookingId',$this->flightBookingId);
        $criteria->compare('documentTypeId',$this->documentTypeId);
        $criteria->compare('countryId',$this->countryId);
        $criteria->compare('expiration',$this->expiration,true);
        $criteria->compare('genderId',$this->genderId);
        $criteria->compare('timestamp',$this->timestamp,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    public function populate($passport, $flightBookerId)
    {
        $this->birthday = $passport->birthday;
        $this->firstName = $passport->firstName;
        $this->lastName = $passport->lastName;
        $this->countryId = $passport->countryId;
        $this->number = $passport->number;
        $this->series = $passport->series;
        $this->genderId = $passport->genderId;
        $this->documentTypeId = $passport->documentTypeId;
        $this->flightBookingId = $flightBookerId;        
    }
}