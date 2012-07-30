<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 15.05.12
 * Time: 18:58
 * To change this template use File | Settings | File Templates.
 */
class FlightPricing
{
    public static function getPriceInfo(FlightVoyage $flightVoyage)
    {
        return array('fullPrice'=>$flightVoyage->price,'commissionPrice'=>0, 'profitPrice');
    }
}
