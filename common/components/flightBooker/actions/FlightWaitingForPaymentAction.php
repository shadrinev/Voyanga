<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 02.07.12
 * Time: 11:13
 * To change this template use File | Settings | File Templates.
 */
class FlightWaitingForPaymentAction extends StageAction
{
    public function execute()
    {
        $this->getController()->render('flightBooker.views.payment',
            array(

            )
        );
    }
}
