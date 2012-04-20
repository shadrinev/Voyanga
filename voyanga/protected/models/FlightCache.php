<?php
/**
 * FlightCache class
 * Class with information about one flight
 * @author oleg
 *
 */
class FlightCache extends CActiveRecord {
    public $id;
    public $timestamp;
    public $departure_city_id;
    public $arrival_city_id;
    public $departure_date;
    public $adult_count;
    public $child_count;
    public $infant_count;
    public $cache_type;
    public $price;
    public $transport_airlines;
    public $airline_id;
    public $duration;
    public $flight_search_id;
    public $with_return;
    private $departure_city;
    private $arrival_city;
    
    
    
    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }
    
    /**
     * addCacheFromStack
     * Adding caches into db, flights with best paraments(price,time,pricetime)
     * @param FlightVoyageStack $oFlightVoyageStack
     */
    public static function addCacheFromStack(FlightVoyageStack $oFlightVoyageStack) {
        $aAttrs = array(
                'adult_count' => $oFlightVoyageStack->adult_count, 
                'child_count' => $oFlightVoyageStack->child_count, 
                'infant_count' => $oFlightVoyageStack->infant_count,
                'flight_search_id' => $oFlightVoyageStack->flight_search_id
        );
        
        if ( $oFlightVoyageStack->iBestPriceInd !== false ) {
            //saving to cache FlightVoyage with best price
            try {
                $oFlightCache = new FlightCache();
                $oFlightCache->setAttributes($aAttrs, false);
                $oFlightCache->setFromFlightVoyage($oFlightVoyageStack->aFlightVoyages[$oFlightVoyageStack->iBestPriceInd]);
                $oFlightCache->cache_type = 1;
                $oFlightCache->save();
            } catch (Exception $e) {
                new CException( Yii::t( 'application', 'Cant save FlightCache with best price: '.$e->getMessage() ) );
            }
            
        } elseif( ($oFlightVoyageStack->iBestTimeInd !== false) && ($oFlightVoyageStack->iBestTimeInd !== $oFlightVoyageStack->iBestPriceInd) ) {
            //saving to cache FlightVoyage with best time
            try {
                $oFlightCache = new FlightCache();
                $oFlightCache->setAttributes($aAttrs,false);
                $oFlightCache->setFromFlightVoyage($oFlightVoyageStack->aFlightVoyages[$oFlightVoyageStack->iBestTimeInd]);
                $oFlightCache->cache_type = 2;
                $oFlightCache->save();
            } catch (Exception $e) {
                new CException( Yii::t( 'application', 'Cant save FlightCache with best time: '.$e->getMessage() ) );
            }
        } elseif( ($oFlightVoyageStack->iBestPriceTimeInd !== false) && ($oFlightVoyageStack->iBestTimeInd !== $oFlightVoyageStack->iBestPriceInd) ) {
            //saving to cache FlightVoyage with best pricetime
            try {
                $oFlightCache = new FlightCache();
                $oFlightCache->setAttributes($aAttrs,false);
                $oFlightCache->setFromFlightVoyage($oFlightVoyageStack->aFlightVoyages[$oFlightVoyageStack->iBestTimeInd]);
                $oFlightCache->cache_type = 3;
                $oFlightCache->save();
            } catch (Exception $e) {
                new CException( Yii::t( 'application', 'Cant save FlightCachewith best pricetime: '.$e->getMessage() ) );
            }
        }
    }
    
    public function tableName() {
        return 'flight_cache';
    }
    
    /**
     * 
     * Set data from FlightVoyage object
     * @param FlightVoyage $oFlightVoyage
     * @throws CException
     */
    public function setFromFlightVoyage(FlightVoyage $oFlightVoyage) {
        if($oFlightVoyage instanceof FlightVoyage) {
            $this->departure_city_id = $oFlightVoyage->aFlights[0]->departure_city_id;
            $this->arrival_city_id = $oFlightVoyage->aFlights[0]->arrival_city_id;
            $this->departure_date = $oFlightVoyage->aFlights[0]->departure_date;
            $this->airline_id = $oFlightVoyage->airline_id;
            $this->price = $oFlightVoyage->price;
            $this->duration = $oFlightVoyage->getFullDuration();
            $this->with_return = count($oFlightVoyage->aFlights) == 2;
        } else {
            throw new CException( Yii::t( 'application', 'Required param type FlightVoyage' ) );
        }
    }
    
    public function __get( $name ) {
        if ( $name == 'departure_city' || $name == 'arrival_city' ) {
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
    }

}