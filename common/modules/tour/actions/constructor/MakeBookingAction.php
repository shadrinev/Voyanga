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

        foreach($trip as $cartElement)
        {
            if($cartElement instanceof FlightTripElement)
            {
                if(!$cartElement->flightVoyage)
                {
                    $valid = false;
                }
            }

            if($cartElement instanceof HotelTripElement)
            {
                if(!$cartElement->hotel)
                {
                    $valid = false;
                }
            }
        }
        VarDumper::dump(Yii::app()->shoppingCart);
        if($valid)
        {
            Yii::app()->order->booking();
        }
        echo 123;die();
    }
}
