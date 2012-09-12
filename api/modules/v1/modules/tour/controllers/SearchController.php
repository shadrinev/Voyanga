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
        if (!empty($this->errors))
            return false;
        $cheapestTour = $this->filterCheapest();
        $fastestTour = $this->filterFastest();
        $optimalTour = $this->filterOptimal();
        return array(
            'cheapest' => array(
                'totalPrice' => $this->getTotalPrice($cheapestTour),
                'tour' => $cheapestTour,
            ),
            'fastest'  => array(
                'totalPrice' => $this->getTotalPrice($fastestTour),
                'tour' => $fastestTour,
            ),
            'optimal'  => array(
                'totalPrice' => $this->getTotalPrice($optimalTour),
                'tour' => $optimalTour,
            ),
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
        return $this->filterByMask(FlightVoyage::MASK_BEST_PRICE);
    }

    private function filterFastest()
    {
        return $this->filterByMask(FlightVoyage::MASK_BEST_TIME);
    }

    private function filterOptimal()
    {
        return $this->filterByMask(FlightVoyage::MASK_BEST_PRICETIME);
    }

    private function filterByMask($mask)
    {
        $current = array();
        foreach ($this->variants as $variant)
        {
            if (isset($variant['flightVoyages']))
                $current[] = $this->filterFlight($variant, $mask);
            else
                $current[] = $this->filterHotel($variant,  $mask);
        }
        return $current;
    }

    private function filterFlight($variants, $mask)
    {
        $clone = $variants['flightVoyages'];
        foreach ($variants['flightVoyages'] as $i => $variant)
        {
            if (!($variant['bestMask'] & $mask))
            {
                unset($clone[$i]);
            }
        }
        unset ($variants['flightVoyages']);
        $variants['flight'] = reset($clone);
        return $variants;
    }

    private function filterHotel($variants, $mask)
    {
        $clone = $variants['hotels'];
        foreach ($variants['hotels'] as $i => $variant)
        {
            if ($i>0)
            {
                unset($clone[$i]);
            }
        }
        unset ($variants['hotels']);
        $variants['hotel'] = reset($clone);
        return $variants;
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

    private function getTotalPrice($elements)
    {
        $total = 0;
        foreach ($elements as $element)
        {
            if (isset($element['flight']))
                $total += $element['flight']['price'];
            else
                $total += $element['hotel']['rubPrice'];
        }
        return $total;
    }
}
