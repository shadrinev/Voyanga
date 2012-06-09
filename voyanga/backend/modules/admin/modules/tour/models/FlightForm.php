<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.06.12
 * Time: 13:56
 */
class FlightForm extends CFormModel
{
    public $departureCityId;
    public $departureDate;
    public $arrivalCityId;

    public function attributeLabels()
    {
        return array(
            'departureCityId' => 'Город отправления',
            'arrivalCityId' => 'Город прибытия',
            'departureDate' => 'Дата отправления',
        );
    }
}
