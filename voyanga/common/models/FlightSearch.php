<?php
/**
 * FlightSearch class
 * Class for making FlightSearch requesh with saving results into db
 * @author oleg
 *
 */
class FlightSearch extends CActiveRecord implements IStatisticItem
{
    public $id;
    public $timestamp;
    public $requestId;
    public $status;
    public $key;
    public $data;
    public $flight_class;
    public $flightVoyageStack;
    private $_routes;

    public function __construct($scenario = 'insert')
    {
        parent::__construct($scenario);

    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'flight_search';
    }

    public function sendRequest(FlightSearchParams $flightSearchParams)
    {
        if ($flightSearchParams instanceof FlightSearchParams)
        {
            if ($flightSearchParams->checkValid())
            {
                $this->_routes = $flightSearchParams->routes;
                $this->flight_class = $flightSearchParams->flight_class;
                $this->key = $flightSearchParams->key;
                if ($fs = Yii::app()->cache->get('flightSearch' . $this->key))
                {
                    $this->_routes = $fs->_aRoutes;
                    $this->flight_class = $fs->flight_class;
                    $this->key = $fs->key;
                    $this->id = $fs->id;
                    $this->timestamp = $fs->timestamp;
                    $this->data = $fs->data;
                    $this->requestId = $fs->requestId;
                    $this->flightVoyageStack = $fs->oFlightVoyageStack;
                    $this->status = $fs->status;
                    $this->afterSave();
                    return FlightCache::addCacheFromStack($this->flightVoyageStack);
                }

                //TODO: Making request to GDS
                //fill fields of object:
                //data
                //status
                //request_id
                $sJdata = Yii::app()->gdsAdapter->FlightSearch($flightSearchParams);
                if ($sJdata)
                {
                    $paramsFs['aFlights'] = $sJdata;
                    $flightVoyageStack = new FlightVoyageStack($paramsFs);

                    $this->flightVoyageStack = $flightVoyageStack;
                    Yii::app()->cache->set('flightSearch' . $this->key, $this, Yii::app()->params['fligh_search_cache_time']);

                    $this->status = 1;
                    $this->data = json_encode($this->flightVoyageStack);
                    $this->requestId = '1';
                }
                else
                    $this->status = 2;

                //$this->save();

                if ($this->flightVoyageStack)
                {
                    //saving best data to FlightCache
                    $attributes = array(
                        'adult_count' => $flightSearchParams->adultCount,
                        'child_count' => $flightSearchParams->childCount,
                        'infant_count' => $flightSearchParams->infantCount,
                        'flight_search_id' => $this->id
                    );
                    $this->flightVoyageStack->setAttributes($attributes);
                    $this->afterSave();
                    return FlightCache::addCacheFromStack($this->flightVoyageStack);
                }
            }
            else
            {
                throw new CException(Yii::t('application', 'Data in oFlightSearchParams not valid'));
            }
        }
        else
        {
            throw new CException(Yii::t('application', 'Parameter oFlightSearchParams not type of FlightSearchParams'));
        }
    }

    public function __get($name)
    {
        if ($name === 'aRoutes')
        {
            return $this->_routes;
        }
        else
        {
            return parent::__get($name);
        }
    }

    public function save($runValidation = true, $attributes = null)
    {
        if ($runValidation)
        {
            if ($this->_routes)
            {
                //check valid of Rotes
                //TODO: check good save
                parent::save();
                $this->id = $this->getPrimaryKey();
                $this->timestamp = date('Y-m-d H:i:s');
                foreach ($this->_routes as $route)
                {
                    $route->searchId = $this->id;
                    $route->save();
                    $route->id = $route->getPrimaryKey();
                }
            }
            else
            {
                throw new CException(Yii::t('application', 'Cant save FlightSearch without Routes'));
            }
        }
    }

    public function getStatisticData()
    {
        return array(
            ''
        );
    }
}