<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 17.10.12
 * Time: 14:21
 */
class BuyController extends Controller
{
    private $keys;

    public function actionIndex()
    {
        $this->layout = 'static';
        $this->addItems();
        //$this->render('index');
    }

    public function addItems()
    {
        foreach ($_POST['item'] as $item)
        {
            if ($item['type']=='avia')
                $this->addFlightToTrip($item['searchId'], $item['searchKey']);
            if ($item['type']=='hotel')
                $this->addHotelToTrip($item['searchId'], $item['searchKey']);
        }
    }

    public function addFlightToTrip($searchKey, $searchId)
    {
        $flightSearchResult = Yii::app()->cache->get('flightSearchResult' . $searchId);
        $flightSearchParams = Yii::app()->cache->get('flightSearchParams' . $searchId);
        if ($flightSearchParams and $flightSearchResult)
        {
            $this->results = $flightSearchResult;
            if (!$this->results)
            {
                throw new CException(500, 'Error while send Request To Hotel Provider');
                Yii::app()->end();
            }
            foreach ($this->results as $result)
            {
                if ($result->flightKey == $searchKey)
                    $this->addFlighTripElement($result, $flightSearchParams);
            }
            throw new CException(404, 'No item found');
        }
        throw new CException(500, 'Cache expired');
    }

    public function addHotelToTrip($searchKey, $searchId)
    {
        $hotelSearchResult = Yii::app()->cache->get('hotelSearchResult' . $searchId);
        $hotelSearchParams = Yii::app()->cache->get('hotelSearchParams' . $searchId);
        if ($hotelSearchParams and $hotelSearchResult)
        {
            $this->results = $hotelSearchResult;
            if (!$this->results)
            {
                throw new CException(500, 'Error while send Request To Hotel Provider');
                Yii::app()->end();
            }
            foreach ($this->results as $result)
            {
                if ($result->resultId == $searchKey)
                    $this->addHotelTripElement($result, $hotelSearchParams);
            }
            throw new CException(404, 'No item found');
        }
        throw new CException(500, 'Cache expired');
    }

    public function addFlightTripElement(FlightSearchParams $flightSearchParams)
    {
        $flightTripElement = new FlightTripElement();
        $key = md5($flightSearchParams);
        if (!isset($this->keys[$key]))
            $this->keys[$key] = 0;
        if ($this->keys[$key]==1)
        {
            $flightTripElement->fillFromSearchParams($flightSearchParams, true);
        }
        else
            $flightTripElement->fillFromSearchParams($flightSearchParams, false);
        if ($flightSearchParams->isRoundTrip())
        {
            $this->keys[$key] = 1;
            $flightTripElement->setGroupId($key);
        }
        $flightTripElement->hotel = $flightTripElement;
        Yii::app()->shoppingCart->put($flightTripElement);
    }

    public function addHotelTripElement($hotelSearchParams)
    {
        $hotelTripElement = new HotelTripElement();
        $hotelTripElement->fillFromSearchParams($hotelSearchParams);
        $hotelTripElement->hotel = $hotelTripElement;
        Yii::app()->shoppingCart->put($hotelTripElement);
    }
}
