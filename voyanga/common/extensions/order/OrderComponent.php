<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.06.12
 * Time: 16:25
 */
class OrderComponent extends CApplicationComponent
{
    public function create($userId, $items)
    {
        $order = new Order;
        $order->userId = $userId;
        $order->initSoftAttributes(array_keys($items));
        foreach ($items as $name=>$value)
            $order->name = $value;
        $order->save();
        return $order->id;
    }

    public function update($id, $userId, $items)
    {
        $order = Order::model()->findByPk($id);
        if ($order)
        {
            $order = new Order;
            $order->userId = $userId;
            $order->initSoftAttributes(array_keys($items));
            foreach ($items as $name=>$value)
                $order->name = $value;
            $order->save();
        }
        return $order->id;
    }
}
