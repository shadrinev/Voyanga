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
    private $results;

    private $logId;

    private function log()
    {
        if (!$this->logId)
            $this->logId = 'req'.rand(1, 10000);
        $args = func_get_args();
        $other = array_slice($args, 1);
        $time = date('Y-m-d H:i:s', time());
        Yii::log($time."\n".$args[0] ."\n".CVarDumper::dumpAsString($other), CLogger::LEVEL_INFO, 'application.api.'.$this->logId);
    }

    /**
     * @param array $destinations
     *  [Х][departure] - departure city iata code,
     *  [Х][arrival] - arrival city iata code,
     *  [Х][date] - departure date,
     *
     * @param int $adt amount of adults
     * @param int $chd amount of childs
     * @param int $inf amount of infanties
     */
    public function actionBE(array $destinations, $adt = 1, $chd = 0, $inf = 0, $format='json')
    {
        $this->log('Search for business and econom', $destinations, $adt, $chd, $inf, $format);
        if (!$this->filter($destinations))
        {
            $this->log('Current request doesn\'t pass filter and we close it');
            $variants = array();
        }
        else
        {
            $this->log('Dividing request to two branches');
            $asyncExecutor = new AsyncCurl();
            $this->addBusinessClassAsyncResponse($destinations, $adt, $chd, $inf, $asyncExecutor);
            $this->addEconomClassAsyncResponse($destinations, $adt, $chd, $inf, $asyncExecutor);
            $this->log('Sending requests');
            $responses = $asyncExecutor->send();
            $errors = array();
            $variants = array();
            $this->log('We got response');
            foreach ($responses as $i=>$response)
            {
                $this->log("For $i response we got headers:", $response->headers);
                if ($httpCode=$response->headers['http_code'] == 200)
                {
                    $combined = json_decode($response->body);
                    $this->log("Decoding response. Length of body response:", strlen($response->body));
                    if ((isset($combined->flights)) and (is_iterable($combined->flights)))
                    {
                        $flights = $combined->flights;
                        $this->log("Ok. We got flights. Amount:", sizeof($flights));
                    }
                    else
                    {
                        $newException = new Exception("Error: combined->flights is not iterable. Response: ".CVarDumper::dumpAsString($response));
                        Yii::app()->RSentryException->logException($newException);
                        $flights = array();
                    }
                    $searchParams = $combined->searchParams;
                    $this->log("Adding specific params for b/e to response");
                    $variants = CMap::mergeArray($variants, FlightManager::injectForBe($flights, $searchParams));
                }
                else
                {
                    $errors[] = 'Error '.$httpCode;
                    $this->log("Response ended without 200 http response. Code:", $httpCode);
                }
            }
            if (!empty($this->errors))
            {
                $variants = array();
            }
        }
        $flightSearchParams = $this->buildSearchParams($destinations, $adt, $chd, $inf, 'A');
        $this->results = $variants;
        $result['flights']['flightVoyages'] = $this->results;
        $result['searchParams'] = $flightSearchParams->getJsonObject();
        $this->log("We added search params");
        $siblingsEconom = FlightManager::createSiblingsData($flightSearchParams);
        $this->log("We added siblings params");
        $result['siblings']['E'] = $siblingsEconom;
        $this->log("We are sending to output with format", $format);
        $this->sendWithCorrectFormat($format, $result);
    }

    private function filter($destinations)
    {
        if (sizeof($destinations)==2)
        {
            if (($destinations[0]['departure'] == $destinations[0]['arrival']) ||
                ($destinations[1]['departure'] == $destinations[1]['arrival']))
                return false;
        }
        return true;
    }

    private function addBusinessClassAsyncResponse($destinations, $adt, $chd, $inf, $asyncExecutor)
    {
        $this->log('add Business Class Async Response');
        $businessUrl = Yii::app()->params['app.api.flightSearchNoSecure'].'/search/withParams';
        $query = http_build_query(array(
            'destinations' => $destinations,
            'adt' => $adt,
            'chd' => $chd,
            'inf' => $inf,
            'serviceClass' => 'B',
        ));
        $businessUrl = $businessUrl . '?' . $query;
        $asyncExecutor->add($businessUrl);
    }

    private function addEconomClassAsyncResponse($destinations, $adt, $chd, $inf, $asyncExecutor)
    {
        $this->log('add Econom Class Async Response');
        $businessUrl = Yii::app()->params['app.api.flightSearchNoSecure'].'/search/withParams';
        $query = http_build_query(array(
            'destinations' => $destinations,
            'adt' => $adt,
            'chd' => $chd,
            'inf' => $inf,
            'serviceClass' => 'E',
        ));
        $businessUrl = $businessUrl . '?' . $query;
        //echo "send req:".$businessUrl;
        $asyncExecutor->add($businessUrl);
    }

    /**
     * @param array $destinations
     *  [Х][departure] - departure city iata code,
     *  [Х][arrival] - arrival city iata code,
     *  [Х][date] - departure date,
     *
     * @param int $adt amount of adults
     * @param int $chd amount of childs
     * @param int $inf amount of infanties
     * @param string $serviceClass (A = all | E = economy | B = business)
     */
    public function actionDefault(array $destinations, $adt = 1, $chd = 0, $inf = 0, $serviceClass = 'A', $format='json')
    {
        $flightSearchParams = $this->buildSearchParams($destinations, $adt, $chd, $inf, $serviceClass);
        $results['flightVoyages'] = $this->doFlightSearch($flightSearchParams);
        $this->sendWithCorrectFormat($format, $results);
    }

    public function actionWithParams(array $destinations, $adt = 1, $chd = 0, $inf = 0, $serviceClass = 'A', $format='json')
    {
        $flightSearchParams = $this->buildSearchParams($destinations, $adt, $chd, $inf, $serviceClass);
        $results = array(
            'flights' => $this->doFlightSearch($flightSearchParams),
            'searchParams' => $flightSearchParams->getJsonObject()
        );
        $this->sendWithCorrectFormat($format, $results);
    }

    public function actionTariffRules($flightIds,$format='json'){
        $flightIds = explode(':',$flightIds);
        $results = array();
        foreach($flightIds as $flightId)
        {
            $results[$flightId] = Yii::app()->gdsAdapter->flightTariffRules($flightId);
        }
        $this->sendWithCorrectFormat($format, $results);

    }

    private function inject($flights, $cacheId)
    {
        $newFlights = array();
        $searchId = $flights['searchId'];
        $flightVoyages = $flights['flightVoyages'];
        foreach ($flightVoyages as $key => $flight)
        {
            $newFlight = $flight;
            $newFlight['searchId'] = $searchId;
            $newFlight['cacheId'] = $cacheId;
            if (Partner::getCurrentPartner())
            {
                $query = 'item[0][module]=Avia&item[0][type]=avia&item[0][searchId]='.$cacheId.'&item[0][searchKey]='.$flight['flightKey'].'&pid='.Partner::getCurrentPartnerKey();
                $newFlight['url'] = Yii::app()->params['baseUrl'].'/buy?'.$query;
            }
            $newFlights[] = $newFlight;
        }
        return $newFlights;
    }

    private function doFlightSearch($flightSearchParams)
    {
        $fs = new FlightSearch();
        $this->results = $fs->sendRequest($flightSearchParams, false);
        $cacheId = $this->storeToCache($flightSearchParams);
        $results = $this->inject($this->results->getJsonObject(), $cacheId);
        return $results;
    }

    private function buildSearchParams($destinations, $adt, $chd, $inf, $service_class)
    {
        $flightSearchParams = new FlightSearchParams();
        foreach ($destinations as $route)
        {
            $departureDate = date('d.m.Y', strtotime($route['date']));
            $departureCity = City::model()->getCityByCode($route['departure']);
            if (!$departureCity)
                $this->sendError(400, 'Incorrect IATA code for deparure city');
            $arrivalCity = City::model()->getCityByCode($route['arrival']);
            if (!$arrivalCity)
                $this->sendError(400, 'Incorrect IATA code for arrival city');
            $flightSearchParams->addRoute(array(
                'adult_count' => $adt,
                'child_count' => $chd,
                'infant_count' => $inf,
                'departure_city_id' => $departureCity->id,
                'arrival_city_id' => $arrivalCity->id,
                'departure_date' => $departureDate,
            ));
            $flightSearchParams->flight_class = $service_class;
        }
        return $flightSearchParams;
    }

    private function sendWithCorrectFormat($format, $results)
    {
        if ($format == 'json')
            $this->sendJson($results);
        elseif ($format == 'xml')
            $this->sendXml($results, 'aviaSearchResults');
        else
        {
            $this->sendError(400, 'Incorrect response format');
            Yii::app()->end();
        }
    }

    private function storeToCache($flightSearchParams)
    {
        $cacheId = md5(md5(serialize($flightSearchParams)).microtime().rand(1000,9999));
        Yii::app()->pCache->set('flightSearchResult' . $cacheId, $this->results, appParams('flight_search_cache_time'));
        Yii::app()->pCache->set('flightSearchParams' . $cacheId, $flightSearchParams, appParams('flight_search_cache_time'));
        return $cacheId;
    }

    private function getPartnerCacheId($flightSearchParams)
    {
        $partner = Partner::getCurrentPartner();
        $cacheId = md5(md5(serialize($flightSearchParams)).microtime().rand(1000,9999).$partner->id);
        return $cacheId;
    }

    public function actionPartner($from, $to, $date1, $adults, $children, $infants, $cabin, $partner, $password, $date2='')
    {
        $this->checkCredentials($partner, $password);
        $destinations = array();
        $destinations[] = array(
            'departure' => $from,
            'arrival' => $to,
            'date' => date('d.m.Y', strtotime($date1))
        );
        if (strlen($date2)>0)
        {
            $destinations[] = array(
                'departure' => $to,
                'arrival' => $from,
                'date' => date('d.m.Y', strtotime($date2))
            );
        }
        $serviceClass = strtr($cabin, array('Y' => 'E', 'C' => 'B'));
        $flightSearchParams = $this->buildSearchParams($destinations, $adults, $children, $infants, $serviceClass);
        $cacheId = $this->storeToCache($flightSearchParams);
        $partnerCacheId = $this->getPartnerCacheId($flightSearchParams);
        $prepared = Yii::app()->pCache->get($partnerCacheId);
        if (!$prepared)
        {
            $results = $this->doFlightSearch($flightSearchParams);
            $prepared = $this->prepareForAviasales($results, $cabin, $cacheId);
            Yii::app()->pCache->get($partnerCacheId, $prepared, appParams('flight_search_cache_time_parner'));
        }
        $this->data = $prepared;
        $this->_sendResponse(true, 'application/xml');
    }

    private function checkCredentials($u, $p)
    {
        $partner = Partner::model()->findByAttributes(array('name'=>$u));
        if (($partner) && ($partner->verifyPassword($p)))
        {
            Partner::setPartnerByName($u);
            return;
        }
        $this->sendError(403, 'Permission denied');
        Yii::app()->end();
    }

    private function prepareForAviasales(&$results, $cabin, $cacheId)
    {
        $prepared = array();
        $i = 0;
        foreach ($results as $variant)
        {
            $query = 'item[0][module]=Avia&item[0][type]=avia&item[0][searchId]='.$cacheId.'&item[0][searchKey]='.$variant['flightKey'].'&pid='.Yii::app()->params['aviasales.partnerId'];
            $url = Yii::app()->params['baseUrl'].'/buy?'.$query;
            $prepared[$i] = array(
                'price' => $variant['price'],
                'currency' => 'rub',
                'url' => $url,
                'validatingCarrier' => $variant['valCompany'],
                'segment' => array()
            );
            $j = 0;
            foreach ($variant['flights'] as $flight)
            {
                $prepared[$i]['segment']['flight'.$j] = array();
                foreach ($flight['flightParts'] as $flightPart)
                {
                    $departureCity = City::getCityByPk($flightPart['departureCityId']);
                    $departureDate = strtotime($flightPart['datetimeBegin']);
                    $arrivalCity = City::getCityByPk($flightPart['arrivalCityId']);
                    $arrivalDate = strtotime($flightPart['datetimeEnd']);
                    $prepared[$i]['segment']['flight'.$j] = array(
                        'operatingCarrier' => $flightPart['transportAirline'],
                        'number' => $flightPart['flightCode'],
                        'departure' => $departureCity->code,
                        'departureDate' => date('Y-m-d', $departureDate),
                        'departureTime' => date('H:i', $departureDate),
                        'arrival' => $arrivalCity->code,
                        'arrivalDate' => date('Y-m-d', $arrivalDate),
                        'arrivalTime' => date('H:i', $arrivalDate),
                        'equipment' => $flightPart['aircraftCode'],
                        'cabin' => $cabin
                    );
                    $j++;
                }
            }
            $i++;
        }
        $xml = new ArrayToXml('variants');
        $prepared = $xml->toXml($prepared);
        $prepared = str_replace('flight0>', 'flight>', $prepared);
        $prepared = str_replace('flight1>', 'flight>', $prepared);
        return $prepared;
    }
}