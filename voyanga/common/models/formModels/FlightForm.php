<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.06.12
 * Time: 13:56
 */
class FlightForm extends CFormModel
{
    const MAX_PASSENGER_NUMBER = 7;
    public $errorMaxPassenger;
    public $errorMaxInfantPassenger;

    /** @var RouteForm[] */
    public $routes = array();

    public $adultCount = 1;
    public $childCount = 0;
    public $infantCount = 0;

    public function init()
    {
        if ($this->errorMaxPassenger==null)
            $this->errorMaxPassenger = 'Количество пассажиров не больше '.self::MAX_PASSENGER_NUMBER.' человек';
        if ($this->errorMaxInfantPassenger==null)
            $this->errorMaxInfantPassenger = 'Количество младенцев не больше количества взрослых';
    }

    public function rules()
    {
        return array(
            array('adultCount, infantCount, childCount', 'numerical', 'integerOnly'=>true),
            array('adultCount, infantCount, childCount', 'required'),
            array('adultCount', 'checkPassengerCount'),
            array('infantCount', 'checkInfantCount'),
            array('infantCount, childCount', 'in', 'range'=>range(0, self::MAX_PASSENGER_NUMBER)),
            array('adultCount', 'in', 'range'=>range(1, self::MAX_PASSENGER_NUMBER))
        );
    }

    public function attributeLabels()
    {
        return array(
            'adultCount' => 'Количество взрослых',
            'childCount' => 'Количество детей старше 2-х лет',
            'infantCount' => 'Количество детей до 2-х лет',
        );
    }

    public function checkPassengerCount($attribute)
    {
        if ($this->adultCount + $this->childCount > self::MAX_PASSENGER_NUMBER)
        {
            $this->addError($attribute, $this->errorMaxPassenger);
            return false;
        }
        return true;
    }

    public function checkInfantCount($attribute)
    {
        if ($this->adultCount < $this->infantCount)
        {
            $this->addError($attribute, $this->errorMaxInfantPassenger);
            return false;
        }
        return true;
    }

    public function getPossibleAdultCount()
    {
        $range = range(1, FlightForm::MAX_PASSENGER_NUMBER);
        return array_combine($range, $range);
    }

    public function getPossibleChildCount()
    {
        $range = range(0, FlightForm::MAX_PASSENGER_NUMBER);
        return array_combine($range, $range);
    }

    public function getPossibleInfantCount()
    {
        $range = range(0, FlightForm::MAX_PASSENGER_NUMBER);
        return array_combine($range, $range);
    }
}
