<?php
class GDSNemo {
    
    private static function request( $sMethod, $oParams, $bCache = FALSE, $iExpiration = 0 ) {
        /*$soapClient = new SoapClient('http://srt.mute-lab.com/nemoflights/wsdl.php?for=SearchFlights');
        $functions = $soapClient->__getFunctions();
        print_r($functions);*/
        print_r( $oParams );
        $oClient = new GDSNemoSoapClient( 'http://srt.mute-lab.com/nemoflights/wsdl.php?for=SearchFlights', array(
                'trace' => 1, 
                'typemap' => array(
                        array(
                                'type_ns' => 'http://spos.ru/flights/2010-02-01', 
                                'type_name' => 'PersonFlightExtensions' ) ) ) );
        $i = 1;
        
        return $oClient->$sMethod( $oParams );
    
    }
    
    public function FlightSearch( FlightSearchParams $oFlightSearchParams ) {
        if ( !( $oFlightSearchParams instanceof FlightSearchParams ) ) {
            throw new CException( Yii::t( 'application', 'Parameter oFlightSearchParams not type of FlightSearchParams' ) );
        }
        //prepare request  structure
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
                                                        '_' => 'MOW' ), 
                                                'ArrAirp' => array(
                                                        'CodeType' => 'IATA', 
                                                        '_' => 'PAR' ) ) ), 
                                'Travellers' => array(
                                        'Traveller' => array(
                                                array(
                                                        'Type' => 'ADT', 
                                                        'Count' => '1' ), 
                                                array(
                                                        'Type' => 'CNN', 
                                                        'Count' => '1' ) ) ), 
                                'Restrictions' => array(
                                        'ClassPref' => 'all', 
                                        'OnlyAvail' => 'true', 
                                        'AirVPrefs' => '', 
                                        'IncludePrivateFare' => 'false', 
                                        'CurrencyCode' => 'RUB' ) ) ), 
                
                'Source' => array(
                        'ClientId' => 102, 
                        'APIKey' => '7F48365D42B73307C99C12A578E92B36', 
                        'Language' => 'UA', 
                        'Currency' => 'RUB' ) );
        $aPairs = array();
        foreach( $oFlightSearchParams->aRoutes as $oRoute ) {
            $aODPair = array();
            $aODPair['DepDate'] = $oRoute->departure_date . 'T00:00:00';
            $aODPair['DepAirp'] = array(
                    'CodeType' => 'IATA', 
                    '_' => $oRoute->departure_city->code );
            $aODPair['ArrAirp'] = array(
                    'CodeType' => 'IATA', 
                    '_' => $oRoute->arrival_city->code );
            $aPairs[] = $aODPair;
        }
        $sFType = 'OW';
        if ( count( $oFlightSearchParams->aRoutes ) == 2 ) {
            $bEqualFrom = $oFlightSearchParams->aRoutes[0]->departure_city_id === $oFlightSearchParams->aRoutes[1]->arrival_city_id;
            $bEqualTo = $oFlightSearchParams->aRoutes[0]->arrival_city_id === $oFlightSearchParams->aRoutes[1]->departure_city_id;
            if ( $bEqualFrom && $bEqualTo ) {
                $sFType = 'RT';
            } else {
                $sFType = 'CR';
            }
        } elseif ( count( $oFlightSearchParams->aRoutes ) > 2 ) {
            $sFType = 'CR';
        }
        $aParams['Request']['SearchFlights']['ODPairs']['Type'] = $sFType;
        $aParams['Request']['SearchFlights']['ODPairs']['ODPair'] = CUtilsHelper::normalizeArray( $aPairs );
        unset( $aPairs );
        
        $aTravelleler = array();
        if ( $oFlightSearchParams->adult_count > 0 ) {
            $aTravelleler = array(
                    'Type' => 'ADN', 
                    'Count' => $oFlightSearchParams->adult_count );
        }
        if ( $oFlightSearchParams->child_count > 0 ) {
            $aTravelleler = array(
                    'Type' => 'CNN', 
                    'Count' => $oFlightSearchParams->child_count );
        }
        if ( $oFlightSearchParams->infant_count > 0 ) {
            $aTravelleler = array(
                    'Type' => 'INF', 
                    'Count' => $oFlightSearchParams->infant_count );
        }
        $aParams['Request']['SearchFlights']['Travellers']['Traveller'] = CUtilsHelper::normalizeArray( $aTravelleler );
        unset( $aTravelleler );
        
        //real request
        $oSoapResponse = self::request( 'search', $aParams, $bCache = FALSE, $iExpiration = 0 );
        
        //processing response
        

        //print_r( $oSoapResponse );
        Yii::beginProfile( 'processingSoap' );
        $aFlights = array();
        CUtilsHelper::soapObjectsArray( $oSoapResponse->Response->SearchFlights->Flights->Flight );
        foreach( $oSoapResponse->Response->SearchFlights->Flights->Flight as $oSoapFlight ) {
            $aParts = array();
            Yii::beginProfile( 'processingSegments' );
            CUtilsHelper::soapObjectsArray( $oSoapFlight->Segments->Segment );
            foreach( $oSoapFlight->Segments->Segment as $oSegment ) {
                $oPart = new stdClass();
                Yii::beginProfile( 'laodAirportData' );
                $oPart->departure_airport = Airport::getAirportByCode( $oSegment->DepAirp->_ );
                //Yii::endProfile('laodAirportData');
                $oPart->departure_city = $oPart->departure_airport->city;
                //Yii::beginProfile('laodAirportData');
                $oPart->arrival_airport = Airport::getAirportByCode( $oSegment->ArrAirp->_ );
                
                $oPart->arrival_city = $oPart->arrival_airport->city;
                Yii::endProfile( 'laodAirportData' );
                $oPart->departure_terminal_code = $oSegment->DepTerminal;
                $oPart->arrival_terminal_code = $oSegment->ArrTerminal;
                $oPart->airline = Airline::getAirlineByCode( $oSegment->MarkAirline );
                $oPart->code = $oSegment->FlightNumber;
                $oPart->duration = $oSegment->FlightTime * 60;
                $oPart->datetime_begin = $oSegment->DepDateTime->_;
                $oPart->datetime_end = $oSegment->DepDateTime->_;
                $oPart->aircraft_code = $oSegment->AircraftType;
                $oPart->transport_airline = Airline::getAirlineByCode( $oSegment->OpAirline );
                $oPart->aTariffs = array();
                $oPart->aTaxes = array();
                $oPart->aBookingCodes = array();
                CUtilsHelper::soapObjectsArray( $oSegment->BookingCodes->BookingCode );
                foreach( $oSegment->BookingCodes->BookingCode as $sBookingCode ) {
                    $oPart->aBookingCodes[] = $sBookingCode;
                }
                $aParts[$oSegment->SegNum] = $oPart;
            }
            Yii::endProfile( 'processingSegments' );
            $full_sum = 0;
            $aPassengers = array();
            $aTariffs = array();
            Yii::beginProfile( 'processingPassengers' );
            CUtilsHelper::soapObjectsArray( $oSoapFlight->PricingInfo->PassengerFare );
            foreach( $oSoapFlight->PricingInfo->PassengerFare as $oFare ) {
                $sType = $oFare->Type;
                $aPassengers[$sType]['count'] = $oFare->Quantity;
                $aPassengers[$sType]['base_fare'] = $oFare->BaseFare->Amount;
                $aPassengers[$sType]['total_fare'] = $oFare->TotalFare->Amount;
                $full_sum += ( $oFare->TotalFare->Amount * $oFare->Quantity );
                $aPassengers[$sType]['LastTicketDateTime'] = $oFare->LastTicketDateTime;
                $aPassengers[$sType]['aTaxes'] = array();
                CUtilsHelper::soapObjectsArray( $oFare->Taxes->Tax );
                foreach( $oFare->Taxes->Tax as $oTax ) {
                    if ( $oTax->CurCode == 'RUB' ) {
                        $aPassengers[$sType]['aTaxes'][$oTax->TaxCode] = $oTax->Amount;
                    } else {
                        throw new CException( Yii::t( 'application', 'Valute code unexpected. Code: {code}. Expected RUB', array(
                                '{code}' => $oTax->CurCode ) ) );
                    }
                }
                CUtilsHelper::soapObjectsArray( $oFare->Tariffs->Tariff );
                foreach( $oFare->Tariffs->Tariff as $oTariff ) {
                    $aParts[$oTariff->SegNum]->aTariffs[$oTariff->Code] = $oTariff->Code;
                }
            }
            Yii::endProfile( 'processingPassengers' );
            $aNewParts = array();
            //print_r($aParts);
            $oPart = reset( $aParts );
            foreach( $oFlightSearchParams->aRoutes as $oRoute ) {
                $aSubParts = array();
                $aCities = array();
                while( $oPart ) {
                    $aSubParts[] = $oPart;
                    $aCities[] = $oPart->arrival_city->code;
                    if ( $oRoute->arrival_city->code === $oPart->arrival_city->code ) {
                        $oPart = next( $aParts );
                        break;
                    }
                    $oPart = next( $aParts );
                }
                if ( !$oPart ) {
                    $oPart = end( $aParts );
                    
                    if ( $oRoute->arrival_city->code !== $oPart->arrival_city->code ) {
                        throw new CException( Yii::t( 'application', 'Not found segment with code arrival city {code}. Segment cityes: {codes}', array(
                                '{code}' => $oRoute->arrival_city->code, 
                                '{codes}' => implode( ', ', $aCities ) ) ) );
                    }
                }
                $aNewParts[] = $aSubParts;
            }
            
            $oFlight = new stdClass();
            $oFlight->full_sum = $full_sum;
            $oFlight->commission_price = 0;
            $oFlight->flight_key = $oSoapFlight->FlightId;
            $oFlight->parts = $aNewParts;
            $aFlights[] = $oFlight;
        }
        Yii::endProfile( 'processingSoap' );
        //print_r($aFlights);
        

        return $aFlights;
    
    }
    
    public function FlightBooking( FlightBookingParams $oFlightBookingParams ) {
        if ( !( $oFlightBookingParams instanceof FlightBookingParams ) ) {
            throw new CException( Yii::t( 'application', 'Parameter oFlightBookingParams not type of FlightBookingParams' ) );
        }
        $aParams = array(
                'Request' => array(
                        'BookFlight' => array(
                                'FlightId' => 534733, 
                                'BookingCodes' => array(
                                        'BookingCode' => array(
                                                'Code' => 'Q', 
                                                'SegNumber' => '1' ) ), 
                                'Agency' => array(
                                        'Name' => 'Easy', 
                                        'Address' => array(
                                                'City' => 'Saint-Petersburg' ) ), 
                                'Travellers' => array(
                                        'Traveller' => array(
                                                array(
                                                        'Type' => 'ADT', 
                                                        'Num' => '1', 
                                                        'PersonalInfo' => array(
                                                                'DateOfBirth' => '01.01.1985', 
                                                                'Nationality' => 'RU', 
                                                                'Gender' => 'M', 
                                                                'FirstName' => 'Aleksandr', 
                                                                'LastName' => 'Kovalev' ), 
                                                        'DocumentInfo' => array(
                                                                'DocType' => 'М', 
                                                                'DocNum' => 'asdawe131', 
                                                                'CountryCode' => 'RU', 
                                                                'DocElapsedTime' => '24.03.2026' ) ), 
                                                array(
                                                        'Type' => 'ADT', 
                                                        'Count' => '1' ) ) ) ), 
                        'Source' => array(
                                'ClientId' => 102, 
                                'APIKey' => '7F48365D42B73307C99C12A578E92B36', 
                                'Language' => 'UA', 
                                'Currency' => 'RUB' ) ) );
        if ( $oFlightBookingParams->checkValid() ) {
            $aTraveler = array();
            $iNum = 1;
            foreach( $oFlightBookingParams->aPassengers as $oPassenger ) {
                $oTraveller = array();
                $oTraveller['Type'] = Yii::app()->params['aPassegerTypes'][$oPassenger->iType];
                $oTraveller['Num'] = $iNum;
                $oTraveller['PersonalInfo'] = array();
                $oTraveller['PersonalInfo']['DateOfBirth'] = CUtilsHelper::dateToPointDate( $oPassenger->oPassport->birthday );
                $oTraveller['PersonalInfo']['Nationality'] = Country::model()->findByPk( $oPassenger->oPassport->country_id )->code;
                $oTraveller['PersonalInfo']['Gender'] = $oPassenger->oPassport->gender_id == 1 ? 'M' : 'F';
                $oTraveller['PersonalInfo']['FirstName'] = $oPassenger->oPassport->first_name;
                $oTraveller['PersonalInfo']['LastName'] = $oPassenger->oPassport->last_name;
                $oTraveller['DocumentInfo'] = array();
                $oTraveller['DocumentInfo']['DocType'] = $oPassenger->oPassport->document_type_id;
                $oTraveller['DocumentInfo']['DocNum'] = $oPassenger->oPassport->number;
                $oTraveller['DocumentInfo']['CountryCode'] = Country::model()->findByPk( $oPassenger->oPassport->country_id )->code;
                $oTraveller['DocumentInfo']['DocElapsedTime'] = CUtilsHelper::dateToPointDate( $oPassenger->oPassport->expiration);
            }
        } else {
            throw new CException( Yii::t( 'application', 'Data in parameter oFlightBookingParams not valid' ) );
        }
        print_r( self::request( 'bookFlight', $aParams, $bCache = FALSE, $iExpiration = 0 ) );
    }
    
    public function FlightTariffRules() {
        $aParams = array(
                'Request' => array(
                        'GetAirRules' => array(
                                'FlightId' => 534733 ), 
                        'Source' => array(
                                'ClientId' => 102, 
                                'APIKey' => '7F48365D42B73307C99C12A578E92B36', 
                                'Language' => 'UA', 
                                'Currency' => 'RUB' ) ) );
        
        print_r( self::request( 'GetAirRules', $aParams, $bCache = FALSE, $iExpiration = 0 ) );
    }
    
    public function checkFlight() {
        $aParams = array(
                'Request' => array(
                        'AirAvail' => array(
                                'FlightId' => 534733 ), 
                        'Source' => array(
                                'ClientId' => 102, 
                                'APIKey' => '7F48365D42B73307C99C12A578E92B36', 
                                'Language' => 'UA', 
                                'Currency' => 'RUB' ) ) );
        
        print_r( self::request( 'AirAvail', $aParams, $bCache = FALSE, $iExpiration = 0 ) );
    }
    
    public function FlightTicketing() {
        $aParams = array(
                'Request' => array(
                        'Ticketing' => array(
                                'BookID' => 534733, 
                                'ValCompany' => '', 
                                'Commision' => array(
                                        'Percent' => '2' ) ), 
                        'Source' => array(
                                'ClientId' => 102, 
                                'APIKey' => '7F48365D42B73307C99C12A578E92B36', 
                                'Language' => 'UA', 
                                'Currency' => 'RUB' ) ) );
        
        print_r( self::request( 'bookFlight', $aParams, $bCache = FALSE, $iExpiration = 0 ) );
    
    }
    
    public function FlightVoid() {
        $aParams = array(
                'Request' => array(
                        'VoidTicket' => array(
                                'BookID' => 534733 ), 
                        'Source' => array(
                                'ClientId' => 102, 
                                'APIKey' => '7F48365D42B73307C99C12A578E92B36', 
                                'Language' => 'UA', 
                                'Currency' => 'RUB' ) ) );
        
        print_r( self::request( 'bookFlight', $aParams, $bCache = FALSE, $iExpiration = 0 ) );
    }
}

