<?php

/**
 * This is the model class for table "flight_booker".
 *
 * The followings are the available columns in table 'flight_booker':
 * @property integer $id
 * @property integer $status
 * @property string $pnr
 * @property Booking $booking
 * @property integer $bookingId
 * @property integer $nemoBookId
 * @property string $timeout
 * @property string $flight_voyage
 */
class FlightBooker extends SWActiveRecord
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

    public function behaviors()
    {
        return array(
            'workflow'=>array(
                'class' => 'site.common.extensions.simpleWorkflow.SWActiveRecordBehavior',
                'workflowSourceComponent' => 'workflow',
            ),
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created_at',
                'updateAttribute' => 'updated_at',
            )
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'flight_booker';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pnr', 'length', 'max'=>255),
            array('timeout, flight_voyage, bookingId, nemoBookId', 'safe'),
            array('status', 'SWValidator'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, status, pnr, timeout, flight_voyage', 'safe', 'on'=>'search'),
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
            'booking'=>array(self::BELONGS_TO, 'Booking', 'bookingId')
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
            'flight_voyage' => 'Flight Voyage',
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
        $criteria->compare('status',$this->status);
        $criteria->compare('pnr',$this->pnr,true);
        $criteria->compare('timeout',$this->timeout,true);
        $criteria->compare('flight_voyage',$this->flight_voyage,true);

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
                $element = unserialize($this->flight_voyage);
                $this->_flightVoyage = $element;
            }
        }
        return $this->_flightVoyage;
    }

    public function setFlightVoyage($value)
    {
        $element = serialize($value);
        $this->_flightVoyage = $value;
        $this->flight_voyage = $element;
    }
}