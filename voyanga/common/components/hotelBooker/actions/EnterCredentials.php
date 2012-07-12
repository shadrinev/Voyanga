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

        if($valid)
        {
            /** @var HotelBookerComponent $hotelBookerComponent  */
            $hotelBookerComponent = Yii::app()->hotelBooker;
            $hotelBookerComponent->book();
            $hotelBookerId = $hotelBookerComponent->getHotelBookerId();
            $validSaving = true;
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
                }
                foreach($roomPassport->childrenPassports as $childInfo)
                {
                    $hotelPassport = new HotelBookingPassport();
                    $hotelPassport->scenario = 'child';
                    $hotelPassport->attributes = $childInfo->attributes;
                    $hotelPassport->hotelBookingId = $hotelBookerId;
                    $hotelPassport->roomKey = $i;
                    $validSaving = $validSaving and $hotelPassport->save();
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
