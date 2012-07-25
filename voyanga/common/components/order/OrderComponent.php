<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.06.12
 * Time: 16:25
 */
class OrderComponent extends CApplicationComponent
{
    public $shoppingCartComponent = 'shoppingCart';
    public $isValid;

    public function getPositions($asJson = true)
    {
        $positions = Yii::app()->{$this->shoppingCartComponent}->getPositions();
        $result = array();
        $time = array();
        foreach($positions as $position)
        {
            if ($asJson)
            {
                $element = $position->getJsonObject();
            }
            else
            {
                $element = $position;
            }
            $time[] = $position->getTime();
            if ($asJson)
            {
                $element['key'] = $position->getId();
                if ($position instanceof FlightVoyage)
                    $element['isFlight'] = true;
                if ($position instanceof Hotel)
                {
                    $element['isHotel'] = true;

                }

            }
            $result['items'][] = $element;
            unset($element);
        }
        if (sizeof($time)>0)
        {
            array_multisort($time, SORT_ASC, SORT_NUMERIC, $result['items']);
        }
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
            $items = $this->getPositions(false);
            foreach ($items['items'] as $item)
            {
                if ($saved = $item->saveToOrderDb())
                {
                    $item->saveReference($order);
                }
                else
                {
                    $result = false;
                    break;
                }
            }

        }
        echo json_encode(array('result'=>$result));
    }

    public function forceValidate()
    {
        $positions = $this->getPositions(false);
        $allValid = true;
        foreach($positions as $position){
            $valid = $position->getIsValid();
            if(!$valid)
            {

            }
            $allValid &= $valid;
        }
    }
}
