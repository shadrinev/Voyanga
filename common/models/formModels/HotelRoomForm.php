<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.06.12
 * Time: 13:56
 */
class HotelRoomForm extends CFormModel
{
    public $adultCount=2;
    public $childCount=0;
    public $cots=0;
    public $childAges=array();

    public function rules()
    {
        return array(
            array('adultCount, childCount, cots', 'numerical', 'integerOnly'=>true),
            array('adultCount', 'required'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'adultCount' => 'Количество взрослых',
            'childCount' => 'Количество детей',
            'cots' => 'Количество люлек',
            'childAges' => 'Возраст ребёнка',
        );
    }
}
