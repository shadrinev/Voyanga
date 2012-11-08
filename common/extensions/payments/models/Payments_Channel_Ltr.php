<?php

Yii::import("common.extensions.payments.models.Payments_Channel");

class Payments_Channel_Ltr extends Payments_Channel {
    protected $name = 'ltr';

    protected function contributeToConfirm($context, $booker)
    {
        $context['IData'] = $this->getIData($booker);
        return $context;
    }

    private function getIData($booker)
    {
        $flightVoyage = $booker->flightVoyage;
        $ltr = "";
        $ltr.= "01"; // CONST
        $ltr.= "        "; // AGENCY CODE
        //! FIXME: WHAT IF WE HAVE MORE THAN 4 FLIGHTS????!!!1111
        $i = 0;
        foreach($flightVoyage->flights as $flight)
        {
            foreach($flight->flightParts as $part){
                $i++;
                $ltr.= $part->transportAirlineCode; //CARREIR LEGN
                $ltr.= "X"; //SERVICE CLASS LEGN
                $ltr.= " "; //STOPOVER CODE LEGN //optional
                $ltr.= $part->arrivalAirport->code; //DEST CITY LEGN

                if($i>3)
                    break 2;
            }
        }
        //! FIXME did not tested for edge case
        $ltr.= str_repeat('       ', 4-$i);
        $ltr.= "1"; // RESTRICTED TICKET INDICATOR // NELZA VERNUT
        //! FIXME !
        $ltr.= "12345678901234"; // TICKET NUMBER
        //! FIXME check how de focking php TZs are working
        list($date, $time) = explode("T",$flightVoyage->flights[0]->departureDate);
        list($year, $month, $date) = explode('-', $date);
        $ltr.= $month . $date . ($year%100);
        //! FIXME Write getters for this
        $ltr.= $flightVoyage->flights[0]->flightParts[0]->departureAirport->code;
        //! FIXME can we get unicode here ?
        $name = $booker->flightBookingPassports[0]->firstName . ' ' .$booker->flightBookingPassports[0]->lastName;
        if(strlen($name)>20)
        {
            $name = substr($name, 0, 20);
        }
        else
        {
            $diff = 20-strlen($name);
            $name .= str_repeat(' ', $diff);
        }
        $ltr.= $name;

        if(!preg_match("~01[ \w]{8}[A-Z]{2}[ \w]{1}[ O]{1}[A-Z]{3}(([A-Z]{2}[ \w]{1}[ O]{1}[A-Z]{3})|[ ]{7}){3}[ 01]{1}[ \w-]{14}[\d]{6}[A-Z]{3}[ \w/-]{20}~", $ltr))
            throw new Exception("Wrong LTR generated");
        return $ltr;
    }
}