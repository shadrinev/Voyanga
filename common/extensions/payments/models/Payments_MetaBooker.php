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
        $this->bookers = array();
        foreach ($bookers as $booker) {
            if($booker instanceof FlightBooker) {
                $bookerComp  = new FlightBookerComponent();
                $bookerComp->setFlightBookerFromId($booker->id);
            } else {
                $bookerComp  = new HotelBookerComponent();
                $bookerComp->setHotelBookerFromId($booker->id);
            }
            $this->bookers[] = $bookerComp;
        }
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