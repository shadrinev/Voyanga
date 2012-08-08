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
                $this->hotelBooker->setHotelBookerComponent($this);
                if ($this->hotelBooker)
                    Yii::trace('Done', 'HotelBookerComponent.book');
                else
                    Yii::trace('No such record', 'HotelBookerComponent.book');
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

        Yii::trace(CVarDumper::dumpAsString($this->hotelBooker->getErrors()), 'HotelBookerComponent.book');
        if (!$this->hotelBooker->id)
        {
            $this->hotelBooker->id = $this->hotelBooker->primaryKey;
        }
        Yii::app()->user->setState('hotelResultKey', $this->hotelBooker->hotel->id);
    }

    public function status($newStatus)
    {
        $this->hotelBooker->status = $newStatus;
        return $this->hotelBooker->save();
    }

    public function stageEnterCredentials()
    {

    }

    public function stageAnalyzing()
    {
        $hotelBookClient = new HotelBookClient();
        $hotelBookClient->hotelSearchDetails($this->hotel);
        SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,HotelBookClient::$requestIds);
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
        //echo "innn booking";
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

                $res = Yii::app()->cron->add(date(time() + appParams('hotel_payment_time')), 'HotelBooking', 'ChangeState', array('hotelBookerId' => $this->hotelBooker->id, 'newState' => 'bookingTimeLimitError'));
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
                echo "error";
            }

        }
        else
        {
            $this->status('bookingError');
            echo "error2";
        }
    }

    // this is action
    public function stageSoftWaitingForPayment()
    {
        //переход в SoftStartPayment, если достаточно времени.
        //Написать aciton клика по кнопке и там проверки условия для перехода
    }

    public function stageBookingError()
    {

    }

    public function stageSoftStartPayment()
    {
        if (($this->hotel->cancelExpiration - time()) > appParams('hotel_payment_time'))
        {
            $res = Yii::app()->cron->add(time() + appParams('hotel_payment_time'), 'HotelBooker', 'ChangeState', array('hotelBookerId' => $this->hotelBooker->id, 'newState' => 'softWaitingForPayment'));
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
        //TODO: получение информации о ваучере и отправка денег
        //$vaucher =

    }

    public function stageCheckingAvailability()
    {
        $hotelBookClient = new HotelBookClient();
        $searchParams = array();
        $hotelKey = $this->hotel->key;
        $searchParams['cityId'] = $this->hotel->cityId;
        $searchParamsFull = Yii::app()->cache->get('hotelSearchParams'.Yii::app()->user->getState('avia.cacheId'));
        $searchParams['checkIn'] = $searchParamsFull->checkIn;
        $searchParams['duration'] = $searchParamsFull->duration;
        $searchParams['rooms'] = array();
        foreach ($this->hotel->rooms as $room)
        {
            $searchParams['rooms'][] = array(
                'roomSizeId' => $room->sizeId,
                'child' => $room->childCount ? $room->childCount : 0,
                'cots' => $room->cotsCount,
                'ChildAge' => isset($room->childAges[0]) ? $room->childAges[0] : 0,
                'roomNumber'=>1
            );
        }
        $hotelSearchResponse = $hotelBookClient->hotelSearch($searchParams);
        SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,HotelBookClient::$requestIds);
        HotelBookClient::$requestIds = array();
        $find = false;
        if ($hotelSearchResponse['hotels'])
        {
            foreach ($hotelSearchResponse['hotels'] as $hotel)
            {
                if ($hotel->key == $hotelKey)
                {
                    $this->hotel = $hotel;
                    $find = true;
                    $this->hotelBooker->hotel = $this->hotel;
                    break;
                }
            }
        }

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
        $res = Yii::app()->cron->add(time() + appParams('hotel_payment_time'), 'HotelBooking', 'ChangeState', array('hotelBookerId' => $this->hotelBooker->id, 'newState' => 'hardWaitingForPayment'));
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
        foreach ($this->hotelBooker->hotelBookingPassports as $passport)
        {
            $roomer = new Roomer();
            $roomer->setFromHotelBookingPassport($passport);
            $hotelOrderParams->roomers[] = $roomer;
        }
        $hotelBookClient = new HotelBookClient();
        $orderInfo = $hotelBookClient->addOrder($hotelOrderParams);
        SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,HotelBookClient::$requestIds);
        HotelBookClient::$requestIds = array();
        if ($orderInfo->orderId)
        {
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
        $this->hotelBooker->tryCount++;
        $this->hotelBooker->save();
        if ($this->hotelBooker->tryCount > 3)
        {
            $this->status('moneyReturn');
        }
        $hotelOrderParams = new HotelOrderParams();
        //TODO: подгрузить паспорта из HotelBooker
        $hotelOrderParams->hotel = $this->hotel;
        $hotelBookClient = new HotelBookClient();
        $orderInfo = $hotelBookClient->addOrder($hotelOrderParams);
        SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,HotelBookClient::$requestIds);
        HotelBookClient::$requestIds = array();
        if ($orderInfo->orderId)
        {
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
            //TODO: переставить стутус через время T + считать количество раз.
            $this->status('ticketingRepeat');
        }
    }

    public function stageManualProcessing()
    {

    }

    public function stageTicketingError()
    {

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
}
