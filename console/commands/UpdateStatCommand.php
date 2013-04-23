<?php

class UpdateStatCommand extends CConsoleCommand
{
    public function actionRun($force = false)
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'id desc';
        $criteria->limit = 500;
        $criteria->addCondition('partnerId is not null');
        if (!$force)
        {
            $criteria->addCondition('hash is null or partner_status is null or full_partner_price is null');
        }
        $orders = OrderBooking::model()->with(array(
                                                   'flightBookers' => array(
                                                       'select' => 'id, status, flightVoyageInfo'
                                                   )
                                              ))->findAll($criteria);
        echo "Total: ".sizeof($orders)."\n";
        foreach ($orders as $order)
        {
            $order->buildHash();
            $order->buildPartnerStatus();
            $order->buildFullPartnerPrice();
        }
        echo "Done\n";
    }
}
