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

    private $asyncExecutor;

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
    public function actionDefault($start, array $destinations, array $rooms, $format = 'json', $eventId = null)
    {

        $tourSearchParams = $this->buildSearchParams($start, $destinations, $rooms);
        if($eventId){
            $event = Event::model()->findByPk($eventId);
            $dataProvider = new TripDataProvider();
            foreach($event->tours as $tour){
                if($tourSearchParams->getStartCityId() == $tour->startCityId){
                    $dataProvider->restoreFromDb($tour->orderId);
                    //echo $tour->orderId.'dsf';

                    $items = $dataProvider->getWithAdditionalInfo($dataProvider->getSortedCartItemsOnePerGroup(false));
                    //print_r($items);
                }
            }
        }
        $this->buildTour($tourSearchParams);
        $results = $this->searchTourVariants();
        if(isset($items)){
            $results['items'] = $items['items'];
        }
        if (empty($this->errors))
            $this->sendWithCorrectFormat($format, $results);
        else
            $this->sendError(200, CVarDumper::dumpAsString($this->errors));
        Yii::app()->end();
    }

    public function actionComplex($format = 'json')
    {
        $items = $_REQUEST['items'];
        $this->asyncExecutor = new AsyncCurl();
        foreach ($items as $item)
        {
            if ($item['type'] == 'avia')
                $this->addAviaAsyncRequest($item);
            elseif ($item['type'] == 'hotel')
                $this->addHotelAsyncRequest($item);
        }
        $responses = $this->asyncExecutor->send();
        $result = array();
        foreach ($responses as $response)
        {
            if ($httpCode = $response->headers['http_code'] == 200)
            {
                $result[] = CJSON::decode($response->body);
            }
            else
                $errors[] = 'Error ' . $httpCode;
        }
        if (!empty($this->errors))
        {
            $this->sendError(200, CVarDumper::dump($errors));
            Yii::app()->end();
        }
        else
        {
            $this->sendWithCorrectFormat($format, $result);
        }
    }

    public function actionUpdateEvent()
    {
        print_r($_POST);
        $event = Event::model()->findByPk($_POST['eventId']);
        if(isset($_POST['startCity'])){
            $startCity = City::getCityByCode($_POST['startCity']);
        }

        foreach($event->tours as $tour){

            if($tour->startCityId == $startCity->id){
                echo "find startCity";
                break;
            }else{
                echo "cid : ".$tour->startCityId;
            }
            $tour = null;
        }
        if($tour){
            //print_r($tour);
        }
        if($tour->order){
            //print_r($tour->order);
        }
        $items = $_POST['items'];
        foreach($items as $item){
            if($item['type'] == 'hotel'){
                $item['data']['cityId'] = City::getCityByCode($item['data']['cityCode'])->hotelbookId;
                $item['data']['rubPrice'] = $item['data']['discountPrice'];
                $item['data']['comparePrice'] = $item['data']['discountPrice'];
                $hotel = new Hotel($item['data']);
                //print_r($hotel->getJsonObject());
                $cityId = $hotel->city->id;
                /** var OrderHotel $hotelItem */
                foreach($tour->order->hotelItems as $hotelItem){
                    //$hotelItem = new OrderHotel();

                    if($hotelItem->cityId == $cityId || $hotelItem->checkIn == $hotel->checkIn){
                        $hotelItem->object = serialize($hotel);
                        $hotelItem->isNewRecord = false;
                        echo "update ".$hotelItem->id." orderHotel".$hotel->checkIn.serialize($hotel);
                        $hotelItem->save();
                    }
                    //print_r($hotelItem);
                }
            }else{
                $flight = new FlightVoyage($item['data']);
                //print_r($flight);
                foreach($flight->flights as $flightKey=>$fl){
                    foreach($tour->order->flightItems as $flightItem){
                        if($flightItem->departureCity == $flight->getDepartureCity($flightKey)->id
                            && $flightItem->arrivalCity == $flight->getArrivalCity($flightKey)->id
                            && $flightItem->departureDate == date('Y-m-d',strtotime($flight->getDepartureDate($flightKey))) ){
                            $flightItem->object = serialize($flight);
                            $flightItem->isNewRecord = false;
                            echo "update ".$flightItem->id." flight\n";
                            $flightItem->save();
                            break;
                        }else{
                            echo "notUpdate ".$flightItem->id." flight ".$flightItem->departureCity.'|'.$flight->getDepartureCity()->id.'|'.$flightItem->arrivalCity.'|'.$flight->getArrivalCity()->id.'|'.$flightItem->departureDate.'|'.$flight->getDepartureDate()."\n";
                            //print_r($flight->getJsonObject());die();
                        }
                    }
                }
            }
        }

    }

    private function addAviaAsyncRequest($sp)
    {
        $url = Yii::app()->createAbsoluteUrl('/v1/flight/search/BE');
        $query = http_build_query(array(
            'destinations' => $sp['destinations'],
            'adt' => $sp['adt'],
            'chd' => $sp['chd'],
            'inf' => $sp['inf'],
        ));
        $url = $url . '?' . $query;
        $this->asyncExecutor->add($url);
    }

    private function addHotelAsyncRequest($sp)
    {
        $url = Yii::app()->createAbsoluteUrl('/v1/hotel/search');
        $query = http_build_query(array(
            'city' => $sp['city'],
            'checkIn' => $sp['checkIn'],
            'duration' => $sp['duration'],
            'rooms' => $sp['rooms'],
        ));
        $url = $url . '?' . $query;
        $this->asyncExecutor->add($url);
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
            }
            else if ($group[0] instanceof HotelTripElement)
            {
                $itemVariantsUrl = $group[0]->getUrlToAllVariants();
                $asyncExecutor->add($itemVariantsUrl);
            }
        }
        $responses = $asyncExecutor->send();
        $this->errors = array();
        foreach ($responses as $response)
        {
            if ($httpCode = $response->headers['http_code'] == 200)
            {
                $this->variants[] = CJSON::decode($response->body);
            }
            else
                $this->errors[] = 'Error ' . $httpCode;
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
                $current[] = $this->filterHotel($variant, $mask);
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
            if ($i > 0)
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
