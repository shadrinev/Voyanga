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
class FlightCache extends CActiveRecord
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

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'flight_cache';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('departureCityId, arrivalCityId, adultCount, childCount, infantCount, isBestTime, isBestPrice, isOptimal, price, validationAirline, duration, flightSearchId, withReturn', 'numerical', 'integerOnly' => true),
            array('transportAirlines', 'length', 'max' => 45),
            array('timestamp, departureDate', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, timestamp, departureCityId, arrivalCityId, departureDate, returnDate, adultCount, childCount, infantCount, isBestTime, isBestPrice, isOptimal, price, transportAirlines, validationAirline, duration, flightSearchId, withReturn', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'timestamp' => 'Timestamp',
            'departureCityId' => 'Departure City',
            'arrivalCityId' => 'Arrival City',
            'departureDate' => 'Departure Date',
            'adultCount' => 'Adult Count',
            'childCount' => 'Child Count',
            'infantCount' => 'Infant Count',
            'price' => 'Price',
            'transportAirlines' => 'Transport Airlines',
            'validationAirline' => 'Validation Airline',
            'duration' => 'Duration',
            'flightSearchId' => 'Flight Search',
            'withReturn' => 'With Return',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('timestamp', $this->timestamp, true);
        $criteria->compare('departureCityId', $this->departureCityId);
        $criteria->compare('arrivalCityId', $this->arrivalCityId);
        $criteria->compare('departureDate', $this->departureDate, true);
        $criteria->compare('adultCount', $this->adultCount);
        $criteria->compare('childCount', $this->childCount);
        $criteria->compare('infantCount', $this->infantCount);
        $criteria->compare('price', $this->price);
        $criteria->compare('transportAirlines', $this->transportAirlines, true);
        $criteria->compare('validationAirline', $this->validationAirline);
        $criteria->compare('duration', $this->duration);
        $criteria->compare('flightSearchId', $this->flightSearchId);
        $criteria->compare('withReturn', $this->withReturn);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * addCacheFromStack
     * Adding caches into db, flights with best paraments(price,time,pricetime)
     * @param FlightVoyageStack $flightVoyageStack
     */
    public static function addCacheFromStack(FlightVoyageStack $flightVoyageStack)
    {
        $attributes = array(
            'adultCount' => $flightVoyageStack->adult_count,
            'childCount' => $flightVoyageStack->child_count,
            'infantCount' => $flightVoyageStack->infant_count,
            'flightSearchId' => $flightVoyageStack->flight_search_id
        );
        $ind = array_unique(array($flightVoyageStack->bestPriceInd, $flightVoyageStack->bestTimeInd, $flightVoyageStack->bestPriceTimeInd));
        foreach ($ind as $i)
        {
            if ($i !== null)
            {
                $flightCache = new FlightCache();
                $flightCache->setAttributes($attributes, false);
                $flightCache->setFromFlightVoyage($flightVoyageStack->flightVoyages[$i]);

                if ($flightVoyageStack->bestPriceInd == $i)
                     $flightCache->isBestPrice = 1;

                if ($flightVoyageStack->bestTimeInd == $i)
                    $flightCache->isBestTime = 1;

                if ($flightVoyageStack->bestPriceTimeInd == $i)
                    $flightCache->isOptimal = 1;

                if (!$flightCache->validate())
                {
                    throw new CException("Can't save fligh cache item.".CVarDumper::dump($flightCache->errors));
                }
                $flightCache->save();
            }
        }
    }

    /**
     *
     * Set data from FlightVoyage object
     * @param FlightVoyage $oFlightVoyage
     * @throws CException
     */
    public function setFromFlightVoyage(FlightVoyage $oFlightVoyage)
    {
        if ($oFlightVoyage instanceof FlightVoyage)
        {
            $this->departureCityId = $oFlightVoyage->flights[0]->departureCityId;
            $this->arrivalCityId = $oFlightVoyage->flights[0]->arrivalCityId;
            $this->departureDate = $oFlightVoyage->flights[0]->departureDate;
            $this->validationAirline = $oFlightVoyage->valAirline->id;
            $this->price = $oFlightVoyage->price;
            $this->duration = $oFlightVoyage->getFullDuration();
            $this->withReturn = (count($oFlightVoyage->flights) == 2) ? 1 : 0;
            if ($this->withReturn)
                $this->returnDate = $oFlightVoyage->flights[1]->departureDate;
        }
        else
        {
            throw new CException(Yii::t('application', 'Required param type FlightVoyage'));
        }
    }
}