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
        $this->dateFrom = date('Y-m-d', strtotime($value->dateFrom));
        $this->dateBack = date('Y-m-d', strtotime($value->dateBack));;
        $this->attributes = serialize($value->attributes);
    }
}
