<?php
class FlightChildPassportForm extends BaseFlightPassportForm
{
    public $documentTypeId = BaseFlightPassportForm::TYPE_BIRTH_CERT;
    public $passengerType = Passenger::TYPE_CHILD;

}