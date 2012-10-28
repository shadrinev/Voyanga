<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 10.07.12
 * Time: 13:21
 */
class HotelPassportForm extends CFormModel
{
    /** @var RoomPassportForm[] */
    public $roomsPassports = array();

    /** @var BookingForm */
    public $bookingForm;

    public function init()
    {
        $this->bookingForm = new BookingForm();
    }

    public function addRoom($adults, $children)
    {
        $roomsPassports = new RoomPassportForm();
        $roomsPassports->addPassports(sizeof($adults), sizeof($children));
        foreach ($roomsPassports->adultsPassports as $i => $adultsPassports)
        {
            $adultsPassports->attributes = $adults[$i]->attributes;
        }
        foreach ($roomsPassports->childrenPassports as $i => $childPassport)
        {
            $childPassport->attributes = $children[$i]->attributes;
        }
        $this->roomsPassports[] = $roomsPassports;
    }
}
