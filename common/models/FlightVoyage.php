<?php
/**
 * FlightVoyage class
 * Class with full flight marchroute
 * @author oleg
 *
 */
class FlightVoyage extends CApplicationComponent
{
    const TYPE = 1;
    const MASK_BEST_PRICE = 1;
    const MASK_BEST_TIME = 4;
    const MASK_BEST_PRICETIME = 2;

    public $price;
    public $taxes;
    public $flightKey;
    /** @var Airline */
    public $valAirline;
    public $commission;
    public $flights;
    public $adultPassengerInfo;
    public $childPassengerInfo;
    public $infantPassengerInfo;

    private $internalId;
    /**
     * @var int bitwise mask 0b001 - Best price, 0b010 - best recommended, 0b100 best speed
     */
    public $bestMask = 0;
    public $webService;
    /** @var boolean $refundable */
    public $refundable;
    public $searchKey;

    /**
     * @return mixed id
     */
    public function getId()
    {
        return 'flight_voyage_' . $this->searchKey . "_" . $this->flightKey;
    }

    /**
     * @return float price
     */
    public function getPrice()
    {
        return $this->price;
    }

    public function getIsValid()
    {
        $request = new GDSNemoAgency();
        return $request->checkFlight($this->flightKey);
    }

    public function getIsPayable()
    {
        return true;
    }

    public function saveToOrderDb($groupId = null)
    {
        $key = $this->getId();
        $order = OrderFlightVoyage::model()->findByAttributes(array('key' => $key));
        if (($order) && (sizeof($this->flights)==1))
        {
            //we try to save same flight
            $order->delete();
            $order = false;
        }
        if (!$order)
        {
            $order = new OrderFlightVoyage();
            $order->key = $key;
            $order->groupId = $groupId;
            $order->departureCity = $this->getDepartureCity(0)->id;
            $order->arrivalCity = $this->getArrivalCity(0)->id;
            $order->departureDate = $this->getDepartureDate(0);
            $order->object = serialize($this);
        }
        else
        {
            $order = new OrderFlightVoyage();
            $order->key = $key;
            $order->groupId = $groupId;
            $order->departureCity = $this->getDepartureCity(1)->id;
            $order->arrivalCity = $this->getArrivalCity(1)->id;
            $order->departureDate = $this->getDepartureDate(1);
            $order->object = serialize($this);
        }
        if ($order->save())
        {
            $this->internalId = $order->id;
            return $order;
        }
        return false;
    }

    public function saveReference($order)
    {
        $orderHasFlightVoyage = new OrderHasFlightVoyage();
        $orderHasFlightVoyage->orderId = $order->id;
        $orderHasFlightVoyage->orderFlightVoyage = $this->internalId;
        if (!$orderHasFlightVoyage->save())
            throw new CException(VarDumper::dumpAsString($this->attributes).VarDumper::dumpAsString($orderHasFlightVoyage->errors));
    }

    /**
     * @static
     * @param $searchId
     * @param $key
     * @return FlightVoyage
     */
    public static function getFromCache($searchId, $key)
    {
        //TODO: refactoring place that use this function
        $fs = Yii::app()->cache->get('flightSearch' . $searchId);
        if ($fs)
        {
            $item = $fs->flightVoyageStack->getFlightById($key);
            $item->searchKey = $searchId;
            return $item;
        }
        return false;
    }

    public function __construct($oParams)
    {
        $this->price = $oParams->full_sum;
        $this->taxes = $oParams->commission_price;
        $this->flightKey = $oParams->flight_key;
        $this->commission = $oParams->commission_price;
        $this->webService = $oParams->webService;
        $this->refundable = $oParams->refundable;
        $this->flights = array();
        //$this->searchKey = $oParams->searchId;
        if (!$this->valAirline)
        {
            $this->valAirline = $oParams->valAirline;
        }
        $iInd = 0;
        $lastArrTime = 0;
        $lastCityToId = 0;
        $bStart = true;
        if (isset($oParams->passengersInfo))
        {
            foreach ($oParams->passengersInfo as $passengerType => $passengerParams)
            {
                switch ($passengerType)
                {
                    case 'ADT':
                        $this->adultPassengerInfo = new PassengerInfo($passengerParams);
                        break;
                    case 'CNN':
                        $this->childPassengerInfo = new PassengerInfo($passengerParams);
                        break;
                    case 'INF':
                        $this->infantPassengerInfo = new PassengerInfo($passengerParams);
                        break;
                }
            }
        }
        if ($oParams->parts)
        {
            foreach ($oParams->parts as $iGroupId => $aParts)
            {
                $iIndPart = 0;
                $this->flights[$iGroupId] = new Flight();

                foreach ($aParts as $oPartParams)
                {
                    $oPart = new FlightPart($oPartParams);
                    $this->flights[$iGroupId]->addPart($oPart);
                }
            }
        }
        else
        {
            throw new CException(Yii::t('application', 'Required param $oParams->parts not set.'));
        }
    }

    public function getFullDuration()
    {
        $iFullDuration = 0;
        foreach ($this->flights as $oFlight)
        {
            $iFullDuration += $oFlight->fullDuration;
        }
        return $iFullDuration;
    }

    public function getFlightCodes()
    {
        $flightCodes = array();
        foreach ($this->flights as $flight)
        {
            foreach ($flight->flightParts as $part)
            {
                $flightCodes[] = $part->code;
            }
        }
        return $flightCodes;
    }

    public function setPriceInfo($priceInfo)
    {
        $this->fullPrice = $priceInfo['fullPrice'];
        $this->commissionPrice = $priceInfo['commissionPrice'];
        $this->profitPrice = $priceInfo['profitPrice'];
    }

    /**
     * @return City
     */
    public function getDepartureCity($ind=0)
    {
        return $this->flights[$ind]->getDepartureCity();
    }

    public function getDepartureDate($ind=0)
    {
        return $this->flights[$ind]->departureDate;
    }

    /**
     * @return City
     */
    public function getArrivalCity($ind=0)
    {
        return $this->flights[$ind]->getArrivalCity();
    }

    public function getArrivalDate($ind=0)
    {
        return $this->flights[$ind]->arrivalDate;
    }


    public function isComplex()
    {
        $countFlights = count($this->flights);
        if ($countFlights > 2)
            return true;
        if ($countFlights == 2)
        {
            $condition = ($this->flights[0]->getDepartureCity()->id == $this->flights[1]->getArrivalCity()->id);
            if (!$condition)
                return true;
        }
        return false;
    }

    public function isRoundTrip()
    {
        $countFlights = count($this->flights);
        if ($countFlights == 2)
        {
            $condition = ($this->flights[0]->getDepartureCity()->id == $this->flights[1]->getArrivalCity()->id);
            if ($condition)
                return true;
        }
        return false;
    }

    public function getTransportAirlines()
    {
        $airlines = array();
        foreach ($this->flights as $flight)
        {
            foreach ($flight->flightParts as $part)
            {
                $airlines[$part->opAirlineCode] = $part->transportAirlineCode;
            }
        }
        $return = implode(",", $airlines);
        return $return;
    }

    public function getJsonObject()
    {
        $ret = array(
            'key' => $this->getId(),
            'flightKey' => $this->flightKey,
            'price' => $this->price,
            'commission' => $this->commission,
            'taxes' => $this->taxes,
            'valCompany' => $this->valAirline->code,
            'valCompanyName' => $this->valAirline->localRu,
            'valCompanyNameEn' => $this->valAirline->localEn,
            'bestMask' => $this->bestMask,
            'refundable' => $this->refundable,
            'economFreeWeight' => $this->valAirline->economFreeWeight,
            'economDescription' => trim($this->valAirline->economDescription),
            'businessFreeWeight' => $this->valAirline->businessFreeWeight,
            'businessDescription' => trim($this->valAirline->businessDescription),
            'service' => trim($this->webService),
            'flights' => array(),
        );
        foreach ($this->flights as $flight)
        {
            $ret['flights'][] = $flight->getJsonObject();
        }
        return $ret;
    }

    public function getTime($departureCity = false)
    {
        if (!$departureCity)
            return strtotime(date('Y-m-d', $this->flights[0]->flightParts[0]->timestampBegin));
        else
        {
            foreach ($this->flights as $flight)
            {
                foreach($flight->flightParts as $flightPart)
                {
                    if ($flightPart->departureCityId == $departureCity)
                        return strtotime(date('Y-m-d', $flightPart->timestampBegin));
                }
            }
        }
        throw new CException('Cannot determine flight start time');
    }

    /**
     * Return array of passports. If no passport needs so it should return false. If we need passports but they not provided return an empty array.
     * Array = array of classes derived from BasePassportForm (e.g. BaseFlightPassportForm)
     *
     * @return PassengerPassportForm
     */
    public function getPassports()
    {

    }

    public function getWeight()
    {
        return 1;
    }
}