<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 19.08.12 19:26
 */
class TripDataProvider
{
    public $shoppingCartComponent = 'shoppingCart';

    private $usedGroups;
    private $sortedCartItems;
    private $sortedCartItemsOnePerGroup;

    private $bookerIds=array();

    public function __construct($orderBookingId=false)
    {
        $this->usedGroups = array();
        if (is_numeric($orderBookingId))
        {
            $this->restoreOrderBookingFromDb($orderBookingId);
        }
    }


    public function restoreOrderBookingFromDb($orderBookingId)
    {
        $orderBooking = OrderBooking::model()->findByPk($orderBookingId);
        if (!$orderBooking)
            throw new CException("No such order");
        $flights = $orderBooking->flightBookers;
        $hotels = $orderBooking->hotelBookers;
        Yii::app()->{$this->shoppingCartComponent}->clear();
        foreach ($flights as $flight)
        {
            $flightVoyage = unserialize($flight->flightVoyageInfo);
            $searchParams = @unserialize($flight->searchParams);
            $flightTripElement = new FlightTripElement();
            $flightTripElement->flightVoyage = $flightVoyage;
            $flightTripElement->flightBookerId = $flight->id;
            if ($searchParams)
                $flightTripElement->fillFromSearchParams($searchParams);
            Yii::app()->{$this->shoppingCartComponent}->put($flightTripElement);
        }
        foreach ($hotels as $hotel)
        {
            $hotelInfo = unserialize($hotel->hotelInfo);
            $searchParams = @unserialize($hotel->searchParams);
            $hotelTripElement = new HotelTripElement();
            $hotelTripElement->hotel = $hotelInfo;
            $hotelTripElement->hotelBookerId = $hotel->id;
            if ($searchParams)
                $hotelTripElement->fillFromSearchParams($searchParams);
            Yii::app()->{$this->shoppingCartComponent}->put($hotelTripElement);
        }
    }

    public function restoreFromDb($orderId)
    {
        Yii::app()->shoppingCart->clear();
        $order = Order::model()->findByPk($orderId);
        if (!$order)
            throw new CException("No such order");
        $items = $order->flightItems();
        foreach ($items as $item)
        {
            $flightTripElement = new FlightTripElement();
            $searchParams = @unserialize($item->searchParams);
            if ($searchParams)
                $flightTripElement->fillFromSearchParams($searchParams);
            $object = @unserialize($item->object);
            if ($object)
            {
                $flightTripElement->flightVoyage = $object;
            }
            Yii::app()->shoppingCart->put($flightTripElement);
        }
        $items = $order->hotelItems();
        foreach ($items as $item)
        {
            $hotelTripElement = new HotelTripElement();
            $searchParams = @unserialize($item->searchParams);
            if ($searchParams)
                $hotelTripElement->fillFromSearchParams($searchParams);
            /*$city = City::model()->findByPk($item->cityId);
            $hotelTripElement->city = $city;
            $hotelTripElement->checkIn = $item->checkIn;*/
            $object = @unserialize($item->object);
            if ($object)
            {
                $hotelTripElement->hotel = $object;
            }
            if (false && $hotelTripElement->searchParams && $hotelTripElement->searchParams['cityFull'])
            {
                //$hotelTripElement->searchParams->cityFull = $hotelTripElement->searchParams->cityFull->getAttributes();
            }
            Yii::app()->shoppingCart->put($hotelTripElement);
        }
    }

    public function getSortedCartItemsOnePerGroup($cache = true)
    {
        if (!$this->sortedCartItemsOnePerGroup || !$cache)
        {
            $items = $this->getSortedCartItems($cache);
            $this->sortedCartItemsOnePerGroup = $this->getItemsOnePerGroup($items);
        }
        return $this->sortedCartItemsOnePerGroup;
    }

    public function getSortedCartItems($cache = true)
    {
        if (!$this->sortedCartItems  || !$cache)
        {
            $items = $this->getDbItems();
            $this->sortedCartItems = $this->sortItemsFromCartAndGetThem($items);
        }
        return $this->sortedCartItems;
    }

    public function getSortedCartItemsOnePerGroupAsJson()
    {
        return json_encode($this->getWithAdditionalInfo($this->getSortedCartItemsOnePerGroup()));
    }

    public function getSortedCartItemsAsJson()
    {
        $items = $this->getSortedCartItems();
        return $this->getJsonWithAdditionalInfo($items);
    }

    private function getDbItems()
    {
        return Yii::app()->{$this->shoppingCartComponent}->getPositions();
    }

    private function sortItemsFromCartAndGetThem($items)
    {
        $times = $this->getTimesForCartItems($items);
        $weights = $this->getWeightsForCartItems($items);
        return $this->getItemsSortedByTimeAndWeights($items, $times, $weights);
    }

    private function getJsonWithAdditionalInfo($items)
    {
        $out = array();
        foreach ($items as $item)
        {
            $prepared = $item->getJsonObject();
            $prepared['isLinked'] = $item->isLinked();
            $prepared['searchParams'] = $item->getJsonObjectForSearchParams();
            TripDataProvider::injectAdditionalInfo($prepared);
            $out['items'][] = $prepared;
        }
        return json_encode($out);
    }

    public function getWithAdditionalInfo($items)
    {
        $out = array();
        foreach ($items as $item)
        {
            $prepared = $item->getJsonObject();
            $prepared['isLinked'] = $item->isLinked();
            $prepared['searchParams'] = $item->getJsonObjectForSearchParams();
            TripDataProvider::injectAdditionalInfo($prepared);
            $out['items'][] = $prepared;
        }
        return $out;
    }

    private function getItemsOnePerGroup($items)
    {
        $uniqueItems = array();
        foreach ($items as $item)
        {
            $groupId = $item->groupId;
            if ($this->itemsDoNotUseGroupsOrItemGroupIsFirstTime($groupId))
            {
                $uniqueItems[] = $item;
                $this->usedGroups[] = $groupId;
            }
        }
        return $uniqueItems;
    }

    private function itemsDoNotUseGroupsOrItemGroupIsFirstTime($groupId)
    {
        return ($groupId === false) or (!in_array($groupId, $this->usedGroups));
    }

    private function getTimesForCartItems($items)
    {
        $times = array();
        foreach ($items as $item)
        {
            $times[] = $item->getTime();
        }
        return $times;
    }

    private function getWeightsForCartItems($items)
    {
        return array_map(function ($item) { return $item->getWeight(); }, $items);
    }

    private function getItemsSortedByTimeAndWeights($items, $time, $weight)
    {
        array_multisort($time, SORT_ASC, SORT_NUMERIC, $weight, SORT_ASC, SORT_NUMERIC, $items);
        return $items;
    }

    public static function injectAdditionalInfo(&$element)
    {
        Yii::import('site.common.modules.hotel.models.*');
        $hotelClient = new HotelBookClient();

        $element['isFlight'] = isset($element['flightKey']);
        $element['isHotel'] = !isset($element['flightKey']);
        if ($element['isHotel'])
        {
            $element['hotelDetails'] = $hotelClient->hotelDetail($element['hotelId']);
        }
        if ($element['isFlight'])
        {
            $elements = FlightManager::injectForBe(array($element), true);
            $element = $elements[0];
        }
    }

    public function getIconAndTextForPassports()
    {
        $items = $this->getDbItems();
        $onlyFlights = true;
        $onlyHotels = true;
        foreach ($items as $item)
        {
            if ($item instanceof FlightTripElement)
                $onlyHotels = false;
            if ($item instanceof HotelTripElement)
                $onlyFlights = false;
        }
        if ($onlyFlights)
        {
            $icon = 'ico-fly';
            $message = 'Пассажиры';
        }
        elseif ($onlyHotels)
        {
            $icon = 'ico-hotel';
            $message = 'Гости';
        }
        else
        {
            $icon = 'ico-fly';
            $message = 'Пассажиры';
        }
        return array($icon, $message);
    }
}
