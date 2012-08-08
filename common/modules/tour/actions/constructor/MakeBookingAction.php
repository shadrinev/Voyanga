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
        if($valid)
        {
            Yii::app()->order->booking();
        }
        echo 123;die();
    }
}
