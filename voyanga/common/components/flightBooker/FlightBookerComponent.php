<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 19.06.12
 * Time: 10:42
 */
class FlightBookerComponent extends CApplicationComponent
{
    private $flightBooker;
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
        $this->status('waitingForPayment');
    }

    public function stageWaitingForPayment()
    {
        //maybe we need to remove it?
        $this->status('payment');
    }

    public function stageBookingError()
    {

    }

    public function stageBookingTimeLimitError()
    {

    }

    public function stageTicketing()
    {
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
