<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 22.06.12
 * Time: 11:51
 * To change this template use File | Settings | File Templates.
 */
class HotelSearchForm extends CFormModel
{
    public $stayCityId = 4466;
    public $stayCity;
    public $checkIn;
    public $duration;
    public $rooms;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // first_name, last_name, number, birthday, document_type_id, gender_id are required
            array(
                'stayCityId, checkIn, duration',
                'required'),
            // email has to be a valid birthday format
            array(
                'departureDate, returnDate',
                'date', 'format' => 'dd.MM.yyyy'),
            array('adultCount, childCount, infantCount, departureCity, arrivalCity, returnDate','safe')
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
