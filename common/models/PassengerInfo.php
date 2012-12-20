<?php
class PassengerInfo
{
    public $count = 0;
    public $priceDetail;
    public $baseFare;
    public function __construct($params)
    {
        if(isset($params['count'])) $this->count = $params['count'];
        if(isset($params['total_fare'])) $this->priceDetail = $params['total_fare'];
        if(isset($params['base_fare'])) $this->baseFare = $params['base_fare'];
    }
}