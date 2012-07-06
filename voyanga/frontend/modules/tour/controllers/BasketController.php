<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 13.06.12
 * Time: 14:41
 */
class BasketController extends FrontendController
{
    public function actionAdd($type, $key, $searchId)
    {
        switch ($type)
        {
            case FlightVoyage::TYPE:
                $item = FlightVoyage::getFromCache($key, $searchId);
                Yii::app()->shoppingCart->put($item);
                break;
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
        $order->create($name);
    }

    public function actionClear()
    {
        Yii::app()->shoppingCart->clear();
        $this->redirect('/admin/tour/constructor/new');
    }
}
