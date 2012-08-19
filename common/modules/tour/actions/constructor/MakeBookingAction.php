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
                $workflow = $tripElementWorkflow->workflow;
                $elements[] = array('type'=>$item->getType(),'id'=>$item->hotelBookerId,'status'=>$workflow->status);
            }
            $this->controller->render('makeBooking', array('validFill'=>true,'validBooking'=>true,'elements'=>$elements));
        }
    }

    private function areNotAllItemsLinked($items)
    {
        return !array_all($items, function ($item) { return $item->isLinked();} );
    }
}
