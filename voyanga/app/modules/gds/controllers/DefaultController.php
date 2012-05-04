<?php

class DefaultController extends Controller {
    public function actionIndex() {
        $Nemo = new GDSNemo();
        $aParams = array(
                'Request' => array(
                        'SearchFlights' => array(
                                'LinkOnly' => false, 
                                'ODPairs' => array(
                                        'Type' => 'OW', 
                                        'Direct' => "false", 
                                        'AroundDates' => "0", 
                                        'ODPair' => array(
                                                'DepDate' => '2011-06-11T00:00:00', 
                                                'DepAirp' => array(
                                                        'CodeType' => 'IATA', 
                                                        '_'=>'MOW' ), 
                                                'ArrAirp' => array(
                                                        'CodeType' => 'IATA', 
                                                        '_'=>'PAR' ) )
                                 ),
                                 'Travellers'=>array('Traveller'=>array(array('Type'=>'ADT','Count'=>'1'),array('Type'=>'ADT','Count'=>'1'))),
                                 'Restrictions'=>array('ClassPref'=>'all','OnlyAvail'=>'true','AirVPrefs'=>'','IncludePrivateFare'=>'false','CurrencyCode'=>'RUB'),
         
         ) ),
         'Source'=>array('ClientId'=>102,
         'APIKey'=>'7F48365D42B73307C99C12A578E92B36',
         'Language'=>'UA',
         'Currency'=>'RUB'
         ) );
        $oFlightSearchParams = new FlightSearchParams();
        $oFlightSearchParams->addRoute( array(
                'adult_count' => 1, 
                'child_count' => 0, 
                'infant_count' => 0, 
                'departure_city_id' => 4931, 
                'arrival_city_id' => 4466, 
                'departure_date' => '21.05.2012' ) );
        $oFlightSearchParams->addRoute( array(
                'adult_count' => 1, 
                'child_count' => 0, 
                'infant_count' => 0, 
                'departure_city_id' => 4381, 
                'arrival_city_id' => 4931, 
                'departure_date' => '30.05.2012' ) );
        $oFlightSearchParams->flight_class = 'E';
        $aFlights = $Nemo->FlightSearch( $oFlightSearchParams );
        $aParamsFS['aFlights'] = $aFlights;
        $oFlightVoyageStack = new FlightVoyageStack( $aParamsFS );
        print_r($oFlightVoyageStack);
        $this->render( 'index' );
    }
}