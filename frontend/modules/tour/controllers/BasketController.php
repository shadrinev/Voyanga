<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 13.06.12
 * Time: 14:41
 */
class BasketController extends FrontendController
{
    public function actionAdd($type, $key, $searchId='', $searchId2='')
    {
        switch ($type)
        {
            case FlightVoyage::TYPE:
                $flight = FlightVoyage::getFromCache($key, $searchId);
                if ($flight)
                {
                    $item = new FlightTripElement();
                    $item->flightVoyage = $flight;
                    Yii::app()->shoppingCart->put($item);
                }
                else
                    throw new CHttpException(404, 'Can\'t found item inside cache');
                break;
            case Hotel::TYPE:
                $hotel = Hotel::getFromCache($key, $searchId, $searchId2);
                if ($hotel)
                {
                    $item = new HotelTripElement();
                    $item->hotel = $hotel;
                    Yii::app()->shoppingCart->put($item);
                }
                else
                    throw new CHttpException(404, 'Can\'t found item inside cache');
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
        if (!Yii::app()->request->isAjaxRequest)
           $this->redirect('/tour/constructor/new');
        else
           $this->actionShow();
    }
}
