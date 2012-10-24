<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:08
 */
class MakeBookingAction extends CAction
{
    public function run()
    {
        $this->getController()->layout = 'static';
        $dataProvider = new TripDataProvider();
        $items = $dataProvider->getSortedCartItems();
        $elements = array();

        if ($this->areNotAllItemsLinked($items))
        {
            $this->controller->render('makeBooking', array('validFill'=>false,'validBooking'=>false,'elements'=>$elements));
        }
        else
        {
            $tripElementsWorkflow = Yii::app()->order->bookAndReturnTripElementWorkflowItems();
            foreach($tripElementsWorkflow as $tripElementWorkflow)
            {
                $item = $tripElementWorkflow->item;
                $elements[] = array('type'=>$item->getType(),'id'=>$item->getId(),'status'=>$tripElementWorkflow->finalStatus);
            }
            $this->controller->render('makeBooking', array('validFill'=>true,'validBooking'=>true,'elements'=>$elements));
        }
    }

    private function areNotAllItemsLinked($items)
    {
        return !array_all($items, function ($item) { return $item->isLinked();} );
    }
}
