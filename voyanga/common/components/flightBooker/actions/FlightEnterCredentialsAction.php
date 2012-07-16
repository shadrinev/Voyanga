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
    public function execute()
    {
        $valid = true;
        $booking = new BookingForm();

        $adults = Yii::app()->flightBooker->getCurrent()->FlightVoyage;
        VarDumper::dump($adults); die();

        if(isset($_POST['BookingForm']))
        {
            $booking->attributes=$_POST['BookingForm'];
            $valid = $booking->validate() && $valid;
        }
        else
        {
            $valid = false;
        }

        $passport = new FlightPassportForm();
        if(isset($_POST['FlightPassportForm']))
        {
            $passport->attributes=$_POST['AviaPassportForm'][1];
            $valid = $valid && $passport->validate();
        }
        $passports[] = $passport;

        if($valid)
        {
            //saving data to objects
            //TODO: link to OrderBooking object
            $flightBookerComponent = Yii::app()->flightBooker;
            $flightBookerComponent->book();

            $flightBookerId = $flightBookerComponent->getCurrent()->primaryKey;
            $bookingAr = new OrderBooking();

            $bookingAr->email = $booking->contactEmail;
            $bookingAr->phone = $booking->contactPhone;
            if (!Yii::app()->user->isGuest)
                $bookingAr->userId = Yii::app()->user->id;

            $bookingPassports = array();
            foreach($passports as $passport)
            {
                $bookingPassport = new FlightBookingPassport();
                $bookingPassport->birthday = $passport->birthday;
                $bookingPassport->firstName = $passport->firstName;
                $bookingPassport->lastName = $passport->lastName;
                $bookingPassport->countryId = $passport->countryId;
                $bookingPassport->number = $passport->number;
                $bookingPassport->series = $passport->series;
                $bookingPassport->genderId = $passport->genderId;
                $bookingPassport->documentTypeId = $passport->documentTypeId;
                $bookingPassport->flightBookingId = $flightBookerId;
                if(!$bookingPassport->save())
                {
                    VarDumper::dump($bookingPassport->getErrors());
                    die();

                }

                $bookingPassports[] = $bookingPassport;
            }


            //$bookingAr->bookingPassports = $bookingPassports;
            //$bookingAr->flightId = Yii::app()->flightBooker->current->flightVoyage->flightKey;

            if($bookingAr->save())
            {
                Yii::app()->flightBooker->getCurrent()->orderBookingId = $bookingAr->id;
                Yii::app()->flightBooker->status('booking');
                $this->getController()->refresh();
            }
            else
            {
                VarDumper::dump($bookingAr->getErrors());
                //echo "error on save()";
                $this->getController()->render('flightBooker.views.enterCredentials', array('passport'=>$passport, 'booking'=>$booking));
            }
        }
        else
        {
            //die();
            $this->getController()->render('flightBooker.views.enterCredentials', array('passport'=>$passport, 'booking'=>$booking));
        }

    }
}
