<?php
class FlightBookingParams
{
    public $phoneNumber;
    public $contactEmail;
    public $flightId;
    public $flightClass;
    /** @var Passenger[] */
    public $passengers = array();

    public function addPassenger($oPassenger)
    {
        if ($oPassenger instanceof Passenger)
        {
            $this->passengers[] = $oPassenger;
        } else
        {
            throw new CException(Yii::t('application', 'Parameter oPassenger must be instance of Passenger'));
        }
    }

    public function checkValid()
    {
        $bValid = false;
        foreach ($this->passengers as $oPassenger)
        {
            $bValid = true;
            $bValid = $bValid && $oPassenger->checkValid();
        }
        return $bValid;
    }
}