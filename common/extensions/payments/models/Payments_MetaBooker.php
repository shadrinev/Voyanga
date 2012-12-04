<?php

/*
 Container used to pay for multiple bookers,
 Mimics booker interface
 */
class Payments_MetaBooker extends CComponent{
    private $bookers;
    private $_billId;
    public function __construct($bookers, $billId)
    {
        $this->bookers = $bookers;
        $this->_billId = $billId;
    }

    public function getBillId() {
        return $this->_billId;
    }

    public function setBillId($billId) {
        foreach($this->bookers as $booker){
            $booker->getCurrent()->billId = $billId;
        }
    }
    public function getPrice() {
        $price = 0;
        foreach ($this->bookers as $booker) {
            $price += $booker->getCurrent()->price;
        }
        return $price;
    }

    public function save() {
        foreach($this->bookers as $booker){
            $booker->getCurrent()->save();
        }
    }

    public function status($status) {
        foreach($this->bookers as $booker){
            $booker->status($status);
        }
    }
}