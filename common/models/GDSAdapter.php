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

    /**
     * @param $flightSearchParams
     * @return array
     */
    public function flightSearch($flightSearchParams)
    {
        $nemo = new GDSNemoAgency();
        $response = $nemo->FlightSearch($flightSearchParams);

        return $response;
    }

    public function flightBooking(FlightBookingParams $flightBookingParams)
    {
        $nemo = new GDSNemoAgency();
        $response = $nemo->FlightBooking($flightBookingParams);

        return $response;
    }

    public function flightTariffRules()
    {

    }

    public function flightTicketing(FlightTicketingParams $flightTicketingParams)
    {
        $nemo = new GDSNemoAgency();
        $response = $nemo->FlightTicketing($flightTicketingParams);

        return $response;
    }

    public function flightVoid()
    {

    }

    public function cancelBooking($bookingId)
    {
        $nemo = new GDSNemoAgency();
        $response = $nemo->CancelBooking($bookingId);
        return $response;
    }
}