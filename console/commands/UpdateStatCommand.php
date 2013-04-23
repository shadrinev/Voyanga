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
            $criteria->addCondition('hash is null');
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
        }
        echo "Done\n";
    }
}
