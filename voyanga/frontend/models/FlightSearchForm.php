<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 12.05.12
 * Time: 11:27
 * To change this template use File | Settings | File Templates.
 */
class FlightSearchForm extends CFormModel
{
    public $departureCityId = 4466;
    public $departureCity;
    public $departureAirportCode;
    public $arrivalCityId = 5754;
    public $arrivalCity;
    public $arrivalAirportCode;
    public $adultCount = 1;
    public $childCount;
    public $infantCount;
    public $departureDate = '12.07.2012';

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // first_name, last_name, number, birthday, document_type_id, gender_id are required
            array(
                'departureCityId, arrivalCityId, departureDate',
                'required'),
            // email has to be a valid birthday format
            array(
                'departureDate',
                'date', 'format' => 'dd.MM.yyyy'),
            array('adultCount, childCount, infantCount, departureCity, arrivalCity','safe')
        );
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'departureCityId' => 'Код города отправления',
            'arrivalCityId' => 'Код города прибытия',
            'adultCount' => 'Количество взрослых',
            'childCount' => 'Количество детей',
            'infantCount' => 'Количество младенцев'
        );
    }


}
