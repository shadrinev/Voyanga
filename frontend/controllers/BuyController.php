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
            'makeBooking' => array('class'=>'site.common.modules.tour.actions.constructor.MakeBookingAction'),
            'makeBookingForItem' => array('class'=>'site.common.modules.tour.actions.constructor.MakeBookingForItemAction'),
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
        Yii::app()->user->setState('blockedToBook', null);
        $this->layout = 'main';
        $this->addItems();
        if (isset($_GET['item'][0]['module']))
            Yii::app()->user->setState('currentModule', $_GET['item'][0]['module']);
        else
            //todo: think what is default here
            Yii::app()->user->setState('currentModule', 'Tours');
        $this->redirect('buy/makeBooking');
    }

    public function actionOrder($id)
    {
        $secretKey = $id;
        $this->layout = 'static';
        $orderBooking = OrderBooking::model()->findByAttributes(array('secretKey'=>$secretKey));
        if (!$orderBooking)
            throw new CHttpException(404, 'Page not found');
        $tripStorage = new TripDataProvider($orderBooking->id);
        $trip = $tripStorage->getSortedCartItemsOnePerGroupAsJson();
        $orderId = Yii::app()->user->getState('orderBookingId');
        $readableOrderId = Yii::app()->user->getState('todayOrderId');
        $this->render('complete', array('trip'=>$trip, 'readableOrderId'=>$readableOrderId, 'orderId'=>$orderId));
    }

    public function addItems()
    {
        Yii::app()->shoppingCart->clear();
        foreach ($_GET['item'] as $i => $item)
        {
            if (!isset($item['type']))
                continue;
            if ($item['type']=='avia')
                $this->addFlightToTrip($item['searchKey'], $item['searchId']);
            if ($item['type']=='hotel')
                $this->addHotelToTrip($item['searchKey'], $item['searchId']);
        }
    }

    public function addFlightToTrip($searchKey, $searchId)
    {
        //to override igbinary_unserialize_long: 64bit long on 32bit platform
        $flightSearchResult = @Yii::app()->pCache->get('flightSearchResult' . $searchId);
        $flightSearchParams = @Yii::app()->pCache->get('flightSearchParams' . $searchId);
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
        //to override igbinary_unserialize_long: 64bit long on 32bit platform
        $hotelSearchResult = @Yii::app()->pCache->get('hotelSearchResult' . $searchId);
        $hotelSearchParams = @Yii::app()->pCache->get('hotelSearchParams' . $searchId);
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

    public function actionDone($ids)
    {
        $ids = explode(',', $ids);
        Yii::app()->order->setBookerIds($ids);
        CVarDumper::dump(Yii::app()->shoppingCart->getPositions()); die();
        Yii::app()->user->setState('blockedToBook', null);
    }

    public function actionWaitpayment()
    {
        $this->layout = false;
        $this->render('waiting');
    }
    public function actionPaymentstatus() {
        $order = Yii::app()->order;
        $payments = Yii::app()->payments;
        $bookers = $payments->preProcessBookers($order->getBookers());
        $paid = true;
        $error = false;
        foreach($bookers as $booker)
        {
            if($payments->getStatus($booker)!='paid'){
                $paid = false;
            }
        }
        header("Content-type: application/json");
        echo json_encode(Array("paid"=>$paid, "error"=>$error));
    }
}
