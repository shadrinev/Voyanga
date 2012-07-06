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
    private $hotel;

    public function init()
    {
        Yii::setPathOfAlias('hotelBooker', realpath(dirname(__FILE__)));
        Yii::import('hotelBooker.actions.*');
        Yii::import('hotelBooker.*');
    }

    public function setHotel($value)
    {
        $this->hotel = $value;
    }

    private function loadModel()
    {
        if ($this->hotelBooker==null)
        {
            $id = Yii::app()->user->getState('flightVoyageId');
            $this->hotelBooker = HotelBooker::model()->findByAttributes(array('flightVoyageId'=>$id));
        }
        return $this->hotelBooker;
    }

    public function getCurrent()
    {
        return $this->loadModel();
    }

    public function getStatus()
    {
        if ($this->hotelBooker!=null)
            return $this->hotelBooker->status;
        return 'search';
    }

    public function book()
    {
        //if we don't have a flight OR we moved to another flight
        if ($this->getCurrent()==null || ($this->getCurrent()->flightVoyage->id != $this->hotel->getId()))
        {
            //if we don't have a flight AND we moved to another flight
            if (($this->getCurrent()!=null) and $this->getCurrent()->flightVoyage->id != $this->hotel->getId())
            {
                $this->hotelBooker = FlightBooker::model()->findByAttributes(array('flightVoyageId'=>$this->hotel->getId()));
            }
            if ($this->hotelBooker == null)
            {
                $this->hotelBooker = new FlightBooker();
                $this->hotelBooker->flightVoyageId = $this->hotel->getId();
                $this->hotelBooker->flightVoyage = $this->hotel;
            }
        }
        $this->hotelBooker->status = 'enterCredentials';
        $this->hotelBooker->save();
        Yii::app()->user->setState('flightVoyageId', $this->hotelBooker->flightVoyage->id);
    }

    public function status($newStatus)
    {
        $this->hotelBooker->status = $newStatus;
        $this->hotelBooker->save();
    }

    public function stageEnterCredentials()
    {

    }

    public function stageAnalyzing()
    {
        $hotelBookClient = new HotelBookClient();
        $hotelBookClient->hotelSearchDetails($this->hotel);
        $this->hotelBooker->hotel = $this->hotel;
        if(($this->hotel->cancelExpiration - time()) > (appParams('hotel_payment_time')*2) )
        {
            $this->status('booking');
        }
        else
        {
            $this->status('hardWaitingForPayment');
        }
    }

    public function stageHardWaitingForPayment()
    {
        $this->status('checkingAvailability');
    }

    public function stageBooking()
    {
        //TODO: подгрузить паспорта из HotelBooker
        $hotelOrderParams = new HotelOrderParams();
        $hotelOrderParams->hotel = $this->hotel;
        $hotelBookClient = new HotelBookClient();
        $orderInfo = $hotelBookClient->addOrder($hotelOrderParams);
        if($orderInfo->orderId)
        {
            $confirmInfo = $hotelBookClient->confirmOrder($orderInfo->orderId);
            if(!$orderInfo->error)
            {
                //TODO: добавить задание на переход в состояние bookingTimeLimitError
                $this->status('softWaitingForPayment');
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

    public function stageSoftWaitingForPayment()
    {
        //переход в SoftStartPayment, если достаточно времени.
    }

    public function stageBookingError()
    {

    }

    public function stageSoftStartPayment()
    {
        //TODO: добавить задание на переход в состояние softWaitingForPayment
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
        $searchParams['rooms'] = array();
        foreach($this->hotel->rooms as $room)
        {
            $searchParams['rooms'][] = array('roomSizeId' => $room->sizeId, 'child' => $room->childCount, 'cots' => $room->cotsCount, 'ChildAge' => $room->childAges[0]);
        }
        $hotelSearchResponse = $hotelBookClient->hotelSearch($searchParams);
        $find = false;
        if($hotelSearchResponse['hotels'])
        {
            foreach($hotelSearchResponse['hotels'] as $hotel)
            {
                if($hotel->key == $hotelKey){
                    $this->hotel = $hotel;
                    $find = true;
                    $this->hotelBooker->hotel = $this->hotel;
                    break;
                }
            }
        }

        if($find)
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
        //TODO: поставить в очередь hardWaitingForPayment

    }

    public function stageTicketing()
    {
        $hotelOrderParams = new HotelOrderParams();
        //TODO: подгрузить паспорта из HotelBooker
        $hotelOrderParams->hotel = $this->hotel;
        $hotelBookClient = new HotelBookClient();
        $orderInfo = $hotelBookClient->addOrder($hotelOrderParams);
        if($orderInfo->orderId)
        {
            $confirmInfo = $hotelBookClient->confirmOrder($orderInfo->orderId);
            if(!$orderInfo->error)
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
        if($this->hotelBooker->tryCount > 3)
        {
            $this->status('moneyReturn');
        }
        $hotelOrderParams = new HotelOrderParams();
        //TODO: подгрузить паспорта из HotelBooker
        $hotelOrderParams->hotel = $this->hotel;
        $hotelBookClient = new HotelBookClient();
        $orderInfo = $hotelBookClient->addOrder($hotelOrderParams);
        if($orderInfo->orderId)
        {
            $confirmInfo = $hotelBookClient->confirmOrder($orderInfo->orderId);
            if(!$orderInfo->error)
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
}
