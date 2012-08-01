<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:22
 */
class ViewAction extends CAction
{
    public function run($id)
    {
        Yii::app()->shoppingCart->clear();
        $order = Order::model()->findByPk($id);
        $items = $order->flightItems();
        foreach ($items as $item)
        {
            $object = unserialize($item->object);
            Yii::app()->shoppingCart->put($object);
        }
        $this->controller->render('view', array('order'=>$order));
    }
}
