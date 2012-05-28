<?php
class FlightSearchParams
{
    public $routes;
    public $flight_class;
    public $adultCount;
    public $childCount;
    public $infantCount;

    public function addRoute($aRouteParams)
    {
        $oRoute = new Route();
        if ($aRouteParams['departure_city_id'])
        {
            $oRoute->departureCityId = $aRouteParams['departure_city_id'];
        }
        else
        {
            throw new CException(Yii::t('application', 'Required param departure_city_id not set'));
        }

        if ($aRouteParams['departure_city_id'])
        {
            $oRoute->arrivalCityId = $aRouteParams['arrival_city_id'];
        }
        else
        {
            throw new CException(Yii::t('application', 'Required param arrival_city_id not set'));
        }
        if ($aRouteParams['departure_date'])
        {
            if (strpos($aRouteParams['departure_date'], '.') !== false)
            {
                list($dd, $mm, $yy) = explode('.', $aRouteParams['departure_date']);
            }
            elseif (strpos($aRouteParams['departure_date'], '-') !== false)
            {
                list($yy, $mm, $dd) = explode('.', $aRouteParams['departure_date']);
            }
            else
            {
                throw new CException(Yii::t('application', 'departure_date format invalid. Need dd.mm.yyyy or yyyy-mm-dd'));
            }
            if (!checkdate($mm, $dd, $yy))
            {
                throw new CException(Yii::t('application', 'departure_date parametr - date incorrect '));
            }
            if ($aRouteParams['adult_count'])
            {
                $oRoute->adultCount = intval($aRouteParams['adult_count']);
                $this->adultCount = $oRoute->adultCount;
            }
            if ($aRouteParams['child_count'])
            {
                $oRoute->childCount = intval($aRouteParams['child_count']);
                $this->childCount = $oRoute->childCount;
            }
            if ($aRouteParams['infant_count'])
            {
                $oRoute->infantCount = intval($aRouteParams['infant_count']);
                $this->infantCount = $oRoute->infantCount;
            }
            $oRoute->departureDate = "{$yy}-{$mm}-{$dd}";
        }
        else
        {
            throw new CException(Yii::t('application', 'Required param departure_date not set'));
        }
        if (($oRoute->adultCount + $oRoute->childCount) <= 0)
        {
            throw new CException(Yii::t('application', 'Passengers count must be more then zero'));
        }
        if (($oRoute->adultCount + $oRoute->childCount) < $oRoute->infantCount)
        {
            throw new CException(Yii::t('application', 'Infants count must be equal or less then (adult + child) count'));
        }
        $this->routes[] = $oRoute;
    }

    public function __get($name)
    {
        if ($name === 'key')
        {
            $attributes = array();
            foreach ($this->routes as $route)
            {
                $attributes[] = $route->attributes;
            }

            $sKey = $this->flight_class . json_encode($attributes);
            return md5($sKey);
        }
        else
        {
            return $this->$name;
        }
    }

    public function checkValid()
    {
        $bValid = true;
        if (!$this->flight_class)
        {
            $bValid = false;
        }
        if (count($this->routes) <= 0)
        {
            $bValid = false;
        }
        return $bValid;
    }
}