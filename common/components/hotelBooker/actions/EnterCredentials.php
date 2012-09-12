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
        $valid = false;
        $rooms = Yii::app()->hotelBooker->getCurrent()->hotel->rooms;
        $form = new HotelPassportForm();
        foreach ($rooms as $room)
        {
            $form->addRoom($room->adults, $room->childCount);
        }
        if (isset($_POST['BookingForm']))
        {
            $valid = true;
            $form->bookingForm->attributes = $_POST['BookingForm'];
            $valid = $valid & $form->bookingForm->validate();
        }
        if (isset($_POST['HotelAdultPassportForm']))
        {
            foreach($_POST['HotelAdultPassportForm'] as $i=>$adults)
            {
                foreach ($adults as $j=>$adultInfo)
                {
                    $form->roomsPassports[$i]->adultsPassports[$j]->attributes = $adultInfo;
                    $valid = $valid & $form->roomsPassports[$i]->adultsPassports[$j]->validate();
                }
            }

        }
        if (isset($_POST['HotelChildPassportForm']))
        {
            foreach($_POST['HotelChildPassportForm'] as $i=>$children)
            {
                foreach ($children as $j=>$childrenInfo)
                {
                    $form->roomsPassports[$i]->childrenPassports[$j]->attributes = $childrenInfo;
                    $valid = $valid & $form->roomsPassports[$i]->childrenPassports[$j]->validate();
                }
            }
        }

        if ($valid)
        {
            /** @var HotelBookerComponent $hotelBookerComponent  */
            $hotelBookerComponent = Yii::app()->hotelBooker;
            $hotelBookerComponent->book();
            $hotelBookerId = $hotelBookerComponent->getHotelBookerId();
            //saving booking data
            /** @var BookingForm  */
            $bookingForm = $form->bookingForm;
            $bookingModel = new OrderBooking();
            $bookingModel->email = $bookingForm->contactEmail;
            $bookingModel->phone = $bookingForm->contactPhone;
            $bookingModel->timestamp = new CDbExpression('NOW()');
            $validSaving = $bookingModel->save();
            $errors = array();
            if ($validSaving)
            {
                $hotelBookerComponent->getCurrent()->orderBookingId = $bookingModel->id;
                $hotelBookerComponent->getCurrent()->save();
            }
            else
            {
                $errors = CMap::mergeArray($errors, $bookingModel->errors);
                Yii::trace(CVarDumper::dumpAsString($bookingModel->errors), 'HotelBooker.EnterCredentials.bookingModel');
            }
            foreach($form->roomsPassports as $i => $roomPassport)
            {
                foreach($roomPassport->adultsPassports as $adultInfo)
                {
                    $hotelPassport = new HotelBookingPassport();
                    $hotelPassport->scenario = 'adult';
                    $hotelPassport->attributes = $adultInfo->attributes;
                    $hotelPassport->hotelBookingId = $hotelBookerId;
                    $hotelPassport->roomKey = $i;
                    $validSaving = $validSaving and $hotelPassport->save();
                    $errors = CMap::mergeArray($errors, $hotelPassport->errors);
                    Yii::trace(CVarDumper::dumpAsString($hotelPassport->errors), 'HotelBooker.EnterCredentials.adultPassport');
                }
                foreach($roomPassport->childrenPassports as $childInfo)
                {
                    $hotelPassport = new HotelBookingPassport();
                    $hotelPassport->scenario = 'child';
                    $hotelPassport->attributes = $childInfo->attributes;
                    $hotelPassport->hotelBookingId = $hotelBookerId;
                    $hotelPassport->roomKey = $i;
                    $validSaving = $validSaving and $hotelPassport->save();
                    $errors = CMap::mergeArray($errors, $hotelPassport->errors);
                    Yii::trace(CVarDumper::dumpAsString($hotelPassport->errors), 'HotelBooker.EnterCredentials.childPassport');
                }
            }
            if ($validSaving)
            {
                $hotelBookerComponent->status('analyzing');
            }
            else
            {
                throw new CHttpException(500, 'Couldn\'t save passport records to db');
            }
        }

        $this->getController()->render('hotelBooker.views.enterCredentials', array('model'=>$form));
    }
}
