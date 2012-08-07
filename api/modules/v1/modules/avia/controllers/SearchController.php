<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 07.08.12
 * Time: 12:17
 */
class SearchController extends ApiController
{
    public $defaultAction = 'default';

    /**
     * @param array $destinations
     *  [Х][departure] - departure city iata code,
     *  [Х][arrival] - arrival city iata code,
     *  [Х][date] - departure date,
     *
     * @param int $adt amount of adults
     * @param int $chd amount of childs
     * @param int $inf amount of infanties
     * @param string $service_class (A = all | E = economy | B = business)
     */
    public function actionDefault($destinations, $adt = 1, $chd = 0, $inf = 0, $service_class = 'A')
    {
        $flightSearchParams = new FlightSearchParams();
        foreach ($destinations as $route)
        {
            $departureDate = date('d.m.Y', strtotime($route['date']));
            $flightSearchParams->addRoute(array(
                'adult_count' => $adt,
                'child_count' => $chd,
                'infant_count' => $inf,
                'departure_city_id' => City::model()->getCityByCode($route['departure']),
                'arrival_city_id' => City::model()->getCityByCode($route['arrival']),
                'departure_date' => $departureDate,
            ));
            $flightSearchParams->flight_class = $service_class;
        }
        $fs = new FlightSearch();
        $variants = $fs->sendRequest($flightSearchParams, false);
        $results = $variants->getJsonObject();
        $this->sendJson($results);
    }
}
