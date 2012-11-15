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

    public function restoreFromDb($orderBookingId)
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
            $flightTripElement = new FlightTripElement();
            $flightTripElement->flightVoyage = $flightVoyage;
            $flightTripElement->flightBookerId = $flight->id;
            Yii::app()->{$this->shoppingCartComponent}->put($flightTripElement);
        }
        foreach ($hotels as $hotel)
        {
            $hotelInfo = unserialize($hotel->hotelInfo);
            $hotelTripElement = new HotelTripElement();
            $hotelTripElement->hotel = $hotelInfo;
            $hotelTripElement->hotelBookerId = $hotel->id;
            Yii::app()->{$this->shoppingCartComponent}->put($hotelTripElement);
        }
    }

    public function getSortedCartItemsOnePerGroup()
    {
        if (!$this->sortedCartItemsOnePerGroup)
        {
            $items = $this->getSortedCartItems();
            $this->sortedCartItemsOnePerGroup = $this->getItemsOnePerGroup($items);
        }
        return $this->sortedCartItemsOnePerGroup;
    }

    public function getSortedCartItems()
    {
        if (!$this->sortedCartItems)
        {
            $items = $this->getDbItems();
            $this->sortedCartItems = $this->sortItemsFromCartAndGetThem($items);
        }
        return $this->sortedCartItems;
    }

    public function getSortedCartItemsOnePerGroupAsJson()
    {
        $items = $this->getSortedCartItemsOnePerGroup();
        return $this->getJsonWithAdditionalInfo($items);
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
        $element['isFlight'] = isset($element['flightKey']);
        $element['isHotel'] = !isset($element['flightKey']);
        if ($element['isHotel'])
        {
            $element['hotelDetails'] = Yii::app()->pCache->get('HotelDetails-'.$element['hotelId']);
        }
        if ($element['isFlight'])
        {
            $elements = FlightManager::injectForBe(array($element), true);
            $element = $elements[0];
        }
    }
}
