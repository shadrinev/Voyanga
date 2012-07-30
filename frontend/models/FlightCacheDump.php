<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 15:03
 */
class FlightCacheDump extends Component
{
    public $createdAt;
    public $from;
    public $to;
    public $dateFrom;
    public $dateBack;
    public $attributes;

    /**
     * @param $value FlightCache
     */
    public function setModel($value)
    {
        $this->createdAt = time();
        $this->from = $value->from;
        $this->to = $value->to;
        $this->dateFrom = $value->dateFrom;
        $this->dateBack = $value->dateBack;
        $this->attributes = serialize($value->attributes);
    }
}
