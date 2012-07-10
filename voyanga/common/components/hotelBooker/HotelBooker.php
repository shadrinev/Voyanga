<?php

/**
 * This is the model class for table "hotel_booking".
 *
 * The followings are the available columns in table 'hotel_booking':
 * @property integer $id
 * @property string $status
 * @property string $expiration
 * @property string $hotelInfo
 * @property string $updated
 * @property integer $orderBookingId
 * @property string $orderId
 * @property string $timestamp
 *
 * The followings are the available model relations:
 * @property Booking $orderBooking
 * @property HotelBookingPassport[] $hotelBookingPassports
 */
class HotelBooker extends SWActiveRecord
{
    private $_hotel;
    private $statusChanged = false;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return FlightBooker the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function beforeTransition($event)
    {
        Yii::app()->observer->notify('onAfter'.ucfirst($event->source->getId()), $this);
        parent::beforeTransition($event);
    }

    public function afterTransition($event)
    {
        $stage = $event->destination->getId();
        Yii::app()->observer->notify('onBefore'.ucfirst($stage), $this);
        $this->statusChanged = true;
        parent::afterTransition($event);
    }

    public function afterSave()
    {
        if (!$this->statusChanged)
            return parent::afterSave();
        $method = 'stage'.$this->swGetStatus()->getId();
        if (method_exists(Yii::app()->hotelBooker, $method))
        {
            Yii::app()->hotelBooker->$method();
        }
        else
            Yii::app()->request->redirect(Yii::app()->getRequest()->getUrl());
    }

    public function behaviors()
    {
        return array(
            'workflow'=>array(
                'class' => 'site.common.extensions.simpleWorkflow.SWActiveRecordBehavior',
                'workflowSourceComponent' => 'workflow',
            ),
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'timestamp',
                'updateAttribute' => 'updated',
            ),
            'CronTask'=>array(
                'class' => 'site.common.components.cron.CronTaskBehavior',
            ),
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'hotel_booking';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id', 'required'),
            array('id, orderBookingId', 'numerical', 'integerOnly'=>true),
            array('orderId', 'length', 'max'=>45),
            array('status', 'SWValidator'),
            array('expiration, hotelInfo, updated, timestamp', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, status, expiration, hotelInfo, updated, orderBookingId, orderId, timestamp', 'safe', 'on'=>'search'),
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
            'orderBooking' => array(self::BELONGS_TO, 'OrderBooking', 'orderBookingId'),
            'hotelBookingPassports' => array(self::HAS_MANY, 'HotelBookingPassport', 'hotelBookingId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'status' => 'Status',
            'expiration' => 'Expiration',
            'hotelInfo' => 'Hotel Info',
            'updated' => 'Updated',
            'orderBookingId' => 'Order Booking',
            'orderId' => 'Order',
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
        $criteria->compare('status',$this->status,true);
        $criteria->compare('expiration',$this->expiration,true);
        $criteria->compare('hotelInfo',$this->hotelInfo,true);
        $criteria->compare('updated',$this->updated,true);
        $criteria->compare('orderBookingId',$this->orderBookingId);
        $criteria->compare('orderId',$this->orderId,true);
        $criteria->compare('timestamp',$this->timestamp,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function getHotel()
    {
        if ($this->_hotel==null)
        {
            if ($this->isNewRecord)
            {
                return null;
            }
            else
            {
                $element = unserialize($this->hotel);
                $this->_hotel = $element;
            }
        }
        return $this->_hotel;
    }

    public function setHotel($value)
    {
        $element = serialize($value);
        $this->_hotel = $value;
        $this->hotelInfo = $element;
    }
}