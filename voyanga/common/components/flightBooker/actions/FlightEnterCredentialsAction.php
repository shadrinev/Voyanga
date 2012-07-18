<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 02.07.12
 * Time: 11:13
 * To change this template use File | Settings | File Templates.
 */
class FlightEnterCredentialsAction extends StageAction
{
    private function collect($formName, &$passportToFill)
    {
        $valid = false;
        if(isset($_POST[$formName]))
        {
            $valid = true;
            foreach ($_POST[$formName] as $i => $passportForm)
            {
                $passport = new $formName();
                $passport->attributes=$passportForm;
                $valid = $valid && $passport->validate();
                if ($valid)
                    $passportToFill[$i] = $passport;
            }
        }
        return $valid;
    }

    public function execute()
    {
        //collecting booking info for whole ticket
        $booking = new BookingForm();
        $valid = $this->collect('BookingForm', $booking);

        //collecting adults passport data
        $adults = Yii::app()->flightBooker->getCurrent()->FlightVoyage->adultPassengerInfo;
        $adultPassports = array();
        if ($adults)
        {
            $countAdults = $adults->count;
            for ($i=0; $i<$countAdults; $i++)
            {
                $adultPassports[] = new FlightAdultPassportForm();
            }
            $valid = $valid && $this->collect('FlightAdultPassportForm', $adultPassports);
        }

        //collecting child passport data
        $children = Yii::app()->flightBooker->getCurrent()->FlightVoyage->childPassengerInfo;
        $childrenPassports = array();
        if ($children)
        {
            $countChildren = $children->count;
            for ($i=0; $i<$countChildren; $i++)
            {
                $childrenPassports[] = new FlightChildPassportForm();
            }
            $valid = $valid && $this->collect('FlightChildPassportForm', $childrenPassports);
        }

        //collecting infant passport data
        $infant = Yii::app()->flightBooker->getCurrent()->FlightVoyage->infantPassengerInfo;
        $infantPassports = array();
        if ($infant)
        {
            $infantChildren = $infant->count;
            for ($i=0; $i<$infantChildren; $i++)
            {
                $infantPassports[] = new FlightInfantPassportForm();
            }
            $valid = $valid && $this->collect('FlightInfantPassportForm', $infantPassports);
        }

        if ($valid)
        {
            //saving data to objects
            //TODO: link to OrderBooking object
            $flightBookerComponent = Yii::app()->flightBooker;
            $flightBookerComponent->book();
            $flightBookerId = $flightBookerComponent->getCurrent()->primaryKey;

            $bookingAr = new OrderBooking();
            $bookingAr->populate($booking);
            if (!Yii::app()->user->isGuest)
                $bookingAr->userId = Yii::app()->user->id;

            $bookingPassports = array();
            foreach($adultPassports as $passport)
            {
                $bookingPassport = new FlightBookingPassport();
                $bookingPassport->populate($passport, $flightBookerId);
                if(!$bookingPassport->validate())
                {
                    VarDumper::dump($bookingPassport->getErrors());
                }
                else
                {
                    $bookingPassports[] = $bookingPassport;
                }
            }

            VarDumper::dump($bookingPassports); die();

            if($bookingAr->save())
            {
                Yii::app()->flightBooker->getCurrent()->orderBookingId = $bookingAr->id;
                Yii::app()->flightBooker->status('booking');
                $this->getController()->refresh();
            }
            else
            {
                VarDumper::dump($bookingAr->getErrors());
                $this->getController()->render('flightBooker.views.enterCredentials', array('passport'=>$passport, 'booking'=>$booking));
            }
        }
        else
        {
            //die();
            $this->getController()->render('flightBooker.views.enterCredentials',
                array(
                    'adultPassports' => $adultPassports,
                    'childrenPassports' => $childrenPassports,
                    'infantPassports' => $infantPassports,
                    'booking' => $booking
                )
            );
        }
    }
}
