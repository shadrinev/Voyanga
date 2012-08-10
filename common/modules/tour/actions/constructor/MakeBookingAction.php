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
        $trip = Yii::app()->order->getPositions(false);
        $valid = true;

        if(isset($trip['items'])){
            foreach($trip['items'] as $cartElement)
            {
                if($cartElement instanceof FlightTripElement)
                {
                    if(!$cartElement->flightVoyage)
                    {
                        $valid = false;
                    }
                }
                elseif($cartElement instanceof HotelTripElement)
                {
                    if(!$cartElement->hotel)
                    {
                        $valid = false;
                    }
                }
                else
                {
                    $valid = false;
                }
            }
        }
        //VarDumper::dump(Yii::app()->shoppingCart);
        //VarDumper::dump($valid);
        //VarDumper::dump($trip);
        $validBooking = false;
        $elements = array();
        if($valid)
        {
            $validBooking = Yii::app()->order->booking();
            $positions = Yii::app()->order->getPositions(false);

            foreach($positions['items'] as $item)
            {
                if($item instanceof HotelTripElement)
                {
                    if($item->hotelBookerId)
                    {
                        $hotelBooker = HotelBooker::model()->findByPk($item->hotelBookerId);
                        if($hotelBooker)
                        {
                            $status = $hotelBooker->status;
                            if(strpos($status,'aiting') === false){
                                $elements[] = array('type'=>'Hotel','id'=>$item->hotelBookerId,'status'=>$status);
                            }

                        }
                    }
                }
                elseif($item instanceof FlightTripElement)
                {
                    if($item->flightBookerId)
                    {
                        $flightBooker = FlightBooker::model()->findByPk($item->flightBookerId);
                        $groupId = $item->getGroupId();
                        $bookedElements[$groupId] = $groupId;
                        if($flightBooker)
                        {
                            $status = $flightBooker->status;
                            if(strpos($status,'aiting') === false){
                                $elements[] = array('type'=>'Flight','id'=>$item->flightBookerId,'status'=>$status);
                            }
                        }
                    }
                }
            }
        }
        $this->controller->render('makeBooking', array('validFill'=>$valid,'validBooking'=>$validBooking,'elements'=>$elements));
    }
}
