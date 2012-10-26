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


        Yii::trace('elements:'.print_r($items,true));
        $flightAmbiguous = false;
        $flightCounts = array();
        foreach($items as $item){
            if($item instanceof FlightTripElement){
                if($flightCounts){
                    if($flightCounts['adultCount'] != $item->adultCount || $flightCounts['childCount'] != $item->childCount || $flightCounts['infantCount'] != $item->infantCount){
                       $flightAmbiguous = true;
                        break;
                    }
                }else{
                    $flightCounts['adultCount'] = $item->adultCount;
                    $flightCounts['childCount'] = $item->childCount;
                    $flightCounts['infantCount'] = $item->infantCount;
                }
            }
        }
        if($flightAmbiguous){
            foreach($items as $item){
                if($item instanceof FlightTripElement){
                    for($i = 0;$i<$item->adultCount;$i++){
                        $elements[] = array('type'=>'adult');
                    }
                    for($i = 0;$i<$item->childCount;$i++){
                        $elements[] = array('type'=>'child');
                    }
                    for($i = 0;$i<$item->infantCount;$i++){
                        $elements[] = array('type'=>'infant');
                    }
                }
            }
        }else{
            for($i = 0;$i<$item->adultCount;$i++){
                $elements[] = array('type'=>'adult');
            }
            for($i = 0;$i<$item->childCount;$i++){
                $elements[] = array('type'=>'child');
            }
            for($i = 0;$i<$item->infantCount;$i++){
                $elements[] = array('type'=>'infant');
            }
        }

        if(isset($_POST['contact_email'])){
            Yii::trace('dan:'.print_r($_POST,true));
        }

        if ($this->areNotAllItemsLinked($items))
        {
            $this->controller->render('makeBooking', array('validFill'=>false,'validBooking'=>false,'elements'=>$elements,'flightAmbiguous'=>$flightAmbiguous));
        }
        else
        {
            $tripElementsWorkflow = Yii::app()->order->bookAndReturnTripElementWorkflowItems();
            foreach($tripElementsWorkflow as $tripElementWorkflow)
            {
                $item = $tripElementWorkflow->item;
                $elements[] = array('type'=>$item->getType(),'id'=>$item->getId(),'status'=>$tripElementWorkflow->finalStatus);
            }
            $this->controller->render('makeBooking', array('validFill'=>true,'validBooking'=>true,'elements'=>$elements,'flightAmbiguous'=>$flightAmbiguous));
        }
    }

    private function areNotAllItemsLinked($items)
    {
        return !array_all($items, function ($item) { return $item->isLinked();} );
    }
}
