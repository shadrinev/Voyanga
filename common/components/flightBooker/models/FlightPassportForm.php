<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 16.07.12
 * Time: 12:11
 */
class FlightPassportForm extends CFormModel
{
    /** @var PassengerPassportForm[] */
    public $passengerPassports = array();

    /** @var BookingForm */
    public $bookingForm;

    public function init()
    {
        $this->bookingForm = new BookingForm();
    }

    public function addPassengers($adults, $children, $infants)
    {
        $passengerPassports = new PassengerPassportForm();
        $passengerPassports->addPassports($adults, $children,$infants);
        $this->passengerPassports = $passengerPassports;
    }
}
