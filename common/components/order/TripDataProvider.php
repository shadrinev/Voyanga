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
            $this->restoreFromDb($orderBookingId);
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
            $flightTripElement->departureDate = $item->departureDate;
            $flightTripElement->departureCity = $item->departureCity;
            $flightTripElement->arrivalCity = $item->arrivalCity;
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
            $city = City::model()->findByPk($item->cityId);
            $hotelTripElement->city = $city;
            $hotelTripElement->checkIn = $item->checkIn;
            $object = @unserialize($item->object);
            if ($object)
            {
                $hotelTripElement->hotel = $object;
            }
            Yii::app()->shoppingCart->put($hotelTripElement);
        }
    }

    public function getItemsByOrderId($orderBookingId)
    {
        $orderBooking = OrderBooking::model()->findByPk($orderBookingId);
        if (!$orderBooking)
            throw new CException("No such order");
        $flights = $orderBooking->flightBookers;
        $hotels = $orderBooking->hotelBookers;
        $elements = array();
        foreach ($flights as $flight)
        {
            $flightVoyage = unserialize($flight->flightVoyageInfo);
            $flightTripElement = new FlightTripElement();
            $flightTripElement->flightVoyage = $flightVoyage;
            $flightTripElement->flightBookerId = $flight->id;
            $elements[] = $flightTripElement;
        }
        foreach ($hotels as $hotel)
        {
            $hotelInfo = unserialize($hotel->hotelInfo);
            $hotelTripElement = new HotelTripElement();
            $hotelTripElement->hotel = $hotelInfo;
            $hotelTripElement->hotelBookerId = $hotel->id;
            $elements[] = $hotelTripElement;
        }
    }

    public function getSortedCartItemsOnePerGroup($cache = true)
    {
        if (!$this->sortedCartItemsOnePerGroup || !$cache)
        {
            $items = $this->getSortedCartItems($cache);
            $this->sortedCartItemsOnePerGroup = $this->getItemsOnePerGroup($items);
        }
        return $this->getWithAdditionalInfo($this->sortedCartItemsOnePerGroup);
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
        return json_encode($this->getSortedCartItemsOnePerGroup());
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

    private function getWithAdditionalInfo($items)
    {
        $out = array();
        foreach ($items as $item)
        {
            $prepared = $item->getJsonObject();
            $prepared['isLinked'] = $item->isLinked();
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


    }
}
