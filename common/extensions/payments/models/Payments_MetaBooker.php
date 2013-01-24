<?php

/*
 Container used to pay for multiple bookers,
 Mimics booker interface
 */
class Payments_MetaBooker extends CComponent{
    private $bookers;
    private $_billId;
    public $orderBookingId;
    public function __construct($bookers, $billId)
    {
        $this->bookers = array();
        foreach ($bookers as $booker) {
            if($booker instanceof FlightBooker) {
                $bookerComp  = new FlightBookerComponent();
                $bookerComp->setFlightBookerFromId($booker->id);
                $this->bookers[] = $bookerComp;
            } elseif ($booker instanceof HotelBooker) {
                $bookerComp  = new HotelBookerComponent();
                $bookerComp->setHotelBookerFromId($booker->id);
                $this->bookers[] = $bookerComp;
            } else {
                $this->bookers[] = $booker;
            }
        }
        $this->orderBookingId = $this->bookers[0]->getCurrent()->orderBookingId;
        $this->_billId = $billId;
        if($billId)
            $this->setBillId($billId);
    }

    public function getBillId() {
        return $this->_billId;
    }

    public function setBillId($billId) {
        foreach($this->bookers as $booker){
            if(($booker->getCurrent()->billId != $billId) && $billId)
                throw new Exception("Hotel set for payment is broken");
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

    public function getStatus() {
        $waitingForPayment = true;
        foreach ($this->bookers as $booker) {
            if (!$this->isWaitingForPayment($booker)) {
                $waitingForPayment = false;
            }
        }
        if($waitingForPayment)
            return 'waitingForPayment';
        //! FIXME temporary
        return $this->bookers[0]->getStatus();
    }

    public function status($status) {
        foreach($this->bookers as $booker){
            $booker->status($status);
        }
    }

    /**
       Almost copy pasted from success action, move to payments?
    */
    protected function isWaitingForPayment($booker)
    {
        if($this->getBookerStatus($booker)=='waitingForPayment')
            return true;
        return false;
    }

    //! helper function returns last segment of 2 segment statuses
    protected function getBookerStatus($booker)
    {
        $status = $booker->getStatus();
        $parts = explode("/", $status);
        if(count($parts)==2)
            return $parts[1];
        return $parts[0];
    }

    public function getPriceBreakdown()
    {
        $result = Array("price"=>$this->getPrice());
        if(count($this->bookers)==1)
            $result['title']= "гостиница";
        if(count($this->bookers)>1)
            $result['title']= "гостиницы";
        return Array($result);
    }

}