<?php
class Passenger{
    /*
     * iType:
     * 1 - adult
     * 2 - child
     * 3 - infant
     */
    public $iType;
    public $oPassport;
    
    public function checkValid(){
        return true;
    }
}