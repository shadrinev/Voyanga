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
        $response = $nemo->FlightSearch($flightSearchParams);

        return $response['flights'];
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