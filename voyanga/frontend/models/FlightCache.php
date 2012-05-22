<?php
/**
 * This is the model class for table "flight_cache". Class with information about one flight
 *
 * The followings are the available columns in table 'flight_cache':
 * @property integer $id
 * @property string $timestamp
 * @property integer $departureCityId
 * @property integer $arrivalCityId
 * @property string $departureDate
 * @property integer $adultCount
 * @property integer $childCount
 * @property integer $infantCount
 * @property integer $isBestPrice
 * @property integer $isBestTime
 * @property integer $isOptimal
 * @property integer $price
 * @property string $transportAirlines
 * @property integer $validationAirline
 * @property integer $duration
 * @property integer $flightSearchId
 * @property integer $withReturn
 */
class FlightCache extends CommonFlightCache
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return FlightCache the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function beforeSave()
    {
        parent::beforeSave();
        $dumper = new FlightCacheDumper();
        $dumper->model = $this;
        $dumper->save();
        return false;
    }

}