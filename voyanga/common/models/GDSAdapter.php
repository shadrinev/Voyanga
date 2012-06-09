<?php
/**
 * GDSAdapter class
 * Frontend layer GDS adapter
 * @author oleg
 *
 */
class GDSAdapter extends CApplicationComponent
{
    public function init()
    {
        Yii::import('site.common.modules.gds.models.*');
    }

    public function flightSearch($flightSearchParams)
    {
        $nemo = new GDSNemoAgency();
        $response = $nemo->FlightSearch($flightSearchParams);

        return $response;
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