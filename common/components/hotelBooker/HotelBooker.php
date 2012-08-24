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
 * @property string $hotelResultKey
 * @property string $timestamp
 * @property float $price
 * @property float $originalPrice
 * @property integer $tryCount
 *
 * The followings are the available model relations:
 * @property OrderBooking $orderBooking
 * @property HotelBookingPassport[] $hotelBookingPassports
 */
class HotelBooker extends SWLogActiveRecord
{
    private $_hotel;
    private $statusChanged = false;
    private $hotelBookerComponent;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return FlightBooker the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function beforeTransition($event)
    {
        Yii::app()->observer->notify('onAfter' . ucfirst($event->source->getId()), $this);
        parent::beforeTransition($event);
    }

    public function afterTransition($event)
    {
        $stage = $event->destination->getId();
        Yii::app()->observer->notify('onBefore' . ucfirst($stage), $this);
        parent::afterTransition($event);
    }

    public function statusChanged()
    {
        $this->statusChanged = true;
    }

    public function afterSave()
    {
        if (!$this->statusChanged)
            return parent::afterSave();
        $method = 'stage' . ucfirst($this->swGetStatus()->getId());
        if ($action = Yii::app()->getController()->createAction($method))
        {
           $action->execute();
        }
        elseif (method_exists(Yii::app()->hotelBooker, $method) or method_exists($this->hotelBookerComponent, $method))
        {
            if ($this->hotelBookerComponent)
            {
                $this->hotelBookerComponent->$method();
                return parent::afterSave();
            }
            elseif (Yii::app()->hotelBooker)
            {
                Yii::app()->hotelBooker->$method();
                return parent::afterSave();
            }
            throw new CException('Unknown '.$method.' of FlightBooker');
        }
    }

    public function onlySave()
    {
        $this->statusChanged = false;
        $this->save();
    }

    public function behaviors()
    {
        return array(
            'workflow' => array(
                'class' => 'site.common.extensions.simpleWorkflow.SWActiveRecordBehavior',
                'workflowSourceComponent' => 'workflow',
            ),
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'timestamp',
                'updateAttribute' => 'updated',
            ),
            'CronTask' => array(
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
            //array('id', 'required'),
            array('id, orderBookingId', 'numerical', 'integerOnly' => true),
            array('orderId', 'length', 'max' => 45),
            array('hotelResultKey', 'length', 'max' => 255),
            array('status', 'SWValidator'),
            array('expiration, hotelInfo, updated, timestamp, hotelResultKey, tryCount', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, status, expiration, hotelInfo, updated, orderBookingId, orderId, timestamp, price, originalPrice, tryCount, hotelResultKey', 'safe', 'on' => 'search'),
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
            'bill' => array(self::BELONGS_TO, 'Bill', 'billId'),
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
            'hotelResultKey' => 'Hotel Result Key',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('expiration', $this->expiration, true);
        $criteria->compare('hotelInfo', $this->hotelInfo, true);
        $criteria->compare('updated', $this->updated, true);
        $criteria->compare('orderBookingId', $this->orderBookingId);
        $criteria->compare('orderId', $this->orderId, true);
        $criteria->compare('hotelResultKey', $this->orderId, true);
        $criteria->compare('timestamp', $this->timestamp, true);
        $criteria->compare('tryCount', $this->tryCount, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getHotel()
    {
        if ($this->_hotel == null)
        {
            if ($this->isNewRecord)
            {
                return null;
            }
            else
            {
                $element = unserialize($this->hotelInfo);
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
        $this->hotelResultKey = $value->getId();
        $this->price = $value->getPrice();
        $this->originalPrice = $value->getOriginalPrice();
        if ($value->cancelExpiration)
        {
            $this->expiration = date('Y-m-d H:i:s', $value->cancelExpiration);
        }
    }

    public function getFullDescription()
    {
        $description = array();
        if ($this->_hotel == null)
        {
            if ($this->isNewRecord)
            {
                return null;
            }
            else
            {
                $element = unserialize($this->hotelInfo);
                $this->_hotel = $element;
            }
        }

        if ($this->_hotel)
        {
            $city = City::getCityByHotelbookId($this->_hotel->cityId);
            $description[] = $city->localRu . ': ' . $this->_hotel->hotelName;
            if ($this->hotelBookingPassports)
            {
                foreach ($this->hotelBookingPassports as $passport)
                {
                    $description[] = $passport->firstName . ' ' . $passport->lastName;
                }
            }
        }
        return $description;
    }

    public function setHotelBookerComponent(HotelBookerComponent &$hotelBookerComponent)
    {
        $this->hotelBookerComponent = &$hotelBookerComponent;
    }
}