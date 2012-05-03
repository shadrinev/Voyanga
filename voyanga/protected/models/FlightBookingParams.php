<?php 
class FlightBookingParams{
    public $sPhoneNumber;
    public $sContactEmail;
    public $sFlightId;
    public $sFlightClass;
    public $aPassengers;
    
    public function addPassenger($oPassenger){
        if($oPassenger instanceof Passenger){
            $this->aPassengers[] = $oPassenger;
        }else{
            throw new CException( Yii::t( 'application', 'Parameter oPassenger must be instance of Passenger' ) );
        }
    }
    
    public function checkValid(){
        $bValid = true;
        foreach ($this->aPassengers as $oPassenger){
            $bValid = $bValid && $oPassenger->checkValid();
        }
        return $bValid;
    }
}