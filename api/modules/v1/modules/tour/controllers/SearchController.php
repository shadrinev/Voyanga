<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 24.08.12
 * Time: 13:12
 */
class SearchController extends ApiController
{
    public $defaultAction = 'default';

    private $variants;
    private $errors;

    /**
     * @param array $destinations
     *  [Х][city] - city iata code,
     *  [Х][dateFrom] - start of visiting date,
     *  [Х][dateTo] - end of visiting date,
     *
     * @param int $adt amount of adults
     * @param int $chd amount of childs
     * @param int $inf amount of infanties
     * @param string $serviceClass (A = all | E = economy | B = business)
     */
    public function actionDefault($start, array $destinations, $adt = 1, $chd = 0, $inf = 0, $format='json')
    {
        $tourSearchParams = $this->buildSearchParams($start, $destinations, $adt, $chd, $inf);
        $this->buildTour($tourSearchParams);
        $results = $this->searchTourVariants();
        if (empty($this->errors))
            $this->sendWithCorrectFormat($format, $results);
        else
            $this->sendError(500, CVarDumper::dumpAsString($this->errors));
    }

    private function buildSearchParams($start, $destinations, $adt, $chd, $inf)
    {
        $tourBuilder = new TourBuilderForm();
        $tourBuilder->setStartCityName($start);
        $tourBuilder->adultCount = $adt;
        $tourBuilder->childCount = $chd;
        $tourBuilder->infantCount = $inf;
        $tourBuilder->trips = array();
        foreach ($destinations as $destination)
        {
            $trip = new TripForm();
            $trip->setCityName($destination['city']);
            $trip->startDate = $destination['dateFrom'];
            $trip->endDate = $destination['dateTo'];
            $tourBuilder->trips[] = $trip;
        }
        return $tourBuilder;
    }

    private function buildTour(TourBuilderForm $tourSearchParams)
    {
        ConstructorBuilder::buildAndPutToCart($tourSearchParams);
    }

    private function searchTourVariants()
    {
        $this->getAllTourVariants();
        return array(
            'cheapest' => $this->filterCheapest(),
            'fastest'  => $this->filterFastest(),
            'optimal'  => $this->filterOptimal()
        );
    }

    private function getAllTourVariants()
    {
        $dataProvider = new TripDataProvider();
        $items = $dataProvider->getSortedCartItems();
        $asyncExecutor = new AsyncCurl();
        foreach ($items as $item)
        {
            $itemVariantsUrl = $item->getUrlToAllVariants();;
            $asyncExecutor->add($itemVariantsUrl);
        }
        $responses = $asyncExecutor->send();
        $this->errors = array();
        foreach ($responses as $response)
        {
            if ($httpCode=$response->headers['http_code'] == 200)
            {
                $this->variants[] = CJSON::decode($response->body);
            }
            else
                $this->errors[] = 'Error '.$httpCode;
        }
    }

    private function filterCheapest()
    {
        return $this->variants[0];
    }

    private function filterFastest()
    {
        return $this->variants[0];
    }

    private function filterOptimal()
    {
        return $this->variants[0];
    }

    private function sendWithCorrectFormat($format, $results)
    {
        if ($format == 'json')
            $this->sendJson($results);
        elseif ($format == 'xml')
            $this->sendXml($results, 'tourSearchResults');
        else
            $this->sendError(400, 'Incorrect response format');
    }
}
