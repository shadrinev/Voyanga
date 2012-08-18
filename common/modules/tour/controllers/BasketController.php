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

    public function actionAdd($type, $key, $searchId='', $searchId2='')
    {
        switch ($type)
        {
            case FlightVoyage::TYPE:
                /** @var $flight FlightVoyage */
                $flight = FlightVoyage::getFromCache($key, $searchId);
                if ($flight)
                {
                    $id = time();
                    //todo: add count of flightVoyageFlights Items
                    foreach($flight->flights as $flightElement){
                        $item = new FlightTripElement();
                        $item->flightVoyage = $flight;
                        $item->departureCity = $flightElement->getDepartureCity()->id;
                        $item->arrivalCity = $flightElement->getArrivalCity()->id;
                        $item->departureDate = date('d.m.Y', strtotime($flightElement->departureDate));
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
                if ($hotel)
                {
                    $item = new HotelTripElement();
                    $item->hotel = $hotel;

                    $item->city = City::getCityByHotelbookId($hotel->cityId)->id;
                    $checkInTimestamp = strtotime($hotel->checkIn);
                    $item->checkIn = date('d.m.Y',$checkInTimestamp);
                    $item->checkOut = date('d.m.Y',$checkInTimestamp + $hotel->duration*3600*24);
                    $item->id = time();
                    Yii::app()->shoppingCart->put($item);
                }
                else
                    throw new CHttpException(404, 'Can\'t found item inside cache');
                break;
        }
    }

    public function actionFillCartElement($cartElementId,$type, $key, $searchId='', $searchId2='')
    {
        $allPositions = Yii::app()->order->getPositions(false);
        $needPosition = null;
        //$needPositions = array();
        foreach($allPositions['items'] as $item)
        {
            if($item->getId() == $cartElementId)
            {
                $needPosition = $item;
                break;
            }
        }
        if($needPosition)
        {
            switch ($type)
            {
                case FlightVoyage::TYPE:
                    $needPositions = array();

                    $groupId = $needPosition->getGroupId();
                    foreach($allPositions['items'] as $item)
                    {
                        if(method_exists($item,'getGroupId')){
                            if($item->getGroupId() == $groupId)
                            {
                                $needPositions[] = $item;
                            }
                        }
                    }
                    /** @var $flight FlightVoyage */
                    $flight = FlightVoyage::getFromCache($searchId,$key);
                    if ($flight)
                    {
                        //updating all cartElements
                        /** @var $needPositions FlightTripElement[] */
                        foreach($needPositions as $item){
                            $item->flightVoyage = $flight;
                            Yii::app()->shoppingCart->update($item,1);
                        }
                        /*$item = new FlightTripElement();
                        $item->flightVoyage = $flight;
                        $item->departureCity = $flight->getDepartureCity()->id;
                        $item->arrivalCity = $flight->getArrivalCity()->id;
                        $item->departureDate = date('d.m.Y', strtotime($flight->getDepartureDate()));
                        $item->id = time();
                        Yii::app()->shoppingCart->put($item);*/
                        echo json_encode($flight->getJsonObject());
                    }
                    else
                        throw new CHttpException(404, 'Can\'t found item inside cache key:'.$key.' searchId:'.$searchId);
                    break;
                case Hotel::TYPE:
                    /** @var $hotel Hotel */
                    $hotel = Hotel::getFromCache($searchId, null, $key);
                    if ($hotel)
                    {
                        //$needPosition = new HotelTripElement();
                        $needPosition->hotel = $hotel;


                        Yii::app()->shoppingCart->update($needPosition,1);

                        echo json_encode($hotel->getJsonObject());
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

    public function actionShow()
    {
        echo Yii::app()->order->getPositions();
    }

    public function actionSave($name)
    {
        $order = new OrderComponent;
        $order->saveOrder($name);
    }

    public function actionClear()
    {
        Yii::app()->shoppingCart->clear();
        if (!Yii::app()->request->isAjaxRequest)
           $this->redirect('/tour/constructor/new');
        else
           $this->actionShow();
    }
}
