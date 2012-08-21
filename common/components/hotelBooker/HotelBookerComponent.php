<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 19.06.12
 * Time: 10:42
 */
class HotelBookerComponent extends CApplicationComponent
{
    /** @var HotelBooker */
    private $hotelBooker;

    /** @var Hotel */
    public $hotel;

    public function init()
    {
        Yii::setPathOfAlias('hotelBooker', realpath(dirname(__FILE__)));
        Yii::import('common.extensions.payments.models.Bill');
        Yii::import('hotelBooker.actions.*');
        Yii::import('hotelBooker.models.*');
        Yii::import('hotelBooker.*');
        Yii::import('site.common.modules.hotel.models.*');

    }

    public function setHotel($value)
    {
        $this->hotel = $value;
    }

    private function loadModel()
    {
        if ($this->hotelBooker == null)
        {
            $id = Yii::app()->user->getState('hotelResultKey');
            $this->hotelBooker = HotelBooker::model()->findByAttributes(array('hotelResultKey' => $id));
        }
        return $this->hotelBooker;
    }

    public function getCurrent()
    {
        return $this->loadModel();
    }

    public function getStatus()
    {
        if ($this->hotelBooker != null)
            return $this->hotelBooker->swGetStatus();
        return 'enterCredentials';
    }

    public function book()
    {
        //if we don't have a hotel OR we moved to another hotel
        if ($this->getCurrent() == null || ($this->getCurrent()->hotel->id != $this->hotel->getId()))
        {
            //if we don't have a hotel AND we moved to another hotel
            if (($this->getCurrent() != null) and $this->getCurrent()->hotel->id != $this->hotel->getId())
            {
                Yii::trace('Trying to restore hotelBooker from db', 'HotelBookerComponent.book');

                $this->hotelBooker = HotelBooker::model()->findByAttributes(array('hotelResultKey' => $this->hotel->getId()));
                if ($this->hotelBooker){
                    $this->hotelBooker->setHotelBookerComponent($this);
                    Yii::trace('Done', 'HotelBookerComponent.book');
                }
                else{
                    Yii::trace('No such record', 'HotelBookerComponent.book');

                }
            }
            if ($this->hotelBooker == null)
            {
                Yii::trace('New hotelBooker to db', 'HotelBookerComponent.book');
                $this->hotelBooker = new HotelBooker();
                $this->hotelBooker->hotelResultKey = $this->hotel->getId();
                $this->hotelBooker->hotel = $this->hotel;
                $this->hotelBooker->status = 'enterCredentials';
                $this->hotelBooker->setHotelBookerComponent($this);
                $this->hotelBooker->save();
            }
        }

        //        Yii::trace(CVarDumper::dumpAsString($this->hotelBooker->getErrors()), 'HotelBookerComponent.book');
        if (!$this->hotelBooker->id)
        {
            $this->hotelBooker->id = $this->hotelBooker->primaryKey;
        }
        Yii::app()->user->setState('hotelResultKey', $this->hotelBooker->hotel->id);
    }

    public function status($newStatus)
    {
        $this->hotelBooker->status = $newStatus;
        $this->hotelBooker->statusChanged();
        return $this->hotelBooker->save();
    }

    public function stageAnalyzing()
    {
        $hotelBookClient = new HotelBookClient();
        $hotelBookClient->hotelSearchDetails($this->hotel);
        SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds, HotelBookClient::$requestIds);
        HotelBookClient::$requestIds = array();
        $this->hotelBooker->hotel = $this->hotel;
        if (($this->hotel->cancelExpiration - time()) > (appParams('hotel_payment_time') * 2))
        {
            $this->status('booking');
        }
        else
        {
            $this->status('hardWaitingForPayment');
        }
    }

    public function stageBooking()
    {
        $hotelOrderParams = new HotelOrderParams();
        $hotelOrderParams->hotel = $this->hotel;
        $contactName = '';
        foreach ($this->hotelBooker->hotelBookingPassports as $passport)
        {
            $roomer = new Roomer();
            $roomer->setFromHotelBookingPassport($passport);
            $roomer->roomId = $passport->roomKey;
            if (!$contactName)
                $contactName = $roomer->fullName;
            $hotelOrderParams->roomers[] = $roomer;
        }
        $hotelOrderParams->contactPhone = $this->hotelBooker->orderBooking->phone;
        $hotelOrderParams->contactEmail = $this->hotelBooker->orderBooking->email;
        $hotelOrderParams->contactName = $contactName;
        $hotelBookClient = new HotelBookClient();
        if ($this->hotelBooker->orderId)
        {
            $orderInfo = new HotelOrderResponse();
            $orderInfo->orderId = $this->hotelBooker->orderId;
        }
        else
        {
            $orderInfo = $hotelBookClient->addOrder($hotelOrderParams);
            SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,HotelBookClient::$requestIds);
            HotelBookClient::$requestIds = array();
        }

        if ($orderInfo->orderId)
        {
            $this->hotelBooker->orderId = $orderInfo->orderId;
            $confirmInfo = $hotelBookClient->confirmOrder($orderInfo->orderId);
            SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,HotelBookClient::$requestIds);
            HotelBookClient::$requestIds = array();
            Yii::trace(VarDumper::dumpAsString($confirmInfo), 'HotelBookerComponent.stageBooking');
            if (!$orderInfo->error)
            {

                $res = Yii::app()->cron->add(strtotime($this->hotelBooker->expiration), 'hotelbooking', 'ChangeState', array('hotelBookerId' => $this->hotelBooker->id, 'newState' => 'bookingTimeLimitError'));
                $this->hotelBooker->saveTaskInfo('timeLimitError', $res);

                $waitStatus = $this->status('softWaitingForPayment');
                Yii::trace('wait: ' . CVarDumper::dumpAsString($waitStatus), 'HotelBookerComponent.stageBooking');

                if (!$waitStatus)
                {
                    Yii::trace(VarDumper::dumpAsString($this->hotelBooker->getErrors(), 'HotelBookerComponent.stageBooking'));
                }
            }
            else
            {
                $this->status('bookingError');
            }
        }
        else
        {
            $this->status('bookingError');
        }
    }

    public function stageBookingError()
    {
        $this->status('error');
    }

    public function stageSoftWaitingForPayment()
    {
        //waiting for payment
    }

    public function stageSoftStartPayment()
    {
        //        return;
        if (($this->hotel->cancelExpiration - time()) > appParams('hotel_payment_time'))
        {
            $res = Yii::app()->cron->add(time() + appParams('hotel_payment_time'), 'hotelbooking', 'ChangeState', array('hotelBookerId' => $this->hotelBooker->id, 'newState' => 'softWaitingForPayment'));
            if ($res)
            {
                $this->hotelBooker->saveTaskInfo('paymentTimeLimit', $res);
                return true;
            }
        }

        return false;
    }

    public function stageBookingTimeLimitError()
    {

    }

    public function stageMoneyTransfer()
    {
        //! FIXME WTF IS ВАУЧЕР
        //TODO: получение информации о ваучере и отправка денег
        //$vaucher =
        Yii::app()->payments->confirm($this->hotelBooker->bill);
        if($this->hotelBooker->bill->status == Bill::STATUS_PAID)
        {
            $this->status('done');
        }
    }

    public function stageCheckingAvailability()
    {
        $hotelBookClient = new HotelBookClient();
        $find = $hotelBookClient->checkHotel($this->hotel);

        if ($find)
        {
            $this->status('hardStartPayment');
        }
        else
        {
            $this->status('availabilityError');
        }

    }

    public function stageAvailabilityError()
    {
        $this->status('error');
    }

    public function stageHardStartPayment()
    {
        $res = Yii::app()->cron->add(time() + appParams('hotel_payment_time'), 'hotelbooking', 'ChangeState', array('hotelBookerId' => $this->hotelBooker->id, 'newState' => 'hardWaitingForPayment'));
        if ($res)
        {
            $this->hotelBooker->saveTaskInfo('hardPaymentTimeLimit', $res);
        }
        else
        {
            $this->status('error');
        }
    }

    public function stageTicketing()
    {
        $hotelOrderParams = new HotelOrderParams();

        $hotelOrderParams->hotel = $this->hotel;
        $contactName = '';
        foreach ($this->hotelBooker->hotelBookingPassports as $passport)
        {
            $roomer = new Roomer();
            $roomer->setFromHotelBookingPassport($passport);
            $roomer->roomId = $passport->roomKey;
            if (!$contactName)
                $contactName = $roomer->fullName;
            $hotelOrderParams->roomers[] = $roomer;
        }
        $hotelOrderParams->contactPhone = $this->hotelBooker->orderBooking->phone;
        $hotelOrderParams->contactEmail = $this->hotelBooker->orderBooking->email;
        $hotelOrderParams->contactName = $contactName;
        $hotelBookClient = new HotelBookClient();
        if ($this->hotelBooker->orderId)
        {
            $orderInfo = new HotelOrderResponse();
            $orderInfo->orderId = $this->hotelBooker->orderId;
        }
        else
        {
            $orderInfo = $hotelBookClient->addOrder($hotelOrderParams);
            SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,HotelBookClient::$requestIds);
            HotelBookClient::$requestIds = array();
        }
        if ($orderInfo->orderId)
        {
            $this->hotelBooker->orderId = $orderInfo->orderId;
            $confirmInfo = $hotelBookClient->confirmOrder($orderInfo->orderId);
            SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,HotelBookClient::$requestIds);
            HotelBookClient::$requestIds = array();
            if (!$confirmInfo->error)
            {
                //TODO: добавить задание на переход в состояние bookingTimeLimitError
                $this->status('ticketReady');
            }
            else
            {
                $this->status('ticketingRepeat');
            }

        }
        else
        {
            //TODO: переставить стутус через время T
            $this->status('ticketingRepeat');
        }
    }

    public function stageTicketReady()
    {
        //TODO: ваучер
    }

    public function stageTicketingRepeat()
    {
        echo "event ticketing repeat {$this->hotelBooker->tryCount}\n";
        $this->hotelBooker->tryCount++;
        echo "after ++ {$this->hotelBooker->tryCount}\n";
        //return;


        //$this->hotelBooker->save(); рекурсия
        if ($this->hotelBooker->tryCount > 3)
        {
            $this->status('ticketingError');
            return;
        }

        $hotelOrderParams = new HotelOrderParams();

        $hotelOrderParams->hotel = $this->hotel;
        $contactName = '';
        foreach ($this->hotelBooker->hotelBookingPassports as $passport)
        {
            $roomer = new Roomer();
            $roomer->setFromHotelBookingPassport($passport);
            $roomer->roomId = $passport->roomKey;
            if (!$contactName)
                $contactName = $roomer->fullName;
            $hotelOrderParams->roomers[] = $roomer;
        }
        $hotelOrderParams->contactPhone = $this->hotelBooker->orderBooking->phone;
        $hotelOrderParams->contactEmail = $this->hotelBooker->orderBooking->email;
        $hotelOrderParams->contactName = $contactName;
        $hotelBookClient = new HotelBookClient();
        if ($this->hotelBooker->orderId)
        {
            $orderInfo = new HotelOrderResponse();
            $orderInfo->orderId = $this->hotelBooker->orderId;
        }
        else
        {
            $orderInfo = $hotelBookClient->addOrder($hotelOrderParams);
            SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,HotelBookClient::$requestIds);
            HotelBookClient::$requestIds = array();
        }

        if ($orderInfo->orderId)
        {

            $this->hotelBooker->orderId = $orderInfo->orderId;
            $confirmInfo = $hotelBookClient->confirmOrder($orderInfo->orderId);
            SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,HotelBookClient::$requestIds);
            HotelBookClient::$requestIds = array();

            if (!$confirmInfo->error)
            {

                $this->status('ticketReady');
            }
            else
            {
                echo $this->hotelBooker->id;

                $res = Yii::app()->cron->add(time() + appParams('hotel_repeat_time'), 'hotelbooking', 'ChangeState', array('hotelBookerId' => $this->hotelBooker->id, 'newState' => 'ticketingRepeat'));

                if ($res)
                {
                    $this->hotelBooker->saveTaskInfo('repeatTime', $res);
                }
                //$this->status('ticketingRepeat');
            }

        }
        else
        {

            $res = Yii::app()->cron->add(time() + appParams('hotel_repeat_time'), 'hotelbooking', 'ChangeState', array('hotelBookerId' => $this->hotelBooker->id, 'newState' => 'ticketingRepeat'));
            if ($res)
            {
                $this->hotelBooker->saveTaskInfo('repeatTime', $res);
            }
            //$this->status('ticketingRepeat');
        }

    }

    public function stageManualProcessing()
    {

    }

    public function stageTicketingError()
    {
        $this->status('moneyReturn');
    }

    public function stageManualTicketing()
    {

    }

    public function stageManualSuccess()
    {

    }

    public function stageMoneyReturn()
    {
        //TODO: Процедура разморозки платежа
        //
    }

    public function stageManualError()
    {

    }

    public function stageDone()
    {

    }

    public function stageError()
    {

    }

    public function setHotelBookerFromId($hotelBookerId)
    {
        $this->hotelBooker = HotelBooker::model()->findByPk($hotelBookerId);
        if (!$this->hotelBooker) throw new CException('HotelBooker with id ' . $hotelBookerId . ' not found');
        $this->hotelBooker->setHotelBookerComponent($this);
        $this->hotel = unserialize($this->hotelBooker->hotelInfo);
        $this->hotelBooker->hotel = $this->hotel;
    }

    public function setHotelBookerFromHotel(Hotel $hotel)
    {
        $this->hotelBooker = new HotelBooker();
        $this->hotelBooker->hotel = $hotel;
        $this->hotelBooker->setHotelBookerComponent($this);
        $this->hotel = $hotel;
    }

    public function getHotelBookerId()
    {
        return $this->hotelBooker->id;
    }

    public function checkValid()
    {
        $hotelBookClient = new HotelBookClient();
        return $hotelBookClient->checkHotel($this->hotel);
    }
}
