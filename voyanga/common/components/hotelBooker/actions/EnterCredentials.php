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
        $rooms = Yii::app()->hotelBooker->getCurrent()->hotel->rooms;
        $form = new HotelPassportForm();
        foreach ($rooms as $room)
        {
            $form->addRoom($room->adults, $room->childCount);
        }
        if (isset($_POST['BookingForm']))
        {
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
        $this->getController()->render('hotelBooker.views.enterCredentials', array('model'=>$form));
    }
}
