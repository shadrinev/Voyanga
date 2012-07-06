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

    }

    public function stageHardWaitingForPayment()
    {

    }

    public function stageBooking()
    {

    }

    public function stageSoftWaitingForPayment()
    {

    }

    public function stageBookingError()
    {

    }

    public function stageSoftStartPayment()
    {

    }

    public function stageBookingTimeLimitError()
    {

    }

    public function stageMoneyTransfer()
    {

    }

    public function stageCheckingAvailability()
    {

    }

    public function stageAvailabilityError()
    {

    }

    public function stageHardStartPayment()
    {

    }

    public function stageTicketing()
    {

    }

    public function stageTicketReady()
    {

    }

    public function stageTicketingRepeat()
    {

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
