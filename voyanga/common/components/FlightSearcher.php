<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 21.05.12
 * Time: 13:41
 */
class FlightSearcher extends Component
{
    public static function getOptimalPrice($from, $to, $dateStart, $dateEnd, $forceUpdate)
    {
        return MFlightSearch::getOptimalPrice($from, $to, $dateStart, $dateEnd, $forceUpdate);
    }
}
