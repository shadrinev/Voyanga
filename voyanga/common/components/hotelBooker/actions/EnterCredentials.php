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
                }
            }
        }
        VarDumper::dump($form->attributes);
        if($valid)
        {
            /** @var HotelBookerComponent $hotelBookerComponent  */
            $hotelBookerComponent = Yii::app()->hotelBooker;
            $hotelBookerComponent->book();
            $hotelBookerId = $hotelBookerComponent->getHotelBookerId();
            foreach($form->attributes['roomsPassports'] as $i=>$room)
            {
                foreach($form->attributes['roomsPassports'][$i]['adultsPassports'] as $adultInfo)
                {
                    $hotelPassport = new HotelBookingPassport();
                    //VarDumper::dump($adultInfo);die();
                    $hotelPassport->attributes = $adultInfo->attributes;
                    $hotelPassport->hotelBookingId = $hotelBookerId;
                    $hotelPassport->roomKey = $i;
                    $hotelPassport->save();

                }
            }


        }

        $this->getController()->render('hotelBooker.views.enterCredentials', array('model'=>$form));
    }
}
