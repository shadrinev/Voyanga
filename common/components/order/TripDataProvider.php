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

    public function __construct()
    {
        $this->usedGroups = array();
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
            $items = $this->getCartItems();
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

    private function getCartItems()
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
            TripDataProvider::injectAdditionalInfo($prepared);
            $out[] = $prepared;
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
        return array_map(function ($item) { return $item->getTime(); }, $items);
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
        $element['isFlight'] = $element instanceof FlightTripElement;
        $element['isHotel'] = $element instanceof HotelTripElement;
    }
}
