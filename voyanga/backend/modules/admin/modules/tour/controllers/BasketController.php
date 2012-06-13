<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 13.06.12
 * Time: 14:41
 */
class BasketController extends ABaseAdminController
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
        $positions = Yii::app()->shoppingCart->getPositions();
        foreach($positions as $position)
        {
            $element = $position->getJsonObject();
            $element['key'] = $position->getId();
            $result['items'][] = $element;
            unset($element);
        }
        echo json_encode($result);
    }
}
