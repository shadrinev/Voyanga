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

        foreach($trip as $cartElement)
        {
            if($cartElement instanceof FlightTripElement)
            {
                $flightSearchParams = new FlightSearchParams();
                $flightSearchParams->addRoute(array(''));
            }
        }
        VarDumper::dump(Yii::app()->shoppingCart);
    }
}
