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
     * @param array $rooms
     *  [Х][adt]
     *  [Х][chd]
     *  [Х][chdAge]
     *  [Х][cots]
     */
    public function actionDefault($start, array $destinations, array $rooms, $format='json')
    {
        $tourSearchParams = $this->buildSearchParams($start, $destinations, $rooms);
        $this->buildTour($tourSearchParams);
        $results = $this->searchTourVariants();
        if (empty($this->errors))
            $this->sendWithCorrectFormat($format, $results);
        else
            $this->sendError(200, CVarDumper::dumpAsString($this->errors));
        Yii::app()->end();
    }

    private function buildSearchParams($start, $destinations, $rooms)
    {
        $tourBuilder = new TourBuilderForm();
        $tourBuilder->setStartCityName($start);
        $tourBuilder->rooms = array();
        foreach ($rooms as $room)
        {
            $newRoom = new HotelRoomForm;
            $newRoom->adultCount = $room['adt'];
            $newRoom->childCount = $room['chd'];
            $newRoom->childAge = $room['chdAge'];
            $newRoom->cots = $room['cots'];
            $tourBuilder->rooms[] = $newRoom;
        }
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
        $allVariants = $this->variants;
        return array(
            'allVariants' => $allVariants
        );
    }

    private function getAllTourVariants()
    {
        $asyncExecutor = new AsyncCurl();
        $dataProvider = new TripDataProvider();
        $items = $dataProvider->getSortedCartItems();
        $grouped = array();
        foreach ($items as $item)
        {
            if ($item instanceof FlightTripElement)
            {
                $grouped[$item->getGroupId()][] = $item;
            }
            else
            {
                $grouped[$item->getGroupId()][] = $item;
            }
        }
        foreach ($grouped as $group)
        {
            if ($group[0] instanceof FlightTripElement)
            {
                $url = FlightTripElement::getUrlToAllVariants($group);
                $asyncExecutor->add($url);
            } else if ($group[0] instanceof HotelTripElement)
            {
                $itemVariantsUrl = $group[0]->getUrlToAllVariants();
                $asyncExecutor->add($itemVariantsUrl);
            }
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
