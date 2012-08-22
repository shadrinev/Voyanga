<?php
/**
 * Route class
 * Class for save and load routes for flight
 * @author oleg
 *
 * The followings are the available columns in table 'route':
 * @property integer $id
 * @property integer $searchId
 * @property integer $departureCityId
 * @property integer $departureAirportId
 * @property string  $departureDate
 * @property integer $arrivalCityId
 * @property integer $arrivalAirportId
 * @property integer $adultCount
 * @property integer $childCount
 * @property integer $infantCount
 *
 * The followings are the available model relations:
 * @property FlightSearch $search
 * @property City $departureCity
 * @property City $arrivalCity
 */
class Route extends CModel
{
    public $id;
    public $searchId;
    public $departureCityId;
    public $departureAirportId;
    public $departureDate;
    public $arrivalCityId;
    public $arrivalAirportId;
    public $adultCount;
    public $childCount;
    public $infantCount;


    //todo: make flight search working
    public function getDepartureCity()
    {
        return City::model()->getCityByPk($this->departureCityId);
    }

    public function getArrivalCity()
    {
        return City::model()->getCityByPk($this->arrivalCityId);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('searchId, departureCityId, departureAirportId, arrivalCityId, arrivalAirportId, adultCount, childCount, infantCount', 'numerical', 'integerOnly'=>true),
            array('departureDate', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, searchId, departureCityId, departureAirportId, departureDate, arrivalCityId, arrivalAirportId, adultCount, childCount, infantCount', 'safe', 'on'=>'search'),
        );
    }

     /**
     * Returns the list of attribute names of the model.
     * @return array list of attribute names.
     */
    public function attributeNames()
    {
        return array(
            'id',
            'searchId',
            'departureCityId',
            'departureAirportId',
            'departureDate',
            'arrivalCityId',
            'arrivalAirportId',
            'adultCount',
            'childCount',
            'infantCount',
        );
    }

    public function getJsonObject()
    {
        return array(
            'departure' => $this->departureCity->localRu,
            'arrival'   => $this->arrivalCity->localRu,
            'date'      => DateTimeHelper::formatForJs($this->departureDate)
        );
    }
}