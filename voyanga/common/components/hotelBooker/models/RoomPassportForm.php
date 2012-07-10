<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 10.07.12
 * Time: 13:21
 */
class RoomPassportForm extends CFormModel
{
    /** @var HotelAdultPassportForm[] */
    public $adultsPassports = array();

    /** @var HotelChildPassportForm[] */
    public $childrenPassports = array();

    public function addPassports($adultCount, $childCount=0)
    {
        for ($i=0; $i<(int)$adultCount; $i++)
        {
            $this->adultsPassports[] = new HotelAdultPassportForm();
        }
        for ($i=0; $i<(int)$childCount; $i++)
        {
            $this->childrenPassports[] = new HotelChildPassportForm();
        }
    }
}
