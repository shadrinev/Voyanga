<?php Yii::import('site.common.modules.tour.models.*'); ?>
<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 13.06.12
 * Time: 14:41
 */
class BasketController extends Controller
{
    public $orderId;

    public function actionAdd($type, $key, $searchId = '', $searchId2 = '', $pCacheId='')
    {
        switch ($type)
        {
            case FlightVoyage::TYPE:
                /** @var $flight FlightVoyage */
                $flight = FlightVoyage::getFromCache($key, $searchId);
                $flightSearchParams = @Yii::app()->pCache->get('flightSearchParams' . $pCacheId);
                if ($flight)
                {
                    $id = time();
                    //todo: add count of flightVoyageFlights Items
                    foreach ($flight->flights as $flightElement)
                    {
                        $item = new FlightTripElement();
                        $item->fillFromSearchParams($flightSearchParams);
                        $item->flightVoyage = $flight;
                        $item->groupId = $flight->getId();
                        $item->id = $id++;
                        Yii::app()->shoppingCart->put($item);
                    }
                }
                else
                    throw new CHttpException(404, 'Can\'t found item inside cache');
                break;
            case Hotel::TYPE:
                /** @var $hotel Hotel */
                $hotel = Hotel::getFromCache($key, $searchId, $searchId2);
                $hotelSearchParams = @Yii::app()->pCache->get('hotelSearchParams' . $pCacheId);
                if ($hotel)
                {
                    $item = new HotelTripElement();
                    $item->fillFromSearchParams($hotelSearchParams);
                    $item->hotel = $hotel;
                    $item->id = time();
                    Yii::app()->shoppingCart->put($item);
                }
                else
                    throw new CHttpException(404, 'Can\'t found item inside cache');
                break;
        }
    }

    public function actionFillCartElement($cartElementId, $type, $key, $searchId = '', $searchId2 = '')
    {
        $dataProvider = new TripDataProvider();
        $allPositions = $dataProvider->getSortedCartItems();
        $needPosition = null;
        foreach ($allPositions as $item)
        {
            if ($item->getId() == $cartElementId)
            {
                $needPosition = $item;
                break;
            }
        }
        if ($needPosition)
        {
            switch ($type)
            {
                case FlightVoyage::TYPE:
                    $needPositions = array();

                    $groupId = $needPosition->getGroupId();
                    foreach ($allPositions as $item)
                    {
                        if ($item->getGroupId())
                        {
                            if ($item->getGroupId() == $groupId)
                            {
                                $needPositions[] = $item;
                            }
                        }
                    }
                    /** @var $flight FlightVoyage */
                    $flight = FlightVoyage::getFromCache($searchId, $key);
                    if ($flight)
                    {
                        //updating all cartElements
                        foreach ($needPositions as $item)
                        {
                            $item->flightVoyage = $flight;
                            Yii::app()->shoppingCart->update($item, 1);
                        }
                        $json = CJSON::encode($flight->getJsonObject());
                        if (isset($_GET['callback']))
                            echo $_GET['callback'] . ' (' . $json . ');';
                        else
                            echo $json;
                    }
                    else
                        throw new CHttpException(404, 'Can\'t found item inside cache key:' . $key . ' searchId:' . $searchId);
                    break;
                case Hotel::TYPE:
                    /** @var $hotel Hotel */
                    $hotel = Hotel::getFromCache($searchId, null, $key);
                    if ($hotel)
                    {
                        //$needPosition = new HotelTripElement();
                        $needPosition->hotel = $hotel;
                        Yii::app()->shoppingCart->update($needPosition, 1);
                        $json = CJSON::encode($hotel->getJsonObject());
                        if (isset($_GET['callback']))
                            echo $_GET['callback'] . ' (' . $json . ');';
                        else
                            echo $json;
                    }
                    else
                        throw new CHttpException(404, 'Can\'t found item inside cache');
                    break;
            }
        }
    }

    public function actionDelete($key)
    {
        Yii::app()->shoppingCart->remove($key);
        $this->actionShow();
    }

    public function actionShow($orderId = false)
    {
        $this->orderId = $orderId;
        $this->prepareData();
        $dataProvider = new TripDataProvider();
        echo $dataProvider->getSortedCartItemsOnePerGroupAsJson();
    }

    public function actionSave($name)
    {
        $tripStorage = new TripStorage();
        $tripStorage->saveOrder($name);
    }

    public function actionClear()
    {
        Yii::app()->shoppingCart->clear();
        if (!Yii::app()->request->isAjaxRequest)
            $this->redirect('/tour/constructor/new');
        else
            $this->actionShow();
    }

    public function prepareData()
    {
        if (!$this->orderId)
            return;
        Yii::app()->shoppingCart->clear();
        $order = Order::model()->findByPk($this->orderId);
        $items = $order->flightItems();
        foreach ($items as $item)
        {
            $flightTripElement = new FlightTripElement();
            $flightTripElement->departureDate = $item->departureDate;
            $flightTripElement->departureCity = $item->departureCity;
            $flightTripElement->arrivalCity = $item->arrivalCity;
            $object = @unserialize($item->object);
            if ($object)
            {
                $flightTripElement->flightVoyage = $object;
            }
            Yii::app()->shoppingCart->put($flightTripElement);
        }
        $items = $order->hotelItems();
        foreach ($items as $item)
        {
            $hotelTripElement = new HotelTripElement();
            $city = City::model()->findByPk($item->cityId);
            $hotelTripElement->city = $city;
            $hotelTripElement->checkIn = $item->checkIn;
            $object = @unserialize($item->object);
            if ($object)
            {
                $hotelTripElement->hotel = $object;
            }
            Yii::app()->shoppingCart->put($hotelTripElement);
        }
    }
}