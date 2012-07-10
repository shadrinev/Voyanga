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
        $roomsPassports->addPassports($adults, $children);
        $this->roomsPassports[] = $roomsPassports;
    }
}
