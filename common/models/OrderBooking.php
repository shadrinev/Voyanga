<?php

/**
 * This is the model class for table "order_booking".
 *
 * The followings are the available columns in table 'order_booking':
 * @property integer $id
 * @property string $email
 * @property string $phone
 * @property string $userId
 * @property string $timestamp
 * @property string $partnerId
 *
 * The followings are the available model relations:
 * @property FlightBooker[] $flightBookers
 * @property HotelBooker[] $hotelBookers
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
            //array('timestamp', 'required'),
            array('email, phone, userId, partnerId', 'length', 'max'=>45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, email, phone, userId, timestamp, partnerId', 'safe', 'on'=>'search'),
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
            'timestamp' => 'Timestamp',
            'partnerId' => 'Partner',
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
        $criteria->compare('timestamp',$this->timestamp,true);
        $criteria->compare('partnerId',$this->partnerId,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function save($runValidation=true,$attributes=null){
        if($this->isNewRecord){
            $partner = Partner::getCurrentPartner();
            if($partner){
                $this->partnerId = $partner->id;
            }
        }
        parent::save($runValidation=true,$attributes=null);
    }

    public function populate(BookingForm $booking)
    {
        $this->email = $booking->contactEmail;
        $this->phone = $booking->contactPhone;
    }

    public function getUserDescription()
    {
        if($this->userId)
        {
            $user = User::model()->findByPk($this->userId);
            if($user)
            {
                return $user->name;
            }
        }
        return '';
    }

    public function getCountBookings()
    {
        $n = count($this->flightBookers) + count($this->hotelBookers);
        return $n;
    }

    public function getOrderStatus()
    {
        $states = array();
        foreach($this->flightBookers as $flightBooker){
            $states[$this->stateAdapter($flightBooker->status)] = $this->stateAdapter($flightBooker->status);
        }

        foreach($this->hotelBookers as $hotelBooker){
            $states[$this->stateAdapter($hotelBooker->status)] = $this->stateAdapter($hotelBooker->status);
        }
        return join(',',$states);
    }

    public function stateAdapter($state)
    {
        if(strpos($state,'/') !== false)
        {
            $state = substr($state, strpos($state,'/')+1);
        }
        $aStates = array('enterCredentials'=>'Ввод ПД',
            'booking'=>'Бронирование',
            'bookingError'=> 'Ошибка бронирования',
            'waitingForPayment'=>'Ожидание начала оплаты',
            'startPayment'=>'Оплата начата',
            'bookingTimeLimitError'=>'Бронь автоматически снята',
            'ticketing'=>'Выписывание',
            'ticketReady'=>'Выписка готова',
            'ticketingRepeat'=>'Повторное выписывание',
            'manualProcessing'=>'Ручная обработка',
            'manualTicketing'=>'Ручное выписывание',
            'ticketingError'=>'Ошибка выписки',
            'manualError'=>'Ошибка при ручной обработке',
            'moneyReturn'=>'Возврат денег',
            'manualSuccess'=>'Обработано вручную',
            'bspTransfer'=>'Трансфер денег',
            'done'=>'Заказ оплачен',
            'error'=>'Ошибка заказа',

            'analyzing'=>'Анализ штрафов',

            'softWaitingForPayment'=>'Ожидание начала оплаты',
            'softStartPayment'=>'Оплата начата',
            'hardWaitingForPayment'=>'Ожидание начала оплаты',
            'checkingAvailability'=>'Проверка доступности',
            'availabilityError'=>'Недоступен',
            'hardStartPayment'=>'Оплата начата',
        );
        if(isset($aStates[$state])){
            $state = $aStates[$state];
        }
        return $state;
    }

    public function getFullPrice()
    {
        $price = 0;
        foreach($this->flightBookers as $flightBooker){
            if($flightBooker->price){
                $price += $flightBooker->price;
            }
            else
            {
                if($flightBooker->flightVoyage->price){
                    $price += $flightBooker->flightVoyage->price;
                }
            }
        }

        foreach($this->hotelBookers as $hotelBooker){
            if($hotelBooker->price){
                $price += $hotelBooker->price;
            }
            else
            {
                if(isset($hotelBooker->hotel)){
                    if(isset($hotelBooker->hotel->rubPrice)){
                        $price += $hotelBooker->hotel->rubPrice;
                    }
                }
            }
        }
        return $price;
    }
}