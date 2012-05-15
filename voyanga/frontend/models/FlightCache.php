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
 * @property integer $cacheType
 * @property integer $price
 * @property string $transportAirlines
 * @property integer $validationAirline
 * @property integer $duration
 * @property integer $flightSearchId
 * @property integer $withReturn
 */
class FlightCache extends CActiveRecord {


    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return FlightCache the static model class
     */
    public static function model($className=__CLASS__)
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
            array('departureCityId, arrivalCityId, adultCount, childCount, infantCount, cacheType, price, validationAirline, duration, flightSearchId, withReturn', 'numerical', 'integerOnly'=>true),
            array('transportAirlines', 'length', 'max'=>45),
            array('timestamp, departureDate', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, timestamp, departureCityId, arrivalCityId, departureDate, adultCount, childCount, infantCount, cacheType, price, transportAirlines, validationAirline, duration, flightSearchId, withReturn', 'safe', 'on'=>'search'),
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
            'cacheType' => 'Cache Type',
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

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('timestamp',$this->timestamp,true);
        $criteria->compare('departureCityId',$this->departureCityId);
        $criteria->compare('arrivalCityId',$this->arrivalCityId);
        $criteria->compare('departureDate',$this->departureDate,true);
        $criteria->compare('adultCount',$this->adultCount);
        $criteria->compare('childCount',$this->childCount);
        $criteria->compare('infantCount',$this->infantCount);
        $criteria->compare('cacheType',$this->cacheType);
        $criteria->compare('price',$this->price);
        $criteria->compare('transportAirlines',$this->transportAirlines,true);
        $criteria->compare('validationAirline',$this->validationAirline);
        $criteria->compare('duration',$this->duration);
        $criteria->compare('flightSearchId',$this->flightSearchId);
        $criteria->compare('withReturn',$this->withReturn);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    /**
     * addCacheFromStack
     * Adding caches into db, flights with best paraments(price,time,pricetime)
     * @param FlightVoyageStack $oFlightVoyageStack
     */
    public static function addCacheFromStack(FlightVoyageStack $oFlightVoyageStack) {
        $attributes = array(
                'adultCount' => $oFlightVoyageStack->adult_count,
                'childCount' => $oFlightVoyageStack->child_count,
                'infantCount' => $oFlightVoyageStack->infant_count,
                'flightSearchId' => $oFlightVoyageStack->flight_search_id
        );
        echo "Try save all cache data";
        if ( $oFlightVoyageStack->iBestPriceInd !== false ) {
            echo "Best price ind: {$oFlightVoyageStack->iBestPriceInd}";
            //print_r($oFlightVoyageStack);
            //saving to cache FlightVoyage with best price
            try {echo "innnn";
                $oFlightCache = new FlightCache();
                $oFlightCache->setAttributes($attributes, false);
                $oFlightCache->setFromFlightVoyage($oFlightVoyageStack->flightVoyages[$oFlightVoyageStack->iBestPriceInd]);
                $oFlightCache->cacheType = 1;
                //echo "Try save ".print_r($oFlightCache,true);
                $oFlightCache->validate();
                echo CHtml::errorSummary($oFlightCache);
                $oFlightCache->save();
            } catch (Exception $e) {echo "innnn333".$e->getMessage();
                new CException( Yii::t( 'application', 'Cant save FlightCache with best price: '.$e->getMessage() ) );
            }
            
        } elseif( ($oFlightVoyageStack->iBestTimeInd !== false) && ($oFlightVoyageStack->iBestTimeInd !== $oFlightVoyageStack->iBestPriceInd) ) {
            //saving to cache FlightVoyage with best time
            echo "Best time ind: {$oFlightVoyageStack->iBestTimeInd}";
            try {
                $oFlightCache = new FlightCache();
                $oFlightCache->setAttributes($attributes,false);
                $oFlightCache->setFromFlightVoyage($oFlightVoyageStack->flightVoyages[$oFlightVoyageStack->iBestTimeInd]);
                $oFlightCache->cacheType = 2;
                $oFlightCache->save();
            } catch (Exception $e) {
                new CException( Yii::t( 'application', 'Cant save FlightCache with best time: '.$e->getMessage() ) );
            }
        } elseif( ($oFlightVoyageStack->iBestPriceTimeInd !== false) && ($oFlightVoyageStack->iBestTimeInd !== $oFlightVoyageStack->iBestPriceInd) ) {
            //saving to cache FlightVoyage with best pricetime
            try {
                $oFlightCache = new FlightCache();
                $oFlightCache->setAttributes($attributes,false);
                $oFlightCache->setFromFlightVoyage($oFlightVoyageStack->flightVoyages[$oFlightVoyageStack->iBestTimeInd]);
                $oFlightCache->cacheType = 3;
                $oFlightCache->save();
            } catch (Exception $e) {
                new CException( Yii::t( 'application', 'Cant save FlightCachewith best pricetime: '.$e->getMessage() ) );
            }
        }
    }
    
    /**
     * 
     * Set data from FlightVoyage object
     * @param FlightVoyage $oFlightVoyage
     * @throws CException
     */
    public function setFromFlightVoyage(FlightVoyage $oFlightVoyage) {
        if($oFlightVoyage instanceof FlightVoyage) {
            $this->departureCityId = $oFlightVoyage->flights[0]->departureCityId;
            $this->arrivalCityId = $oFlightVoyage->flights[0]->arrivalCityId;
            $this->departureDate = $oFlightVoyage->flights[0]->departureDate;
            $this->validationAirline = $oFlightVoyage->valAirline->id;
            //$this->validationAirline = $oFlightVoyage->valAirlineCode;
            $this->price = $oFlightVoyage->price;
            $this->duration = $oFlightVoyage->getFullDuration();
            $this->withReturn = (count($oFlightVoyage->flights) == 2)? 1 : 0;
        } else {
            throw new CException( Yii::t( 'application', 'Required param type FlightVoyage' ) );
        }
    }
    
    /*public function __get( $name ) {
        if ( $name == 'departureCity' || $name == 'arrivalCity' ) {
            if ( !$this->$name ) {
                $this->$name = City::model()->findByPk( $this->{$name . '_id'} );
                if ( !$this->$name ) throw new CException( Yii::t( 'application', '{var_name} not found. City with id {city_id} not set in db.', array(
                        '{var_name}' => $name, 
                        '{city_id}' => $this->{$name . '_id'} ) ) );
            }
            return $this->$name;
        } else {
            return $this->$name;
        }
    }*/

}