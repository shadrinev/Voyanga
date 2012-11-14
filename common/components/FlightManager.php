<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 14.11.12
 * Time: 14:19
 */
class FlightManager
{
    static public function injectForBe($flightVoyages, $injectSearchParams=false)
    {
        $newFlights = array();
        foreach ($flightVoyages as $key => $flight)
        {
            $newFlight = $flight;
            if ($injectSearchParams)
            {
                $newFlight['serviceClass'] = $flight['flights'][0]['serviceClass'];
                $newFlight['freeWeight'] = ($newFlight['serviceClass'] == 'E') ? $flight['economFreeWeight'] : $flight['businessFreeWeight'];
                $newFlight['freeWeightDescription'] = ($newFlight['serviceClass'] == 'E') ? $flight['economDescription'] : $flight['businessDescription'];
                unset($newFlight['economFreeWeight']);
                unset($newFlight['businessFreeWeight']);
                unset($newFlight['economDescription']);
                unset($newFlight['businessDescription']);
            }
            $newFlights[] = $newFlight;
        }
        return $newFlights;
    }
}
