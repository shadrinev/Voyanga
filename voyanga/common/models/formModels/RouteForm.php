<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 17.07.12
 * Time: 10:10
 */
class RouteForm extends CFormModel
{
    public $departureDate;
    public $departureCityId;
    public $arrivalCityId;
    public $isRoundTrip = false;

    public function rules()
    {
        return array(
            array('departureCityId, arrivalCityId', 'numerical', 'integerOnly'=>true),
            array('departureCityId, arrivalCityId', 'required'),
            array('departureDate', 'required'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'departureCityId' => 'Город отправления',
            'arrivalCityId' => 'Город прибытия',
            'departureDate' => 'Дата отправления',
        );
    }
}
