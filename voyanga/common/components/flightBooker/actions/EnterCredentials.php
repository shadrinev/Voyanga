<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 02.07.12
 * Time: 11:13
 * To change this template use File | Settings | File Templates.
 */
class EnterCredentials extends StageAction
{
    public function execute()
    {
        $valid = true;
        $booking = new BookingForm();
        if(isset($_POST['BookingForm']))
        {
            $booking->attributes=$_POST['BookingForm'];
            $valid = $booking->validate() && $valid;
        }
        else
        {
            $valid = false;
        }

        $passport = new AviaPassportForm();
        if(isset($_POST['PassportForm']))
        {
            $passport->attributes=$_POST['PassportForm'];
            $valid = $valid && $passport->validate();
        }

        if($valid)
        {
            //saving data to objects
            $bookingAr = new Booking();

            $bookingAr->email = $booking->contactEmail;
            $bookingAr->phone = $booking->contactPhone;
            if (!Yii::app()->user->isGuest)
                $bookingAr->userId = Yii::app()->user->id;

            $bookingPassports = array();
            $bookingPassport = new BookingPassport();
            $bookingPassport->birthday = $passport->birthday;
            $bookingPassport->firstName = $passport->firstName;
            $bookingPassport->lastName = $passport->lastName;
            $bookingPassport->countryId = $passport->countryId;
            $bookingPassport->number = $passport->number;
            $bookingPassport->series = $passport->series;
            $bookingPassport->genderId = $passport->genderId;
            $bookingPassport->documentTypeId = $passport->documentTypeId;
            $bookingPassports[] = $bookingPassport;

            $bookingAr->bookingPassports = $bookingPassports;
            $bookingAr->flightId = Yii::app()->flightBooker->current->flightVoyage->flightKey;

            if($bookingAr->save())
            {
                Yii::app()->flightBooker->current->bookingId = $bookingAr->id;
                Yii::app()->flightBooker->status('booking');
                $this->getController()->refresh();
            }
            else
            {
                $this->getController()->render('flightBooker.views.enterCredentials', array('passport'=>$passport, 'booking'=>$booking));
            }
        }
        else
            $this->getController()->render('flightBooker.views.enterCredentials', array('passport'=>$passport, 'booking'=>$booking));
    }
}
