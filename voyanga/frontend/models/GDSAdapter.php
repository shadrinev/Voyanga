<?php
/**
 * GDSAdapter class
 * Frontend layer GDS adapter
 * @author oleg
 *
 */
class GDSAdapter extends CApplicationComponent
{
    public function flightSearch($flightSearchParams)
    {
        $nemo = new GDSNemo();
        return $nemo->FlightSearch($flightSearchParams);
    }

    public function flightBooking()
    {

    }

    public function flightTariffRules()
    {

    }

    public function flightTicketing()
    {

    }

    public function flightVoid()
    {

    }
}