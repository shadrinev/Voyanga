<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 13.06.12
 * Time: 19:06
 */
class ViewerController extends ABaseAdminController
{
    public function actionIndex()
    {
        $dataProvider=new CActiveDataProvider('Order');
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
    }

    public function actionView($id)
    {
        Yii::app()->shoppingCart->clear();
        $order = Order::model()->findByPk($id);
        $items = $order->flightItems();
        foreach ($items as $item)
        {
            $object = unserialize($item->object);
            Yii::app()->shoppingCart->put($object);
        }
        $this->render('view', array('order'=>$order));
    }
}
