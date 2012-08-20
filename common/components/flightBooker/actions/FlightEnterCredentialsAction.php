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
    private function collect($formName, &$formToFill)
    {
        $valid = false;
        if(isset($_POST[$formName]))
        {
            $valid = true;
            foreach ($_POST[$formName] as $i => $formValue)
            {
                $form = new $formName();
                $form->attributes=$formValue;
                $isOk = $form->validate();
                $valid = $valid && $isOk;
                $formToFill[$i] = $form;
            }
        }
        return $valid;
    }

    public function execute()
    {
        $valid = false;

        //collecting booking info for whole ticket
        $booking = new BookingForm();
        if(isset($_POST['BookingForm']))
        {
            $valid = true;
            $booking->attributes=$_POST['BookingForm'];
            $valid = $valid && $booking->validate();
        }

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
            $isOk = $this->collect('FlightAdultPassportForm', $adultPassports);
            $valid = $valid && $isOk;
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
            $isOk = $this->collect('FlightChildPassportForm', $childrenPassports);
            $valid = $valid && $isOk;
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
            $isOk = $this->collect('FlightInfantPassportForm', $infantPassports);
            $valid = $valid && $isOk;
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
            $allPassports = array_merge($adultPassports, $childrenPassports, $infantPassports);
            foreach($allPassports as $passport)
            {
                $bookingPassport = new FlightBookingPassport();
                $bookingPassport->populate($passport, $flightBookerId);
                if(!$bookingPassport->save())
                {
                    VarDumper::dump($bookingPassport->getErrors());
                }
                else
                {
                    $bookingPassports[] = $bookingPassport;
                }
            }

            if($bookingAr->save())
            {
                Yii::app()->flightBooker->getCurrent()->orderBookingId = $bookingAr->id;
                Yii::app()->flightBooker->status('booking');
            }
            else
            {
                VarDumper::dump($bookingAr->getErrors());
                $this->getController()->render('flightBooker.views.enterCredentials', array('passport'=>$passport, 'booking'=>$booking));
            }
        }
        else
        {
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
