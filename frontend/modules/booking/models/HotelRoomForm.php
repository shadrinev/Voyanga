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
    public $childAge=0;

    public function rules()
    {
        return array(
            array('adultCount, childCount, cots, childAge', 'numerical', 'integerOnly'=>true),
            array('adultCount', 'required'),
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
