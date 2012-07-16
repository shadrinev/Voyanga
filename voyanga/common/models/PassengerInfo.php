<?php
class PassengerInfo
{
    public $count = 0;
    public $priceDetail;
    public function __construct($params)
    {
        if(isset($params['count'])) $this->count = $params['count'];
        if(isset($params['total_fare'])) $this->priceDetail = $params['total_fare'];
    }
}