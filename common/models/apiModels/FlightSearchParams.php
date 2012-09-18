<?php
class FlightSearchParams extends CComponent
{
    public $routes;
    public $flight_class;
    public $adultCount;
    public $childCount = 0;
    public $infantCount = 0;
    private $key;

    public function addRoute($routeParams)
    {
        $route = new Route();
        if ($routeParams['departure_city_id'])
        {
            $route->departureCityId = $routeParams['departure_city_id'];
        }
        else
        {
            throw new CException(Yii::t('application', 'Required param departure_city_id not set'));
        }

        if ($routeParams['departure_city_id'])
        {
            $route->arrivalCityId = $routeParams['arrival_city_id'];
        }
        else
        {
            throw new CException(Yii::t('application', 'Required param arrival_city_id not set'));
        }
        if ($routeParams['departure_date'])
        {
            if (strpos($routeParams['departure_date'], '.') !== false)
            {
                list($dd, $mm, $yy) = explode('.', $routeParams['departure_date']);
            }
            elseif (strpos($routeParams['departure_date'], '-') !== false)
            {
                list($yy, $mm, $dd) = explode('.', $routeParams['departure_date']);
            }
            else
            {
                throw new CException(Yii::t('application', 'departure_date format invalid. Need dd.mm.yyyy or yyyy-mm-dd'));
            }
            if (!checkdate($mm, $dd, $yy))
            {
                throw new CException(Yii::t('application', 'departure_date parametr - date incorrect '));
            }
            if ($routeParams['adult_count'])
            {
                $route->adultCount = intval($routeParams['adult_count']);
                $this->adultCount = $route->adultCount;
            }
            if ($routeParams['child_count'])
            {
                $route->childCount = intval($routeParams['child_count']);
                $this->childCount = $route->childCount;
            }
            if ($routeParams['infant_count'])
            {
                $route->infantCount = intval($routeParams['infant_count']);
                $this->infantCount = $route->infantCount;
            }
            $route->departureDate = "{$yy}-{$mm}-{$dd}";
        }
        else
        {
            throw new CException(Yii::t('application', 'Required param departure_date not set'));
        }
        if (($route->adultCount + $route->childCount) <= 0)
        {
            throw new CException(Yii::t('application', 'Passengers count must be more then zero'));
        }
        if (($route->adultCount + $route->childCount) < $route->infantCount)
        {
            throw new CException(Yii::t('application', 'Infants count must be equal or less then (adult + child) count'));
        }
        $this->routes[] = $route;
    }

    public function getKey()
    {
        if (!$this->key)
        {
            $attributes = array();
            foreach ($this->routes as $route)
            {
                $attributes[] = $route->attributes;
            }
            $sKey = $this->flight_class . json_encode($attributes);
            $this->key = md5($sKey);
        }
        return $this->key;
    }

    public function checkValid()
    {
        $valid = ($this->flight_class) and (count($this->routes)>0);
        return $valid;
    }

    public function getJsonObject()
    {
        $jsonObject = array(
            'destinations' => $this->getRoutesJsonObject(),
            'adt' => $this->adultCount,
            'chd' => $this->childCount,
            'inf' => $this->infantCount,
            'serviceClass' => $this->flight_class,
            'isRoundTrip' => $this->isRoundTrip()
        );
        return $jsonObject;
    }

    private function getRoutesJsonObject()
    {
        $routes = array();
        foreach ($this->routes as $route)
        {
            $routeElement = $route->getJsonObject();
            $routes[] = $routeElement;
        }
        return $routes;
    }

    private function isRoundTrip()
    {
        if (sizeof($this->routes)==2)
        {
            $startCityFirst = $this->routes[0]->departureCityId;
            $endCityFirst = $this->routes[0]->arrivalCityId;
            $startCityLast = $this->routes[1]->departureCityId;
            $endCityLast = $this->routes[1]->arrivalCityId;
            if (($endCityFirst == $startCityLast) and ($endCityLast == $startCityFirst))
                return true;
        }
        return false;
    }
}