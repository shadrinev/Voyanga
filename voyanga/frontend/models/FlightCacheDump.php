<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 15:03
 */
class FlightCacheDump extends Component
{
    public $dateTime;
    public $from;
    public $to;
    public $isBestTime;
    public $isBestPrice;
    public $isOptimal;
    public $attributes;

    /**
     * @param $value FlightCache
     */
    public function setModel($value)
    {
        $this->dateTime = strtotime($value->timestamp);
        $this->from = $value->departureCityId;
        $this->to = $value->arrivalCityId;
        $this->isBestPrice = $value->isBestPrice;
        $this->isBestTime = $value->isBestTime;
        $this->isOptimal = $value->isOptimal;
        $this->attributes = serialize($value->attributes);
    }
}
