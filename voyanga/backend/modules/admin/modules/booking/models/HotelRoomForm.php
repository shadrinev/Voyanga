<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.06.12
 * Time: 13:56
 */
class HotelRoomForm extends CFormModel
{
    public $adultCount;
    public $childCount;
    public $cots;
    public $childAge;

    public function rules()
    {
        return array(
            array('adultCount, childCount, cots, childAge', 'numerical', 'integerOnly'=>true),
        );
    }

    public function attributeLabels()
    {
        return array(
            'adultCount' => 'Количество взрослых',
            'childCount' => 'Количество детей',
            'cots' => 'Количество люлек',
            'childAge' => 'Возраст ребёнка',
        );
    }
}
