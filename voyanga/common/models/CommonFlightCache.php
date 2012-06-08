<?php
/**
 * This is the model class for table "flight_cache".
 *
 * The followings are the available columns in table 'flight_cache':
 * @property integer $from
 * @property integer $to
 * @property string $dateFrom
 * @property string $dateBack
 * @property integer $priceBestPrice
 * @property integer $durationBestPrice
 * @property integer $validatorBestPrice
 * @property integer $transportBestPrice
 * @property integer $priceBestTime
 * @property integer $durationBestTime
 * @property integer $validatorBestTime
 * @property integer $transportBestTime
 * @property integer $priceBestPriceTime
 * @property integer $durationBestPriceTime
 * @property integer $validatorBestPriceTime
 * @property integer $transportBestPriceTime
 */
class CommonFlightCache extends CActiveRecord
{
    public function beforeSave()
    {
        return parent::beforeSave();
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
            array('from, to, priceBestPrice, durationBestPrice, priceBestTime, durationBestTime, priceBestPriceTime, durationBestPriceTime', 'numerical', 'integerOnly'=>false),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('from, to, dateFrom, dateBack, priceBestPrice, durationBestPrice,  validatorBestPrice, transportBestPrice,  validatorBestPriceTime, transportBestPriceTime, validatorBestTime, transportBestTime, validatorBestPrice, transportBestPrice, priceBestTime, durationBestTime, validatorBestTime, transportBestTime, priceBestPriceTime, durationBestPriceTime, validatorBestPriceTime, transportBestPriceTime', 'safe', 'on'=>'search'),
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
        $flightCache = new FlightCache;

        $firstVoyage = $flightVoyageStack->flightVoyages[0];

        //we aren't saving complex voyage
        if ($firstVoyage->isComplex())
            return;

        $withReturn = (count($firstVoyage->flights) == 2);

        //working on dates
        $flightCache->dateFrom = $firstVoyage->flights[0]->departureDate;
        if ($withReturn)
            $flightCache->dateBack = $firstVoyage->flights[1]->departureDate;

        //working on from and to cities
        $flightCache->from = $firstVoyage->getDepartureCity()->id;
        $flightCache->to   = $firstVoyage->getArrivalCity()->id;

        if ($flightVoyageStack->bestPriceInd !== null)
        {
            $voyage = $flightVoyageStack->flightVoyages[$flightVoyageStack->bestPriceInd];
            $flightCache->setFromFlightVoyage($voyage, 'BestPrice');
        }

        if ($flightVoyageStack->bestTimeInd !== null)
        {
            $voyage = $flightVoyageStack->flightVoyages[$flightVoyageStack->bestPriceInd];
            $flightCache->setFromFlightVoyage($voyage, 'BestTime');
        }

        if ($flightVoyageStack->bestPriceTimeInd !== null)
        {
            $voyage = $flightVoyageStack->flightVoyages[$flightVoyageStack->bestPriceInd];
            $flightCache->setFromFlightVoyage($voyage, 'BestPriceTime');
        }

        if (!$flightCache->validate())
        {
            throw new CException("Can't save fligh cache item.".CVarDumper::dump($flightCache->errors));
        }
        $flightCache->save();
        return $flightCache;
    }

    /**
     *
     * Set data from FlightVoyage object
     * @param FlightVoyage $flightVoyage
     * @throws CException
     */
    public function setFromFlightVoyage(FlightVoyage $flightVoyage, $suffix)
    {
        if ($flightVoyage instanceof FlightVoyage)
        {
            $priceAttribute = "price".$suffix;
            $transportAttribute = "transport".$suffix;
            $validatorAttribute = "validator".$suffix;
            $durationAttribute = "duration".$suffix;

            $this->$priceAttribute = $flightVoyage->price;
            $this->$transportAttribute = $flightVoyage->getTransportAirlines();
            $this->$validatorAttribute = $flightVoyage->valAirline->code;
            $this->$durationAttribute = $flightVoyage->getFullDuration();
        }
        else
        {
            throw new CException(Yii::t('application', 'Required param type FlightVoyage'));
        }
    }

    public function buildQuery()
    {
        $currentSql = '';
        if ($this->isNewRecord)
            $currentSql = "`createdAt` = NOW(), ";

        $query = "INSERT INTO ".$this->tableName()." SET "
            ."`dateFrom` = '".$this->dateFrom."', "
            ."`dateBack` = '".$this->dateBack."', "
            ."`from` = '".$this->from."', "
            ."`to` = '".$this->to."', "
            ."`priceBestPrice` = '".$this->priceBestPrice."', "
            ."`transportBestPrice` = '".$this->transportBestPrice."', "
            ."`validatorBestPrice` = '".$this->validatorBestPrice."', "
            ."`durationBestPrice` = '".$this->durationBestPrice."', "
            ."`priceBestTime` = '".$this->priceBestTime."', "
            ."`transportBestTime` = '".$this->transportBestTime."', "
            ."`validatorBestTime` = '".$this->validatorBestTime."', "
            ."`durationBestTime` = '".$this->durationBestTime."', "
            ."`priceBestPriceTime` = '".$this->priceBestPriceTime."', "
            ."`transportBestPriceTime` = '".$this->transportBestPriceTime."', "
            ."`validatorBestPriceTime` = '".$this->validatorBestPriceTime."', "
            ."`durationBestPriceTime` = '".$this->durationBestPriceTime."', "
            .$currentSql
            ."`updatedAt` = NOW() "
            ." ON DUPLICATE KEY UPDATE "
            ."`priceBestPrice` = '".$this->priceBestPrice."', "
            ."`transportBestPrice` = '".$this->transportBestPrice."', "
            ."`validatorBestPrice` = '".$this->validatorBestPrice."', "
            ."`durationBestPrice` = '".$this->durationBestPrice."', "
            ."`priceBestTime` = '".$this->priceBestTime."', "
            ."`transportBestTime` = '".$this->transportBestTime."', "
            ."`validatorBestTime` = '".$this->validatorBestTime."', "
            ."`durationBestTime` = '".$this->durationBestTime."', "
            ."`priceBestPriceTime` = '".$this->priceBestPriceTime."', "
            ."`transportBestPriceTime` = '".$this->transportBestPriceTime."', "
            ."`validatorBestPriceTime` = '".$this->validatorBestPriceTime."', "
            ."`durationBestPriceTime` = '".$this->durationBestPriceTime."', "
            ."`updatedAt` = NOW() "
            .";";
        return $query;
    }

    public function buildRow()
    {
        $attributes = $this->attributes;
        $row = implode(',', $attributes)."\n";
        return $row;
    }

    public function forceSave()
    {
        $query = $this->buildQuery();
        $command = Yii::app()->db->createCommand($query);
        $command->execute();
    }
}