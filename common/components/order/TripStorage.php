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

    public function __construct()
    {
        $dataProvider = new TripDataProvider();
        $this->items = $dataProvider->getSortedCartItems();
        $this->itemsOnePerGroup = $dataProvider->getSortedCartItemsOnePerGroup();
    }

    public function saveOrder($orderId, $name)
    {
        $this->name = $name;
        $this->order = $this->createOrderAndSaveIt($orderId);
        $this->saveItemsOfOrder();
        return $this->order;
    }

    private function createOrderAndSaveIt($orderId)
    {
        if (is_numeric($orderId))
        {
            $this->deleteCurrentOrder($orderId);
        }
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

    private function deleteCurrentOrder($orderId)
    {
        /** @var Order $order  */
        $order = Order::model()->findByPk($orderId);

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

            $links = OrderHasFlightVoyage::model()->findByAttributes(array('orderId'=>$orderId));
            $links->deleteAll();

            $links = OrderHasHotel::model()->findByAttributes(array('orderId'=>$orderId));
            $links->deleteAll();

            $order->delete();
        }
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
