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

    public function stageBooking()
    {
        //getting pnr and other stuff

        //$this->flightBooker->booking->bookingPassports;
        $flightBookingParams = new FlightBookingParams();

        //VarDumper::dump($this->flightBooker->booking);die();
        $flightBookingParams->contactEmail = $this->hotelBooker->booking->email;
        $flightBookingParams->phoneNumber = $this->hotelBooker->booking->phone;
        $flightBookingParams->flightId = $this->hotel->flightKey;

        foreach($this->hotelBooker->booking->bookingPassports as $passport)
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
            $this->hotelBooker->nemoBookId = $flightBookingResponse->nemoBookId;
            $this->hotelBooker->pnr = $flightBookingResponse->status;
            $this->hotelBooker->timeout = $flightBookingResponse->expiration;
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
    }

    public function stageBookingError()
    {

    }

    public function stageBookingTimeLimitError()
    {

    }

    public function stagePayment()
    {

    }

    public function stageTicketing()
    {
        /** @var FlightBookingResponse $flightBookingResponse  */
        $flightTicketingParams = new FlightTicketingParams();
        $flightTicketingParams->nemoBookId = $this->hotelBooker->nemoBookId;
        $flightTicketingParams->pnr = $this->hotelBooker->pnr;
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

    public function stageManualProccessing()
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
}
