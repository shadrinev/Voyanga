<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.06.12
 * Time: 16:25
 */
class OrderComponent extends CApplicationComponent
{
    public function getPositions($asJson = true)
    {
        $positions = Yii::app()->shoppingCart->getPositions();
        foreach($positions as $position)
        {
            $element = $position->getJsonObject();
            $time[] = $position->getTime();
            $element['key'] = $position->getId();
            $result['items'][] = $element;
            unset($element);
        }
        array_multisort($time, SORT_ASC, SORT_NUMERIC, $result['items']);
        if ($asJson)
            return json_encode($result);
        else
            return $result;
    }

    public function create($name)
    {
        $order = new Order;
        $order->userId = Yii::app()->user->id;
        $order->name = $name;
        if ($result = $order->save())
        {
            $items = $this->getPositions();
            foreach ($items as $item)
                $result  = $result and $item->saveToDb($order->id);
        }
        echo json_encode(array('result'=>$result));
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
