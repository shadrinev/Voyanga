<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 24.07.12
 * Time: 12:26
 *
 * @property integer cityId
 * @property string cityName
 */
class TripForm extends CFormModel
{
    private $cityModel;

    public $startDate;
    public $endDate;

    public function rules()
    {
        return array(
            array('startDate, endDate, cityId', 'required')
        );
    }

    public function getCityId()
    {
        if ($this->cityModel)
            return $this->cityModel->id;
        return null;
    }

    public function setCityId($value)
    {
        $this->cityModel = City::model()->getCityByPk($value);
    }

    public function getCityName()
    {
        if ($this->cityModel)
            return $this->cityModel->localRu;
        return null;
    }

    public function setCityName($value)
    {
        $items = City::model()->guess($value);
        $this->cityModel = $items[0];
    }

    public function attributeLabels()
    {
        return array(
            'cityId' => 'Город',
            'startDate' => 'Начало посещения',
            'endDate' => 'Окончание посещения',
        );
    }
}
