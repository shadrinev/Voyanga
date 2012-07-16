<?php

/**
 * This is the model class for table "flight_booking".
 *
 * The followings are the available columns in table 'flight_booking':
 * @property integer $id
 * @property string $status
 * @property string $pnr
 * @property string $timeout
 * @property string $flightVoyageInfo
 * @property string $updated
 * @property string $flightVoyageId
 * @property integer $orderBookingId
 * @property integer $nemoBookId
 * @property string $timestamp
 *
 * The followings are the available model relations:
 * @property OrderBooking $orderBooking
 * @property FlightBookingPassport[] $flightBookingPassports
 */
class FlightBooker extends SWLogActiveRecord
{
    private $_flightVoyage;
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
        if (method_exists(Yii::app()->flightBooker, $method))
        {
            Yii::app()->flightBooker->$method();
        }
        else
            Yii::app()->request->redirect(Yii::app()->getRequest()->getUrl());
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'flight_booking';
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
            array('id, orderBookingId, nemoBookId', 'numerical', 'integerOnly'=>true),
            array('status, flightVoyageId', 'length', 'max'=>60),
            array('pnr', 'length', 'max'=>10),
            array('timeout, flightVoyageInfo, updated, timestamp', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, status, pnr, timeout, flightVoyageInfo, updated, flightVoyageId, orderBookingId, nemoBookId, timestamp', 'safe', 'on'=>'search'),
        );
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
                'updateAttribute' => null,
            ),
            'EAdvancedArBehavior' => array(
                'class' => 'common.components.EAdvancedArBehavior'
            ),
            'CronTask'=>array(
                'class' => 'site.common.components.cron.CronTaskBehavior',
            ),
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
            'flightBookingPassports' => array(self::HAS_MANY, 'FlightBookingPassport', 'flightBookingId'),
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
            'pnr' => 'Pnr',
            'timeout' => 'Timeout',
            'flightVoyage' => 'Flight Voyage',
            'updated' => 'Updated',
            'flightVoyageId' => 'Flight Voyage',
            'orderBookingId' => 'Order Booking',
            'nemoBookId' => 'Nemo Book',
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
        $criteria->compare('pnr',$this->pnr,true);
        $criteria->compare('timeout',$this->timeout,true);
        $criteria->compare('flightVoyage',$this->flightVoyage,true);
        $criteria->compare('updated',$this->updated,true);
        $criteria->compare('flightVoyageId',$this->flightVoyageId,true);
        $criteria->compare('orderBookingId',$this->orderBookingId);
        $criteria->compare('nemoBookId',$this->nemoBookId);
        $criteria->compare('timestamp',$this->timestamp,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function getFlightVoyage()
    {
        if ($this->_flightVoyage==null)
        {
            if ($this->isNewRecord)
            {
                return null;
            }
            else
            {
                $element = unserialize($this->flightVoyageInfo);
                $this->_flightVoyage = $element;
            }
        }
        return $this->_flightVoyage;
    }

    public function setFlightVoyage($value)
    {
        $element = serialize($value);
        $this->_flightVoyage = $value;
        $this->flightVoyageInfo = $element;
    }
}