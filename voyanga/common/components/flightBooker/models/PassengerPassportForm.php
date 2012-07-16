<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 16.07.12
 * Time: 12:12
 */
class PassengerPassportForm extends CFormModel
{
    /** @var FlightAdultPassportForm[] */
    public $adultsPassports = array();

    /** @var FlightChildPassportForm[] */
    public $childrenPassports = array();

    /** @var FlightInfantPassportForm[] */
    public $infantPassports = array();

    public function addPassports($adultCount, $childCount=0, $infantCount=0)
    {
        for ($i=0; $i<(int)$adultCount; $i++)
        {
            $this->adultsPassports[] = new FlightAdultPassportForm();
        }
        for ($i=0; $i<(int)$childCount; $i++)
        {
            $this->childrenPassports[] = new FlightChildPassportForm();
        }
        for ($i=0; $i<(int)$infantCount; $i++)
        {
            $this->infantPassports[] = new FlightInfantPassportForm();
        }
    }
}
