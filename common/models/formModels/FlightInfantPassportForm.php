<?php
class FlightInfantPassportForm extends BaseFlightPassportForm
{
    public $documentTypeId = BaseFlightPassportForm::TYPE_BIRTH_CERT;
    public $passengerType = Passenger::TYPE_INFANT;
}