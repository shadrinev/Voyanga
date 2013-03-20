<?php
class GDSNemoAgency extends CComponent
{
    public $wsdlUri = null;
    public static $lastRequestDescription = '';
    public static $passportTypesMap = array(1 => 'C', 2 => 'A', 3 => 'V');
    public static $requestIds = array();
    const ERROR_CODE_EMPTY = 1;
    const ERROR_CODE_INVALID = 2;

    /**
     * @static
     * @param $methodName name of method to call
     * @param $params params for calling method
     * @param bool $useCache is cache it
     * @param int $expirationTimeSeconds expiration time in seconds
     * @return mixed
     */
    private static function request($methodName, $params)
    {
        $methodMap = array(
            'Search' => 'SearchFlights',
            'BookFlight' => 'BookFlight',
            'Ticketing' => 'Ticketing',
            'AirAvail' => 'AirAvail',
            'CancelBook' => 'CancelBook',
            'GetAirRules'=> 'GetAirRules'
        );
        $bookingMethods = array(
            'BookFlight'=>true,
            'Ticketing'=>true
        );

        if (!($wsdl = isset($methodMap[$methodName]) ? $methodMap[$methodName] : false))
        {
            throw new CException('Unknown method request');
        }

        $wsdlUri = Yii::app()->params['GDSNemo']['agencyWsdlUri'];
        if(isset($bookingMethods[$methodName])){
            $wsdlUri = Yii::app()->params['GDSNemo']['agencyBookingWsdlUri'];
        }
        $client = new GDSNemoSoapClient(
            $wsdlUri . $wsdl,
            array(
                'trace' => Yii::app()->params['GDSNemo']['trace'],
                'exceptions' => true,
            )
        );

        $mongoKey = substr(md5($methodName . uniqid('', true)), 0, 10);
        $gdsRequest = new GdsRequest();
        $gdsRequest->requestNum = $mongoKey;
        self::$requestIds[] = array('key' => $mongoKey, 'class' => get_class($gdsRequest), 'keyName' => 'requestNum');
        $gdsRequest->timestamp = time();
        $gdsRequest->methodName = $methodName;
        $gdsRequest->requestDescription = self::$lastRequestDescription;
        $client->gdsRequest = $gdsRequest;
        Yii::beginProfile('processingSoap' . $methodName);
        try
        {
            $soapResponse = $client->$methodName($params);
        } catch(SoapFault $e) {
            Yii::log(CVarDumper::dumpAsString($e), CLogger::LEVEL_ERROR, 'application.Gds.GdsNemoAgency.request');
            //! FIXME log nemo fail
        }
        catch (Exception $e)
        {
            Yii::log(CVarDumper::dumpAsString($e), CLogger::LEVEL_ERROR, 'application.Gds.GdsNemoAgency.request');
            throw $e;
        }
        Yii::endProfile('processingSoap' . $methodName);
        return $soapResponse;
    }

    private function requestCached($methodName, $params, $expirationTimeInSeconds = 120)
    {
        $key = $methodName . md5(serialize($params));
        if ($soapResponse = Yii::app()->cache->get($key))
        {
            return $soapResponse;
        }
        $soapResponse = $this->request($methodName, $params);
        if ($soapResponse)
        {
            Yii::app()->cache->set($key, $soapResponse, $expirationTimeInSeconds);
        }
    }

    public function FlightSearch(FlightSearchParams $flightSearchParams)
    {
        if (!($flightSearchParams instanceof FlightSearchParams))
        {
            throw new CException(Yii::t('application', 'Parameter oFlightSearchParams not type of FlightSearchParams'));
        }
        //VarDumper::dump($flightSearchParams);
        //prepare request  structure
        $flightClass = 'all';
        switch ($flightSearchParams->flight_class)
        {
            case 'E':
                $flightClass = 'economy';
                break;
            case 'B':
                $flightClass = 'business';
                break;
            case 'F':
                $flightClass = 'first';
                break;
        }

        $params = array(
            'Request' => array(
                'SearchFlights' => array(
                    'ODPairs' => array(
                        'Type' => 'OW',
                        'Direct' => false,
                        'AroundDates' => "0",

                        'ODPair' => array(
                            'DepDate' => '2012-06-13T00:00:00',
                            'DepAirp' => 'MOW',
                            'ArrAirp' => 'IJK'

                        )
                    ),
                    'Travellers' => array(
                        'Traveller' => array(
                            array(
                                'Type' => 'ADT',
                                'Count' => '1'
                            )
                        )
                    ),
                    'Restrictions' => array(
                        'ClassPref' => $flightClass,
                        'OnlyAvail' => true,
                        'AirVPrefs' => '',
                        'IncludePrivateFare' => false,
                        'CurrencyCode' => 'RUB'
                    ),

                )
            ),
            'Source' => array(
                'ClientId' => Yii::app()->params['GDSNemo']['agencyId'],
                'APIKey' => Yii::app()->params['GDSNemo']['agencyApiKey'],
                'Language' => 'RU',
                'Currency' => 'RUB'
            )
        );
        self::$lastRequestDescription = '';
        $pairs = array();
        foreach ($flightSearchParams->routes as $route)
        {
            //todo: think how to name it
            $ODPair = array();
            $ODPair['DepDate'] = $route->departureDate . 'T00:00:00';
            $ODPair['DepAirp'] = $route->departureCity->code;
            $ODPair['ArrAirp'] = $route->arrivalCity->code;
            self::$lastRequestDescription .= "{$route->departureDate}: {$route->departureCity->code} - {$route->arrivalCity->code}; ";
            $pairs[] = $ODPair;
        }
        $flightType = 'OW';
        if (count($flightSearchParams->routes) == 2)
        {
            $equalFrom = $flightSearchParams->routes[0]->departureCityId === $flightSearchParams->routes[1]->arrivalCityId;
            $equalTo = $flightSearchParams->routes[0]->arrivalCityId === $flightSearchParams->routes[1]->departureCityId;
            if ($equalFrom && $equalTo)
            {
                $flightType = 'RT';
            }
            else
            {
                $flightType = 'CR';
            }
        }
        elseif (count($flightSearchParams->routes) > 2)
        {
            $flightType = 'CR';
        }
        $params['Request']['SearchFlights']['ODPairs']['Type'] = $flightType;
        $params['Request']['SearchFlights']['ODPairs']['ODPair'] = UtilsHelper::normalizeArray($pairs);
        unset($pairs);

        $traveller = array();
        if ($flightSearchParams->adultCount > 0)
        {
            $traveller[] = array(
                'Type' => 'ADT',
                'Count' => $flightSearchParams->adultCount
            );
            self::$lastRequestDescription .= "ADT: {$flightSearchParams->adultCount}; ";
        }
        if ($flightSearchParams->childCount > 0)
        {
            $traveller[] = array(
                'Type' => 'CNN',
                'Count' => $flightSearchParams->childCount
            );
            self::$lastRequestDescription .= "CNN: {$flightSearchParams->childCount}; ";
        }
        if ($flightSearchParams->infantCount > 0)
        {
            $traveller[] = array(
                'Type' => 'INF',
                'Count' => $flightSearchParams->infantCount
            );
            self::$lastRequestDescription .= "INF: {$flightSearchParams->infantCount}; ";
        }
        $params['Request']['SearchFlights']['Travellers']['Traveller'] = UtilsHelper::normalizeArray($traveller);
        unset($traveller);

        $soapResponse = self::request('Search', $params);
        //print_r($soapResponse);die();
        $errorDescription = '';

        if (!$soapResponse)
        {
            if (GDSNemoSoapClient::$lastCurlError)
            {
                $errorDescription = GDSNemoSoapClient::$lastCurlError;
                throw new CException($errorDescription);
            }
        }

        //processing response
        if (!isset($soapResponse->Response->SearchFlights->Flights->Flight))
        {
            $soapResponse = $this->humanReadable($soapResponse);
            if ($soapResponse)
            {
                Yii::trace(CVarDumper::dumpAsString($soapResponse), 'Gds.GdsNemoAgency.request');
                Yii::app()->RSentryException->logException(new CException('Incorrect soap response or no results for search request('.self::$lastRequestDescription.'): '.CVarDumper::dumpAsString($soapResponse)));
                //throw new CException('Incorrect soap response: '.CVarDumper::dumpAsString($soapResponse));
            }
        }

        $flights = array();
        $errorCode = 0;
        $searchId = '';
        if (isset($soapResponse->Response->SearchFlights->Flights))
        {
            $searchId = $soapResponse->Response->SearchFlights->Flights->SearchId;
        }
        if (isset($soapResponse->Response->SearchFlights->Flights->Flight))
        {
            Yii::beginProfile('processingSoap');
            UtilsHelper::soapObjectsArray($soapResponse->Response->SearchFlights->Flights->Flight);
            foreach ($soapResponse->Response->SearchFlights->Flights->Flight as $oSoapFlight)
            {
                $aParts = array();
                //flag of valid flight
                $needSave = true;
                $eTicket = true;
                //Yii::beginProfile('processingSegments');
                $supposedValAirlineCode = '';
                UtilsHelper::soapObjectsArray($oSoapFlight->Segments->Segment);
                foreach ($oSoapFlight->Segments->Segment as $arrKey => $oSegment)
                {
                    $oPart = new stdClass();
                    //Yii::beginProfile('loadAirportData');
                    if (!isset($oSegment->DepAirp))
                    {
                        Yii::log(print_r($oSegment, true) . '|||' . $arrKey, 'info');
                    }
                    try
                    {
                        $oPart->departure_airport = Airport::getAirportByCode(UtilsHelper::soapElementValue($oSegment->DepAirp));
                        $oPart->departure_city = $oPart->departure_airport->city;
                        $oPart->arrival_airport = Airport::getAirportByCode(UtilsHelper::soapElementValue($oSegment->ArrAirp));
                        $oPart->arrival_city = $oPart->arrival_airport->city;
                        $oPart->departure_terminal_code = isset($oSegment->DepTerminal) ? UtilsHelper::soapElementValue($oSegment->DepTerminal) : '';
                        $oPart->arrival_terminal_code = isset($oSegment->ArrTerminal) ? UtilsHelper::soapElementValue($oSegment->ArrTerminal) : '';
                        if (strlen($supposedValAirlineCode)==0)
                        {
                            $supposedValAirlineCode = $oSegment->MarkAirline;
                        }

                        $oPart->markAirline = Airline::getAirlineByCode($oSegment->MarkAirline);
                        if ($oSegment->OpAirline == '**')
                        {
                            $oPart->opAirline = $oPart->markAirline;
                            $oPart->transport_airline = $oPart->markAirline;
                        }
                        else
                        {
                            $oPart->opAirline = Airline::getAirlineByCode($oSegment->OpAirline);
                            $oPart->transport_airline = Airline::getAirlineByCode($oSegment->OpAirline);
                        }
                        $oPart->code = $oSegment->FlightNumber;
                        $oPart->duration = $oSegment->FlightTime * 60;
                        $oPart->datetime_begin = UtilsHelper::soapElementValue($oSegment->DepDateTime);
                        $oPart->datetime_end = UtilsHelper::soapElementValue($oSegment->ArrDateTime);
                        $oPart->stopNum = UtilsHelper::soapElementValue($oSegment->StopNum);
                        $oPart->aircraft_code = $oSegment->AircraftType;

                        $oPart->aTariffs = array();
                        $oPart->aTaxes = array();
                        $oPart->aBookingCodes = array();
                        UtilsHelper::soapObjectsArray($oSegment->BookingCodes->BookingCode);
                        foreach ($oSegment->BookingCodes->BookingCode as $sBookingCode)
                        {
                            $oPart->aBookingCodes[] = UtilsHelper::soapElementValue($sBookingCode);
                        }
                        $aParts[$oSegment->SegNum] = $oPart;
                        $eTicket = $eTicket && $oSegment->ETicket;
                    }
                    catch (Exception $e)
                    {
                        Yii::log('Error while parsing gds nemo results: '.$e->getMessage());
                        Yii::app()->RSentryException->logException($e);
                        $needSave = false;
                    }
                }
                if(!$needSave){
                    continue;
                }
                //Yii::endProfile('processingSegments');
                $full_sum = 0;
                $aPassengers = array();
                $aTariffs = array();
                //Yii::beginProfile('processingPassengers');
                $refundable = $oSoapFlight->PricingInfo->Refundable;
                if ($refundable == 'true' or $refundable === true)
                {
                    $refundable = true;
                }
                else
                {
                    $refundable = false;
                }
                UtilsHelper::soapObjectsArray($oSoapFlight->PricingInfo->PassengerFare);
                foreach ($oSoapFlight->PricingInfo->PassengerFare as $oFare)
                {
                    $sType = $oFare->Type;
                    $aPassengers[$sType]['count'] = $oFare->Quantity;
                    $aPassengers[$sType]['base_fare'] = $oFare->BaseFare->Amount;
                    if($oFare->BaseFare->Currency != 'RUB'){
                        $aPassengers[$sType]['base_fare'] = $oFare->EquiveFare->Amount;
                    }
                    $aPassengers[$sType]['total_fare'] = $oFare->TotalFare->Amount;
                    $full_sum += ($oFare->TotalFare->Amount * $oFare->Quantity);
                    if (isset($oFare->LastTicketDateTime))
                    {
                        $aPassengers[$sType]['LastTicketDateTime'] = $oFare->LastTicketDateTime;
                    }
                    else
                    {
                        Yii::log('!!DONT have LastTicketDate flight:' . $oSoapFlight->FlightId, 'info');
                    }
                    $aPassengers[$sType]['aTaxes'] = array();
                    if (isset($oFare->Taxes->Tax))
                    {
                        UtilsHelper::soapObjectsArray($oFare->Taxes->Tax);
                        foreach ($oFare->Taxes->Tax as $oTax)
                        {
                            if (isset($oTax->CurCode) == 'RUB')
                            {
                                $aPassengers[$sType]['aTaxes'][$oTax->TaxCode] = $oTax->Amount;
                            }
                            else
                            {
                                Yii::log(print_r($oTax, true) . '!!!' . $oSoapFlight->FlightId, 'info');
                                throw new CException(Yii::t('application', 'Valute code unexpected. Code: {code}. Expected RUB', array(
                                    '{code}' => $oTax->CurCode
                                )));
                            }
                        }
                    }
                    else
                    {
                        Yii::log('Flight dont have Taxes. FlightId:' . $oSoapFlight->FlightId, 'info');
                    }
                    //VarDumper::dump($oSoapFlight);die();
                    if (isset($oFare->Tariffs->Tariff))
                    {
                        UtilsHelper::soapObjectsArray($oFare->Tariffs->Tariff);
                        foreach ($oFare->Tariffs->Tariff as $oTariff)
                        {
                            //VarDumper::dump($oTariff);die();SEARCH_WITHOUT_ADULTS_BANNED
                            $aParts[$oTariff->SegNum]->aTariffs[$oTariff->Code] = $oTariff->Code;
                        }
                    }
                }
                //Yii::endProfile('processingPassengers');
                $aNewParts = array();
                //print_r($aParts);
                $oPart = reset($aParts);
                foreach ($flightSearchParams->routes as $route)
                {
                    $aSubParts = array();
                    $aCities = array();
                    while ($oPart)
                    {
                        $aSubParts[] = $oPart;
                        $aCities[] = $oPart->arrival_city->code;
                        if ($route->arrivalCity->code === $oPart->arrival_city->code)
                        {
                            $oPart = next($aParts);
                            break;
                        }
                        $oPart = next($aParts);
                    }
                    if (!$oPart)
                    {
                        $oPart = end($aParts);

                        if ($route->arrivalCity->code !== $oPart->arrival_city->code)
                        {
                            /*throw new CException(Yii::t('application', 'Not found segment with code arrival city {code}. Segment cityes: {codes}', array(
                                '{code}' => $route->arrivalCity->code,
                                '{codes}' => implode(', ', $aCities)
                            )));*/
                            Yii::log(Yii::t('application', 'Not found segment with code arrival city {code}. Segment cityes: {codes}. FlightId: {flightId}', array(
                                '{code}' => $route->arrivalCity->code,
                                '{codes}' => implode(', ', $aCities),
                                '{flightId}' => $oSoapFlight->FlightId
                            )), 'info', 'nemo');
                            Yii::app()->RSentryException->logException(new CException(Yii::t('application', 'Not found segment with code arrival city {code}. Segment cityes: {codes}. FlightId: {flightId}', array(
                                '{code}' => $route->arrivalCity->code,
                                '{codes}' => implode(', ', $aCities),
                                '{flightId}' => $oSoapFlight->FlightId
                            ))));
                            $needSave = false;
                        }
                    }
                    $aNewParts[] = $aSubParts;
                }

                $oFlight = new stdClass();
                $oFlight->full_sum = $full_sum;
                $oFlight->full_sum = UtilsHelper::soapElementValue($oSoapFlight->TotalPrice);
                $oFlight->commission_price = UtilsHelper::soapElementValue($oSoapFlight->Commission);
                $oFlight->charges = UtilsHelper::soapElementValue($oSoapFlight->Charges);
                $oFlight->webService = UtilsHelper::soapElementValue($oSoapFlight->WebService);
                if ((isset($oSoapFlight->ValCompany)) && (strlen($oSoapFlight->ValCompany)>0))
                {
                    $oFlight->valAirline = Airline::getAirlineByCode($oSoapFlight->ValCompany);
                }
                else
                {
                    $oFlight->valAirline = Airline::getAirlineByCode($supposedValAirlineCode);
                }
                $oFlight->flight_key = $oSoapFlight->FlightId;
                $oFlight->parts = $aNewParts;
                $oFlight->passengersInfo = $aPassengers;
                $oFlight->refundable = $refundable;
                //$oFlight->searchId = $searchId;

                if (!$eTicket)
                {
                    Yii::log(Yii::t('application', 'One or more segments dont have eticketing. FlightId:{flightId}', array('{flightId}' => $oSoapFlight->FlightId)));
                    $needSave = $needSave && $eTicket;
                }

                if ($needSave)
                {
                    $flights[] = $oFlight;
                }

            }
            Yii::endProfile('processingSoap');
        }
        else
        {
            $errorCode |= self::ERROR_CODE_EMPTY;
        }
        //print_r($aFlights);
        if ($errorCode > 0)
        {
            return array('flights' => array(), 'searchId' => '', 'errorCode' => $errorCode, 'errorDescription' => '');
        }
        else
        {
            return array('flights' => $flights, 'searchId' => $searchId, 'errorCode' => 0, 'errorDescription' => '');
        }
    }

    /**
     * @param FlightBookingParams $oFlightBookingParams
     * @return FlightBookingResponse
     * @throws CException
     */
    public function FlightBooking(FlightBookingParams $oFlightBookingParams)
    {
        if (!($oFlightBookingParams instanceof FlightBookingParams))
        {
            throw new CException(Yii::t('application', 'Parameter oFlightBookingParams not type of FlightBookingParams'));
        }

        $aParams = array(
            'Request' => array(
                'BookFlight' => array(
                    'FlightId' => $oFlightBookingParams->flightId,
                    'BookingCodes' => array(
                        'BookingCode' => array(
                            'Code' => 'Q',
                            'SegNumber' => '1'
                        )
                    ),
                    'Agency' => array(
                        'Name' => 'Easy',
                        'Telephone' => array(
                            'Type' => 'A',
                            'PhoneNumber' => '84995530933'
                        ),
                        'Address' => array(
                            'City' => 'Saint-Petersburg'
                        )
                    ),
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
                                    'LastName' => 'Kovalev'
                                ),
                                'DocumentInfo' => array(
                                    'DocType' => 'М',
                                    'DocNum' => 'asdawe131',
                                    'CountryCode' => 'RU',
                                    'DocElapsedTime' => '24.03.2026'
                                )
                            ),
                            array(
                                'Type' => 'ADT',
                                'Count' => '1'
                            )
                        )
                    )
                )
            ),
            'Source' => array(
                'ClientId' => Yii::app()->params['GDSNemo']['agencyId'],
                'APIKey' => Yii::app()->params['GDSNemo']['agencyApiKey'],
                'Language' => 'RU',
                'Currency' => 'RUB'
            )
        );
        if ($oFlightBookingParams->checkValid())
        {
            $aTraveler = array();
            $iNum = 1;
            $infLinkedToNum = 1;
            foreach ($oFlightBookingParams->passengers as $passenger)
            {
                $oTraveller['Type'] = Yii::app()->params['aPassegerTypes'][$passenger->type];
                $oTraveller['Num'] = $iNum;
                if($oTraveller['Type'] == 'INF'){
                    $oTraveller['LinkedTo'] = $infLinkedToNum;
                    $infLinkedToNum++;
                }
                $oTraveller['IsContact'] = $iNum == 1 ? true : false;
                $oTraveller['PersonalInfo'] = array();
                $oTraveller['PersonalInfo']['DateOfBirth'] = $passenger->passport->birthday;
                $oTraveller['PersonalInfo']['Nationality'] = Country::getCountryByPk($passenger->passport->countryId)->code;
                $oTraveller['PersonalInfo']['Gender'] = $passenger->passport->genderId == 1 ? 'M' : 'F';
                $oTraveller['PersonalInfo']['FirstName'] = $passenger->passport->firstName;
                $oTraveller['PersonalInfo']['LastName'] = $passenger->passport->lastName;
                $oTraveller['DocumentInfo'] = array();
                $oTraveller['DocumentInfo']['DocType'] = isset(self::$passportTypesMap[$passenger->passport->documentTypeId]) ? self::$passportTypesMap[$passenger->passport->documentTypeId] : 'P';
                $oTraveller['DocumentInfo']['DocNum'] = ($passenger->passport->series ? $passenger->passport->series : '') . $passenger->passport->number;
                $oTraveller['DocumentInfo']['CountryCode'] = Country::getCountryByPk($passenger->passport->countryId)->code;
                $oTraveller['DocumentInfo']['DocElapsedTime'] = UtilsHelper::dateToPointDate($passenger->passport->expiration);
                $oTraveller['ContactInfo'] = array();
                $oTraveller['ContactInfo']['EmailID'] = $oFlightBookingParams->contactEmail;
                $oTraveller['ContactInfo']['Telephone'] = array();
                $oTraveller['ContactInfo']['Telephone']['Type'] = 'M';
                $oTraveller['ContactInfo']['Telephone']['PhoneNumber'] = $oFlightBookingParams->phoneNumber;

                $aTraveler[] = $oTraveller;
                $iNum++;
            }
            $aParams['Request']['BookFlight']['Travellers']['Traveller'] = $aTraveler;
            $aParams['Request']['BookFlight']['Travellers']['Traveller'] = UtilsHelper::normalizeArray($aParams['Request']['BookFlight']['Travellers']['Traveller']);
        }
        else
        {
            throw new CException(Yii::t('application', 'Data in parameter oFlightBookingParams not valid'));
        }

        $response = self::request('BookFlight', $aParams);

        $flightBookingResponse = new FlightBookingResponse();
        if (isset($response->Response->Error))
        {
            $status = 'error';
            $flightBookingResponse->status = 2;
            $flightBookingResponse->responseStatus = ResponseStatus::ERROR_CODE_EXTERNAL;
            $flightBookingResponse->addError('error', $response->Response->Error);
        }
        else
        {
            if (isset($response->Response->BookFlight->Status))
            {
                $status = $response->Response->BookFlight->Status;
            }
            elseif (isset($response->Error->_))
            {
                $status = 'error';
                $flightBookingResponse->addError($response->Error->Code, $response->Error->_);
            }
            else
            {
                $status = 'error';
                if (GDSNemoSoapClient::$lastCurlError)
                {
                    $flightBookingResponse->addError('connection error', GDSNemoSoapClient::$lastCurlError);
                }
            }
        }

        if ($status == 'booked')
        {

            $flightBookingResponse->pnr = $response->Response->BookFlight->Code;
            $flightBookingResponse->expiration = false;
            UtilsHelper::soapObjectsArray($response->Response->BookFlight->Flight->PricingInfo->PassengerFare);
            foreach ($response->Response->BookFlight->Flight->PricingInfo->PassengerFare as $oFare)
            {
                if(!$flightBookingResponse->expiration){
                    $flightBookingResponse->expiration = strtotime($oFare->LastTicketDateTime->_);
                }
                if($flightBookingResponse->expiration < strtotime($oFare->LastTicketDateTime->_)){
                    $flightBookingResponse->expiration = strtotime($oFare->LastTicketDateTime->_);
                }
            }
            $flightBookingResponse->nemoBookId = $response->Response->BookFlight->ID;
            $flightBookingResponse->status = 1;
        }
        else
        {
            $flightBookingResponse->status = 2;
            $flightBookingResponse->responseStatus = ResponseStatus::ERROR_CODE_EXTERNAL;
            $flightBookingResponse->addError('error', 'Status is:' . $status);
        }
        return $flightBookingResponse;

    }

    public function FlightTariffRules($flightId)
    {
        $aParams = array(
            'Request' => array(
                'GetAirRules' => array(
                    'FlightId' => $flightId
                ),
            ),
            'Source' => array(
                'ClientId' => Yii::app()->params['GDSNemo']['agencyId'],
                'APIKey' => Yii::app()->params['GDSNemo']['agencyApiKey'],
                'Language' => 'RU',
                'Currency' => 'RUB'
            )
        );

        $response = self::request('GetAirRules', $aParams);

        $result = array();
        if (isset($response->Response->GetAirRules->Rules->Rule))
        {
            UtilsHelper::soapObjectsArray($response->Response->GetAirRules->Rules->Rule);
            foreach($response->Response->GetAirRules->Rules->Rule as $ruleInfo){
                $ruleLine = array(
                    'code'=>$ruleInfo->Code,
                    'name'=>$ruleInfo->Name,
                    'tariff'=>$ruleInfo->Tarrif,
                    'content'=>$ruleInfo->RuleText
                );
                $result[$ruleLine['code']] = $ruleLine;
            }
        }
        return $result;
    }

    public function humanReadable($soapResponse)
    {
        if (isset($soapResponse->Response->SearchFlights->Flights->ResultURL))
        {
            $soapResponse->Response->SearchFlights->Flights->ResultURL = urldecode($soapResponse->Response->SearchFlights->Flights->ResultURL);
        }
        return $soapResponse;
    }

    /**
     * Function return true if Flight with $flightId is available or false if unavailable. Null if undefined.
     * что возвращать когда результат неизвестен? null?
     * @return boolean
     *
     */
    public function checkFlight($flightId)
    {
        if ($flightId)
        {
            $aParams = array(
                'Request' => array(
                    'AirAvail' => array(
                        'FlightId' => $flightId
                    ),

                ),
                'Source' => array(
                    'ClientId' => Yii::app()->params['GDSNemo']['agencyId'],
                    'APIKey' => Yii::app()->params['GDSNemo']['agencyApiKey'],
                    'Language' => 'RU',
                    'Currency' => 'RUB'
                )
            );

            $response = self::request('AirAvail', $aParams);

            if (isset($response->Response->AirAvail->IsAvail))
            {
                return $response->Response->AirAvail->IsAvail;
            }
            else
            {
                return null;
            }

        }
        else return null;
    }

    public function FlightTicketing(FlightTicketingParams $flightTicketingRequest)
    {
        $aParams = array(
            'Request' => array(
                'Ticketing' => array(
                    'BookID' => $flightTicketingRequest->nemoBookId,
                    //'ValCompany' => '',
                    'Commision' => array(
                        'Percent' => '2'
                    )
                )
            ),
            'Source' => array(
                'ClientId' => Yii::app()->params['GDSNemo']['agencyId'],
                'APIKey' => Yii::app()->params['GDSNemo']['agencyApiKey'],
                'Language' => 'RU',
                'Currency' => 'RUB'
            )
        );

        $response = self::request('Ticketing', $aParams, $bCache = FALSE, $iExpiration = 0);
        $flightTicketingResponse = new FlightTicketingResponse();

        if (isset($response->Response->Error))
        {
            $status = 'error';
            $flightTicketingResponse->status = 2;
            $flightTicketingResponse->responseStatus = ResponseStatus::ERROR_CODE_EXTERNAL;
            $flightTicketingResponse->addError('error', $response->Response->Error);
        }
        else
        {
            if (isset($response->Response->Ticketing->Status))
            {
                $status = $response->Response->Ticketing->Status;
            }
            elseif (isset($response->Error->_))
            {
                $status = 'error';
                $flightTicketingResponse->addError($response->Error->Code, $response->Error->_);
            }
            else
            {
                $status = 'error';
                if (GDSNemoSoapClient::$lastCurlError)
                {
                    $flightTicketingResponse->addError('connection error', GDSNemoSoapClient::$lastCurlError);
                }
            }
        }

        if ($status == 'ticket')
        {
            $flightTicketingResponse->status = 1;
            $aParts = array();
            UtilsHelper::soapObjectsArray($response->Response->Ticketing->Flight->Segments->Segment);
            foreach ($response->Response->Ticketing->Flight->Segments->Segment as $arrKey => $oSegment)
            {
                $oPart = new stdClass();
                //Yii::beginProfile('loadAirportData');
                if (!isset($oSegment->DepAirp))
                {
                    Yii::log(print_r($oSegment, true) . '|||' . $arrKey, 'info');
                }
                try
                {
                    $oPart->departure_airport = Airport::getAirportByCode(UtilsHelper::soapElementValue($oSegment->DepAirp));
                    $oPart->departure_city = $oPart->departure_airport->city;
                    $oPart->arrival_airport = Airport::getAirportByCode(UtilsHelper::soapElementValue($oSegment->ArrAirp));
                    $oPart->arrival_city = $oPart->arrival_airport->city;
                    $oPart->departure_terminal_code = isset($oSegment->DepTerminal) ? UtilsHelper::soapElementValue($oSegment->DepTerminal) : '';
                    $oPart->arrival_terminal_code = isset($oSegment->ArrTerminal) ? UtilsHelper::soapElementValue($oSegment->ArrTerminal) : '';

                    $oPart->markAirline = Airline::getAirlineByCode($oSegment->MarkAirline);
                    if ($oSegment->OpAirline == '**')
                    {
                        $oPart->opAirline = $oPart->markAirline;
                        $oPart->transport_airline = $oPart->markAirline;
                    }
                    else
                    {
                        $oPart->opAirline = Airline::getAirlineByCode($oSegment->OpAirline);
                        $oPart->transport_airline = Airline::getAirlineByCode($oSegment->OpAirline);
                    }
                    $oPart->code = $oSegment->FlightNumber;
                    $oPart->duration = $oSegment->FlightTime * 60;
                    $oPart->datetime_begin = UtilsHelper::soapElementValue($oSegment->DepDateTime);
                    $oPart->datetime_end = UtilsHelper::soapElementValue($oSegment->ArrDateTime);
                    $oPart->stopNum = UtilsHelper::soapElementValue($oSegment->StopNum);
                    $oPart->aircraft_code = $oSegment->AircraftType;

                    $oPart->aTariffs = array();
                    $oPart->aTaxes = array();
                    $oPart->aBookingCodes = array();
                    UtilsHelper::soapObjectsArray($oSegment->BookingCodes->BookingCode);
                    foreach ($oSegment->BookingCodes->BookingCode as $sBookingCode)
                    {
                        $oPart->aBookingCodes[] = UtilsHelper::soapElementValue($sBookingCode);
                    }
                    $aParts[$oSegment->SegNum] = $oPart;
                }catch (CException $e){
                    Yii::app()->RSentryException->logException($e);
                }
            }
            if($aParts){
                $flightTicketingResponse->aParts = $aParts;
            }

            UtilsHelper::soapObjectsArray($response->Response->Ticketing->Travellers->Traveller);
            foreach ($response->Response->Ticketing->Travellers->Traveller as $traveller)
            {
                $ticket = array('ticketNumber' => $traveller->Ticket->TickectNum, 'documentNumber' => $traveller->DocumentInfo->DocNum);

                $flightTicketingResponse->tickets[] = $ticket;
            }
        }
        else
        {
            $flightTicketingResponse->responseStatus = ResponseStatus::ERROR_CODE_EXTERNAL;
            $flightTicketingResponse->addError('error', 'Status is:' . $status);
            $flightTicketingResponse->status = 2;
        }
        return $flightTicketingResponse;

    }

    public function FlightVoid()
    {
        $aParams = array(
            'Request' => array(
                'VoidTicket' => array(
                    'BookID' => 534733
                ),
                'Source' => array(
                    'ClientId' => 102,
                    'APIKey' => '7F48365D42B73307C99C12A578E92B36',
                    'Language' => 'UA',
                    'Currency' => 'RUB'
                )
            )
        );

        print_r(self::request('bookFlight', $aParams));
    }

    public function CancelBooking($bookId)
    {
        if ($bookId)
        {
            $aParams = array(
                'Request' => array(
                    'CancelBook' => array(
                        'BookID' => $bookId
                    ),

                ),
                'Source' => array(
                    'ClientId' => Yii::app()->params['GDSNemo']['agencyId'],
                    'APIKey' => Yii::app()->params['GDSNemo']['agencyApiKey'],
                    'Language' => 'RU',
                    'Currency' => 'RUB'
                )
            );

            $response = self::request('CancelBook', $aParams);
            if (isset($response->Response->CancelBook->Result->Success))
            {
                if (($response->Response->CancelBook->Result->Success === 'true') || ($response->Response->CancelBook->Result->Success === true))
                {
                    $result = true;
                }
                else
                {
                    $result = false;
                }
                return $result;
            }
            elseif (isset($response['errorDescription']))
            {
                if (strpos($response['errorDescription'], 'Already Cancelled') !== false)
                {
                    $result = true;
                }
                else
                {
                    $result = false;
                }
                return $result;
            }
            else
            {
                return null;
            }

        }
        else return null;
    }
}
