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

    public function actions()
    {
        return array(
#            'showTrip' => array('class'=>'site.common.modules.tour.actions.constructor.ShowTripAction'),
            'makeBooking' => array('class'=>'site.common.modules.tour.actions.constructor.MakeBookingAction'),
            'startPayment' => array('class'=>'site.common.modules.tour.actions.constructor.StartPaymentAction'),
            'getPayment' => array('class'=>'site.common.modules.tour.actions.constructor.GetPaymentAction'),
            'new' => array('class'=>'site.common.modules.tour.actions.constructor.NewAction'),
            'flightSearch' => array('class'=>'site.common.modules.tour.actions.constructor.FlightSearchAction'),
            'hotelSearch' => array('class'=>'site.common.modules.tour.actions.constructor.HotelSearchAction'),
            'showBasket' => array('class'=>'site.common.modules.tour.actions.constructor.ShowBasketAction'),
        );
    }

    public function actionIndex()
    {
        $this->layout = 'static';
        $this->addItems();
        $this->redirect('buy/makeBooking');
    }

    public function addItems()
    {
        Yii::app()->shoppingCart->clear();
        foreach ($_POST['item'] as $item)
        {
            if ($item['type']=='avia')
                $this->addFlightToTrip($item['searchKey'], $item['searchId']);
            if ($item['type']=='hotel')
                $this->addHotelToTrip($item['searchKey'], $item['searchId']);
        }
    }

    public function addFlightToTrip($searchKey, $searchId)
    {
        $flightSearchResult = Yii::app()->pCache->get('flightSearchResult' . $searchId);
        $flightSearchParams = Yii::app()->pCache->get('flightSearchParams' . $searchId);
        if (($flightSearchParams) and ($flightSearchResult))
        {
            foreach ($flightSearchResult->flightVoyages as $result)
            {
                if ($result->flightKey == $searchKey)
                    $this->addFlightTripElement($result, $flightSearchParams);
            }
        }
        else
            throw new CHttpException(500, 'Cache expired');
    }

    public function addHotelToTrip($searchKey, $searchId)
    {
        $hotelSearchResult = Yii::app()->pCache->get('hotelSearchResult' . $searchId);
        $hotelSearchParams = Yii::app()->pCache->get('hotelSearchParams' . $searchId);
        if (($hotelSearchParams) and ($hotelSearchResult))
        {
            foreach ($hotelSearchResult->hotels as $result)
            {
                if ($result->resultId == $searchKey)
                    $this->addHotelTripElement($result, $hotelSearchParams);
            }
        }
        else
            throw new CException(500, 'Cache expired');
    }

    public function addFlightTripElement($flight, FlightSearchParams $flightSearchParams)
    {
        $flightTripElement = new FlightTripElement();
        $key = md5(serialize($flightSearchParams));
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
        $flightTripElement->flightVoyage = $flight;
        Yii::app()->shoppingCart->put($flightTripElement);
    }

    public function addHotelTripElement($hotel, $hotelSearchParams)
    {
        $hotelTripElement = new HotelTripElement();
        $hotelTripElement->fillFromSearchParams($hotelSearchParams);
        $hotelTripElement->hotel = $hotel;
        Yii::app()->shoppingCart->put($hotelTripElement);
    }
}
