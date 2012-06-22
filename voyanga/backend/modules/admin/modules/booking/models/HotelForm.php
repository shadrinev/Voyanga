<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.06.12
 * Time: 13:56
 */
class HotelForm extends CFormModel
{
    public $cityId;
    public $fromDate;
    public $duration;
    public $rooms=array();

    public function rules()
    {
        return array(
            array('cityId, duration', 'numerical', 'integerOnly'=>true),
            array('fromDate, rooms', 'safe')
        );
    }

    public function attributeLabels()
    {
        return array(
            'cityId' => 'Город',
            'fromDate' => 'Дата заселения',
            'duration' => 'Количество ночей',
        );
    }
}
