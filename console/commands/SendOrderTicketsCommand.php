<?php
class SendOrderTicketsCommand extends CConsoleCommand
{

    public function getHelp()
    {
        return <<<EOD
sendordertickets cron --orderId=FOO
EOD;
    }

    public function actionCron($orderId = 0)
    {
        $orderId = intval($orderId);
        if ($orderId) {
            Yii::app()->user->setState('orderBookingId', $orderId);
            $order = Yii::app()->order;
            $order->initByOrderBookingId($orderId);
            $order->sendNotifications();
        }
        else
        {
            echo $this->getHelp();
        }
    }
}
