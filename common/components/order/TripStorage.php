<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 19.08.12 20:01
 */
class TripStorage
{
    private $name;
    private $order;
    private $items;
    private $itemsOnePerGroup;
    private $event;
    private $startCityId;

    public function __construct()
    {
        $dataProvider = new TripDataProvider();
        $this->items = $dataProvider->getSortedCartItems();
        $this->itemsOnePerGroup = $dataProvider->getSortedCartItemsOnePerGroup();
    }

    public function  getPrice()
    {
        $price = 0;
        foreach ($this->itemsOnePerGroup as $item)
            $price += $item->getPrice();
        return $price;
    }

    public function saveOrder($event, $startCityId, $name)
    {
        $this->name = $name;
        $this->event = $event;
        $this->startCityId = $startCityId;
        $this->order = $this->createOrderAndSaveIt();
        $this->saveItemsOfOrder();
        return $this->order;
    }

    private function createOrderAndSaveIt()
    {
        $this->deleteCityOrder();
        $order = new Order;
        $order->userId = Yii::app()->user->id;
        $order->name = $this->name;
        if (!$order->save())
        {
            $errMsg = "Could not save named order to database" . PHP_EOL . CVarDumper::dumpAsString($order->getErrors());
            $this->logAndThrowException($errMsg, 'TripStorage.createOrderAndSaveIt');
            return $order;
        }
        return $order;
    }

    private function deleteCityOrder()
    {
        $eventOrder = EventOrder::model()->findByAttributes(array(
            'eventId' => $this->event->id,
            'startCityId' => $this->startCityId
        ));

        if (!$eventOrder)
            return;

        /** @var Order $order  */
        $order = $eventOrder->order;

        if ($order)
        {
            foreach ($order->flightItems as $item)
            {
                $item->delete();
            }
            foreach ($order->hotelItems as $item)
            {
                $item->delete();
            }

            OrderHasFlightVoyage::model()->deleteAllByAttributes(array('orderId'=>$order->id));
            OrderHasHotel::model()->deleteAllByAttributes(array('orderId'=>$order->id));
            $order->delete();
        }

        $eventOrder->delete();
    }

    private function saveItemsOfOrder()
    {
        foreach ($this->items as $item)
        {
            if (!$item->saveToOrderDb())
            {
                $errMsg = "Could not save order's item" . PHP_EOL . CVarDumper::dumpAsString($item);
                $this->logAndThrowException($errMsg, 'TripStorage.saveItemsOfOrder');
            }
            $item->saveReference($this->order);
        }
    }

    public function logAndThrowException($errorMsg, $codePosition)
    {
        Yii::log($errorMsg, CLogger::LEVEL_ERROR, $codePosition);
        throw new Exception($errorMsg);
    }
}
