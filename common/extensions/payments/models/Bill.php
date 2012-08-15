<?php
/**
 * Stores information about bill sent to payonline.
 * Provides some utility functions for easier payonline interactions.
 *
 * The followings are the available columns in table 'bill':
 * @property integer $id
 * @property string $status
 * @property string $transactionId
 * @property string $providerdata
 * @property double $amount
 *
 * The followings are the available model relations:
 * @property HotelBooking[] $hotelBookings
 *
 * @package payments
 * @author  Anatoly Kudinov <kudinov@voyanga.com>
 * @copyright Copyright (c) 2012 EasyTrip LLC
 *
 */
class Bill extends CActiveRecord
{
    const STATUS_NEW = 'NEW';
    const STATUS_PREAUTH = 'PRE';
    const STATUS_PAID = 'PAI';
    const STATUS_FAILED = 'FAI';

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Bill the static model class
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
        return 'bill';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('status, amount', 'required'),
            array('id', 'numerical', 'integerOnly'=>true),
            array('amount', 'numerical'),
            array('status', 'length', 'max'=>3),
            array('transactionId', 'length', 'max'=>20),
            array('providerdata', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, status, transactionId, providerdata, amount', 'safe', 'on'=>'search'),
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
            'hotelBookings' => array(self::HAS_MANY, 'HotelBooking', 'billId'),
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
            'transactionId' => 'Transaction',
            'providerdata' => 'Providerdata',
            'amount' => 'Amount',
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
        $criteria->compare('transactionId',$this->transactionId,true);
        $criteria->compare('providerdata',$this->providerdata,true);
        $criteria->compare('amount',$this->amount);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }


    //! FIXME STORE IN DATABASE?
    private $_channel = 'ecommerce';

    //! FIXME MOVE TO COMPONENT
    public function getPaymentUrl()
    {
        return "https://secure.payonlinesystem.com/ru/payment/";
    }

    public function getParams()
    {
        $amount = $this->amount;
        $params = Yii::app()->payments->getParamsFor($this->channel);
        $params['Amount'] = sprintf("%.2f" ,$amount);
        $params['Currency'] = 'RUB';
        $params['OrderId'] = 'adev-' . $this->id;
        $params['SecurityKey'] = Yii::app()->payments->getSignatureFor($this->channel, $params);
        return $params;
    }

    public function getChannel()
    {
        return $this->_channel;
    }
}
