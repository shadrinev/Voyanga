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
        $positions = Yii::app()->shoppingCart->getPositions();
        foreach($positions as $position) {
            $result[$position->getId()] = $position->getAsArray();
        }
        echo json_encode($result);
    }
}
