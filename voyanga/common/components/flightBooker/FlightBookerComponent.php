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

    public function getStatus()
    {
        if ($this->flightBooker!=null)
            return $this->flightBooker->status;
        return 'search';
    }

    //todo: should be moveed to correct place
    public function proccessSearch(FlightSearchParams $params)
    {
        Yii::app()->observer->notify('onBeforeFlightSearch', $this);
        Yii::app()->observer->notify('onAfterFlightSearch', $this);
    }

    public function book(FlightVoyage $flightVoyage)
    {
        $this->flightBooker = new FlightBooker();
        $this->flightBooker->flightVoyage = $flightVoyage;
        $this->flightBooker->status = 'enterCredentials';
        $this->flightBooker->save();
        Yii::app()->user->setState('flightBookerId', $this->flightBooker->id);
    }

    public function bookById($id)
    {
        $this->flightBooker = FlightBooker::model()->findByPk($id);
    }

    public function proccessEnterCredentials()
    {
        Yii::app()->observer->notify('onEnterCredentials', $this);
    }

    public function proccessBooking()
    {
        Yii::app()->observer->notify('onBeforeFlightBooking', $this);
        Yii::app()->observer->notify('onAfterFlightBooking', $this);
    }

    public function proccessWaitingForPayment()
    {
        Yii::app()->observer->notify('onBeforeWaitingForPayment', $this);
        Yii::app()->observer->notify('onAfterWaitingForPayment', $this);
    }

    public function proccessPayment()
    {
        Yii::app()->observer->notify('onBeforePayment', $this);
        Yii::app()->observer->notify('onAfterPayment', $this);
    }

    public function proccessBookingError()
    {
        Yii::app()->observer->notify('onBookingError', $this);
    }

    public function proccessBookingTimeLimitError()
    {
        Yii::app()->observer->notify('onBookingTimeLimitError', $this);
    }

    public function proccessTicketing()
    {
        Yii::app()->observer->notify('onBeforeTicketing', $this);
        Yii::app()->observer->notify('onAfterTicketing', $this);
    }

    public function proccessTicketReady()
    {
        Yii::app()->observer->notify('onBeforeTicketReady', $this);
        Yii::app()->observer->notify('onAfterTicketReady', $this);
    }

    public function proccessTicketingRepeat()
    {
        Yii::app()->observer->notify('onBeforeTicketingRepeat', $this);
        Yii::app()->observer->notify('onAfterTicketingRepeat', $this);
    }

    public function proccessManualProccessing()
    {
        Yii::app()->observer->notify('onBeforeManualProccessing', $this);
        Yii::app()->observer->notify('onAfterManualProccessing', $this);
    }

    public function proccessManualTicketing()
    {
        Yii::app()->observer->notify('onBeforeManualTicketing', $this);
        Yii::app()->observer->notify('onAfterManualTicketing', $this);
    }

    public function proccessTicketingError()
    {
        Yii::app()->observer->notify('onBeforeTicketingError', $this);
        Yii::app()->observer->notify('onAfterTicketingError', $this);
    }

    public function proccessManualError()
    {
        Yii::app()->observer->notify('onBeforeManualError', $this);
        Yii::app()->observer->notify('onAfterManualError', $this);
    }

    public function proccessMoneyReturn()
    {
        Yii::app()->observer->notify('onMoneyReturn', $this);
    }

    public function proccessManualSuccess()
    {
        Yii::app()->observer->notify('onBeforeManualSuccess', $this);
        Yii::app()->observer->notify('onAfterManualSuccess', $this);
    }

    public function proccessBspTransfer()
    {
        Yii::app()->observer->notify('onBeforeBspTransfer', $this);
        Yii::app()->observer->notify('onAfterBspTransfer', $this);
    }

    public function proccessDone()
    {
        Yii::app()->observer->notify('onFlightBookingDone', $this);
    }

    public function proccessError()
    {
        Yii::app()->observer->notify('onFlightBookingError', $this);
    }
}
