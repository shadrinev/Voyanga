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
        $this->cityModel = City::getCityByCode($value);
        /*$items = CityManager::getCities($value);
        if (isset($items[0]))
            $this->cityModel = City::model()->findByPk($items[0]['id']);
        else
            throw new CException('Cannot define city by city name:'.$value);*/
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
