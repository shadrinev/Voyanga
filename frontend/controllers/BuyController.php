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
            'showTrip' => array('class'=>'site.common.modules.tour.actions.constructor.ShowTripAction'),
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
        $this->controller->redirect($this->controller->createUrl('showTrip'));
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
        CVarDumper::dump($flightSearchResult);
        if (($flightSearchParams) and ($flightSearchResult))
        {
            foreach ($flightSearchResult as $result)
            {
                if ($result['flightKey'] == $searchKey)
                    $this->addFlightTripElement($result, $flightSearchParams);
            }
            throw new CException(404, 'No item found');
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
            foreach ($hotelSearchResult as $result)
            {
                if ($result['resultId'] == $searchKey)
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
