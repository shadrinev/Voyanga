<?php

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $nemo = new GDSNemo();
        $flightSearchParams = new FlightSearchParams();
        $flightSearchParams->addRoute(array(
            'adult_count' => 1,
            'child_count' => 0,
            'infant_count' => 0,
            'departure_city_id' => 4466,
            //'arrival_city_id' => 3654,
            'arrival_city_id' => 5753,
            'departure_date' => '12.07.2012'
        ));
        /*$flightSearchParams->addRoute(array(
            'adult_count' => 1,
            'child_count' => 0,
            'infant_count' => 0,
            'departure_city_id' => 4381,
            'arrival_city_id' => 4931,
            'departure_date' => '30.05.2012'
        ));*/
        $flightSearchParams->flight_class = 'E';
        //$nemo->FlightTariffRules();
        $aFlights = $nemo->FlightSearch($flightSearchParams);
        $aParamsFS['aFlights'] = $aFlights;
        $oFlightVoyageStack = new FlightVoyageStack($aParamsFS);
        print_r($oFlightVoyageStack);
        $this->render('index');
    }
}