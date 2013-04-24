<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 07.08.12
 * Time: 12:17
 */

require_once('common/config/partner_directions_to_skip.php');

class SearchController extends ApiController
{
    public $defaultAction = 'default';
    private $results;


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
    public function actionBE(array $destinations, $adt = 1, $chd = 0, $inf = 0, $format='json', $withFirst=false)
    {
        if (!$this->filter($destinations))
        {
            $variants = array();
        }
        else
        {
            $asyncExecutor = new AsyncCurl();
            $this->addBusinessClassAsyncResponse($destinations, $adt, $chd, $inf, $asyncExecutor);
            $this->addEconomClassAsyncResponse($destinations, $adt, $chd, $inf, $asyncExecutor);
            if ($withFirst)
                $this->addFirstClassAsyncResponse($destinations, $adt, $chd, $inf, $asyncExecutor);
            $responses = $asyncExecutor->send();
            $errors = array();
            $variants = array();
            foreach ($responses as $i=>$response)
            {
                if ($httpCode=$response->headers['http_code'] == 200)
                {
                    $combined = json_decode($response->body);
                    if ((isset($combined->flights)) and (is_iterable($combined->flights)))
                    {
                        $flights = $combined->flights;
                    }
                    else
                    {
                        $newException = new Exception("Error: combined->flights is not iterable. Response: ".CVarDumper::dumpAsString($response));
                        Yii::app()->RSentryException->logException($newException);
                        $flights = array();
                    }
                    $searchParams = $combined->searchParams;
                    $variants = CMap::mergeArray($variants, FlightManager::injectForBe($flights, $searchParams));
                }
                else
                {
                    $errors[] = 'Error '.$httpCode;
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
        $siblingsEconom = FlightManager::createSiblingsData($flightSearchParams);
        $result['siblings']['E'] = $siblingsEconom;
        $this->sendWithCorrectFormat($format, $result);
    }

    public function actionBEF(array $destinations, $adt = 1, $chd = 0, $inf = 0, $format='json')
    {
        $this->actionBE($destinations, $adt, $chd, $inf, $format, true);
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
        $businessUrl = Yii::app()->params['app.api.flightSearchNoSecure'].'/search/withParams';
        $query = http_build_query(array(
            'destinations' => $destinations,
            'adt' => $adt,
            'chd' => $chd,
            'inf' => $inf,
            'serviceClass' => 'B',
            'pid' => Partner::getCurrentPartnerKey()
        ));
        $businessUrl = $businessUrl . '?' . $query;
        $asyncExecutor->add($businessUrl);
    }

    private function addEconomClassAsyncResponse($destinations, $adt, $chd, $inf, $asyncExecutor)
    {
        $businessUrl = Yii::app()->params['app.api.flightSearchNoSecure'].'/search/withParams';
        $query = http_build_query(array(
            'destinations' => $destinations,
            'adt' => $adt,
            'chd' => $chd,
            'inf' => $inf,
            'serviceClass' => 'E',
            'pid' => Partner::getCurrentPartnerKey()
        ));
        $businessUrl = $businessUrl . '?' . $query;
        $asyncExecutor->add($businessUrl);
    }

    private function addFirstClassAsyncResponse($destinations, $adt, $chd, $inf, $asyncExecutor)
    {
        $firstUrl = Yii::app()->params['app.api.flightSearchNoSecure'].'/search/withParams';
        $query = http_build_query(array(
            'destinations' => $destinations,
            'adt' => $adt,
            'chd' => $chd,
            'inf' => $inf,
            'serviceClass' => 'F',
            'pid' => Partner::getCurrentPartnerKey()
        ));
        $firstUrl = $firstUrl . '?' . $query;
        $asyncExecutor->add($firstUrl);
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
        Yii::app()->user->setState('directRequest', true);
        $flightSearchParams = $this->buildSearchParams($destinations, $adt, $chd, $inf, $serviceClass);
        $results['flightVoyages'] = $this->doFlightSearch($flightSearchParams);
        $this->sendWithCorrectFormat($format, $results);
    }

    public function actionWithParams(array $destinations, $adt = 1, $chd = 0, $inf = 0, $serviceClass = 'A', $format='json')
    {
        Yii::app()->user->setState('directRequest', true);

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
            try{
                $departureCity = City::model()->getCityByCode($route['departure']);
            }catch (CException $e){
                $departureCity = false;
            }

            if (!$departureCity){
                City::saveAllNotFoundCodes();
                $this->sendError(400, 'Incorrect IATA code for departure city');
            }
            try{
                $arrivalCity = City::model()->getCityByCode($route['arrival']);
            }catch (CException $e){
                $arrivalCity = false;
            }
            if (!$arrivalCity){
                City::saveAllNotFoundCodes();
                $this->sendError(400, 'Incorrect IATA code for arrival city');
            }
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
        $cacheId = 'partner-'.md5(md5(serialize($flightSearchParams)).$partner->id);
        return $cacheId;
    }

    public function actionPartner($from, $to, $date1, $adults, $children, $infants, $cabin, $partner, $password, $date2='')
    {
        $this->checkCredentials($partner, $password);
        $destinations = array();
        $from = $this->normalizeIataCodeToCityCode($from);
        $to = $this->normalizeIataCodeToCityCode($to);
        City::saveAllNotFoundCodes();

        if($this->shouldSkip($from, $to)) {
            $results = Array();
            $prepared = $this->prepareForAviasales($results, $cabin);
            $this->data = $prepared;
            $this->_sendResponse(true, 'application/xml');
            return;
        }

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
        $partnerCacheId = $this->getPartnerCacheId($flightSearchParams);
        $prepared = Yii::app()->pCache->get($partnerCacheId);
        if (!$prepared)
        {
            $results = $this->doFlightSearch($flightSearchParams);
            $prepared = $this->prepareForAviasales($results, $cabin);
            Yii::app()->pCache->set($partnerCacheId, $prepared, appParams('flight_search_cache_time_partner'));
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

    private function normalizeIataCodeToCityCode($code)
    {
        $city = City::model()->findByAttributes(array('code'=>$code));
        $ret = false;
        try{
            if (!$city)
            {
                $airport = Airport::getAirportByCode($code);
                if($airport){
                    $city = City::getCityByPk($airport->cityId);
                }
            }
            if($city){
                $ret = $city->code;
            }
        }catch (CException $e){

        }
        return $ret;
    }

    private function prepareForAviasales(&$results, $cabin)
    {
        $prepared = array();
        $i = 0;
        foreach ($results as $variant)
        {
            $query = 'item[0][module]=Avia&item[0][type]=avia&item[0][searchId]='.$variant['cacheId'].'&item[0][searchKey]='.$variant['flightKey'].'&pid='.Partner::getCurrentPartnerKey().'&dir=1';
            $url = Yii::app()->params['baseUrl'].'/buy?'.$query;
            $prepared[$i] = array(
                'price' => $variant['price'],
                'currency' => 'rub',
                'url' => $url,
                'validatingCarrier' => $variant['valCompany'],
            );

            foreach ($variant['flights'] as $u=>$flight)
            {
                foreach ($flight['flightParts'] as $j=>$flightPart)
                {
                    $departureAirport = Airport::getAirportByPk($flightPart['departureAirportId']);
                    $departureDate = strtotime($flightPart['datetimeBegin']);
                    $arrivalAirport = Airport::getAirportByPk($flightPart['arrivalAirportId']);
                    $arrivalDate = strtotime($flightPart['datetimeEnd']);
                    $marketingCarrier = $flightPart['markAirline'];
                    $transportAirline = $flightPart['transportAirline'];
                    $prepared[$i]['segment'.$u]['flight'.$j] = array(
                        'operatingCarrier' => $transportAirline,
                        'number' => $flightPart['flightCode'],
                        'departure' => $departureAirport->code,
                        'departureDate' => date('Y-m-d', $departureDate),
                        'departureTime' => date('H:i', $departureDate),
                        'arrival' => $arrivalAirport->code,
                        'arrivalDate' => date('Y-m-d', $arrivalDate),
                        'arrivalTime' => date('H:i', $arrivalDate),
                        'equipment' => $flightPart['aircraftCode'],
                        'cabin' => $cabin
                    );
                    if ($marketingCarrier != $transportAirline)
                    {
                        $prepared[$i]['segment'.$u]['flight'.$j]['marketingCarrier'] = $marketingCarrier;
                    }
                    $j++;
                }
            }
            $i++;
        }
        $xml = new ArrayToXml('variants');
        $prepared = $xml->toXml($prepared);
        $prepared = preg_replace('/segment\d+/', 'segment', $prepared);
        $prepared = preg_replace('/flight\d+/', 'flight', $prepared);
        return $prepared;
    }

    /* Следует ли нам пропустить поиск
       и отдать нет результатов для партнера
     */
    private function shouldSkip($from, $to) {
        global $PARTNER_DIRECTIONS_TO_SKIP;
        $parts = array($from, $to);
        sort($parts);
        $key = implode("", $parts);

        if(in_array($key,$PARTNER_DIRECTIONS_TO_SKIP))
            return true;
        return false;

    }
}