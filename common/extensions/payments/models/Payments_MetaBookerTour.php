<?php

/*
 Container used to pay for multiple bookers,
 Mimics booker interface
 FIXME subclass from Payments_MetaBooker
 */
class Payments_MetaBookerTour extends CComponent{
    private $bookers;
    private $_billId;
    private $base;
    public function __construct($bookers, $base, $billId)
    {
        $this->bookers = array();
        if($base instanceof FlightBooker) {
            $bookerComp  = new FlightBookerComponent();
            $bookerComp->setFlightBookerFromId($base->id);
            $base = $bookerComp;
        }
        $this->base = $base;

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
        $this->_billId = $billId;
        if($billId)
            $this->setBillId($billId);
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
        return $this->getRealPrice();
//        return $this->base->getCurrent()->price;
    }


   public function getRealPrice() {
        $price = 0;
        foreach ($this->bookers as $booker) {
            $price += $booker->getCurrent()->price;
        }
        return $price;// - $this->getPrice();
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

    public function getBaseBooker()
    {
        return $this->base;
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

   public function getBookers()
   {
       return $this->bookers;
   }


    public function getSmallDEscription()
    {
        return "Тур";
    }
}