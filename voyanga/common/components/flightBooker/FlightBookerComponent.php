<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 19.06.12
 * Time: 10:42
 */
class FlightBookerComponent extends CApplicationComponent
{
    /** @var FlightBooker */
    private $flightBooker;
    /** @var FlightVoyage */
    private $flightVoyage;

    public function init()
    {
        Yii::setPathOfAlias('flightBooker', realpath(dirname(__FILE__)));
        Yii::import('flightBooker.actions.*');
        Yii::import('flightBooker.*');
    }

    public function setFlightVoyage($value)
    {
        $this->flightVoyage = $value;
    }

    private function loadModel()
    {
        if ($this->flightBooker==null)
        {
            $id = Yii::app()->user->getState('flightVoyageId');
            $this->flightBooker = FlightBooker::model()->findByAttributes(array('flightVoyageId'=>$id));
        }
        return $this->flightBooker;
    }

    public function getCurrent()
    {
        return $this->loadModel();
    }

    public function getStatus()
    {
        if ($this->flightBooker!=null)
            return $this->flightBooker->status;
        return 'search';
    }

    public function book()
    {
        //if we don't have a flight OR we moved to another flight
        if ($this->getCurrent()==null || ($this->getCurrent()->flightVoyage->id != $this->flightVoyage->getId()))
        {
            //if we don't have a flight AND we moved to another flight
            if (($this->getCurrent()!=null) and $this->getCurrent()->flightVoyage->id != $this->flightVoyage->getId())
            {
                $this->flightBooker = FlightBooker::model()->findByAttributes(array('flightVoyageId'=>$this->flightVoyage->getId()));
            }
            if ($this->flightBooker == null)
            {
                $this->flightBooker = new FlightBooker();
                $this->flightBooker->flightVoyageId = $this->flightVoyage->getId();
                $this->flightBooker->flightVoyage = $this->flightVoyage;
            }
        }
        $this->flightBooker->status = 'enterCredentials';
        $this->flightBooker->save();
        Yii::app()->user->setState('flightVoyageId', $this->flightBooker->flightVoyage->id);
    }

    public function status($newStatus)
    {
        $this->flightBooker->status = $newStatus;
        $this->flightBooker->save();
    }

    public function stageBooking()
    {
        //getting pnr and other stuff

        //$this->flightBooker->booking->bookingPassports;
        $flightBookingParams = new FlightBookingParams();

        //VarDumper::dump($this->flightBooker->booking);die();
        $flightBookingParams->contactEmail = $this->flightBooker->booking->email;
        $flightBookingParams->phoneNumber = $this->flightBooker->booking->phone;
        $flightBookingParams->flightId = $this->flightVoyage->flightKey;

        foreach($this->flightBooker->booking->bookingPassports as $passport)
        {
            $passenger = new Passenger();
            $passenger->type = Passenger::TYPE_ADULT;
            $passenger->passport = $passport;
            $flightBookingParams->addPassenger($passenger);
        }
        //$flightBookingParams->addPassenger();
        //echo 123;//die();
        /** @var FlightBookingResponse $flightBookingResponse  */
        $flightBookingResponse = Yii::app()->gdsAdapter->FlightBooking($flightBookingParams);
        if($flightBookingResponse->status == 1)
        {
            $this->flightBooker->nemoBookId = $flightBookingResponse->nemoBookId;
            $this->flightBooker->pnr = $flightBookingResponse->status;
            $this->flightBooker->timeout = $flightBookingResponse->expiration;
        }
        //die();
        $this->status('waitingForPayment');
    }

    public function stageWaitingForPayment()
    {
        //maybe we need to remove it?
        //TODO: ставим таймер на отмену приема платежа
        //переход в состояние payment должен быть инициализирован из вне
        //$this->status('payment');
        $res = Yii::app()->cron->add(date(time() + appParams('hotel_payment_time')), 'FlightBooker','ChangeState',array('flightBookerId'=>$this->hotelBooker->id,'newState'=>'bookingTimeLimitError'));
        if($res)
        {
            $this->flightBooker->saveTaskInfo('paymentTimeLimit',$res);
            return true;
        }
    }

    public function stageBookingError()
    {

    }

    public function stageBookingTimeLimitError()
    {
        $bookingId = 123;
        Yii::app()->gdsAdapter->cancelBooking($bookingId);
    }

    public function stageStartPayment()
    {

    }

    public function stageTicketing()
    {
        /** @var FlightBookingResponse $flightBookingResponse  */
        $flightTicketingParams = new FlightTicketingParams();
        $flightTicketingParams->nemoBookId = $this->flightBooker->nemoBookId;
        $flightTicketingParams->pnr = $this->flightBooker->pnr;
        $flightTicketingResponse = Yii::app()->gdsAdapter->FlightTicketing($flightTicketingParams);
        if($flightTicketingResponse->status == 1)
        {
            //TODO: need save tickets numbers to DB
            foreach($flightTicketingResponse->tickets as $ticketInfo){

            }
        }
        $this->status('ticketReady');
    }

    public function stageTicketReady()
    {
        $this->status('done');
    }

    public function stageTicketingRepeat()
    {

    }

    public function stageManualProcessing()
    {

    }

    public function stageManualTicketing()
    {

    }

    public function stageTicketingError()
    {

    }

    public function stageManualError()
    {

    }

    public function stageMoneyReturn()
    {

    }

    public function stageManualSuccess()
    {

    }

    public function stageBspTransfer()
    {

    }

    public function stageDone()
    {

    }

    public function stageError()
    {

    }

    public function setFlightBookerFromId($flightBookerId)
    {
        $this->flightBooker = FlightBooker::model()->findByPk($flightBookerId);
        if(!$this->flightBooker) throw new CException('FlightBooker with id '.$flightBookerId.' not found');
    }

    public function getFlightBookerId()
    {
        return $this->flightBooker->id;
    }
}
