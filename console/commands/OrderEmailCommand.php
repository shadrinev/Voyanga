<?php
class OrderEmailCommand extends CConsoleCommand
{

    public function getHelp()
    {
        return <<<EOD
orderemail cron --orderId=FOO
EOD;
    }

    public function actionCron($orderId = 0)
    {
        //! FIXME cache order instance
        $orderId = intval($orderId);
        if ($orderId) {
            Yii::app()->user->setState('orderBookingId', $orderId);
            $order = Yii::app()->order;
            $order->initByOrderBookingId($orderId);
            if($this->isDone($orderId)) {
                $this->sendNotifications($orderId);
            }
        }
        else
        {
            echo $this->getHelp();
        }
    }

    /**
       Sends order email/sms/whatever
    */
    private function sendNotifications($orderId) {
        $order = Yii::app()->order;
        $order->initByOrderBookingId($orderId);
        $order->sendNotifications();
    }

    private function isDone($orderId) {
        $order = Yii::app()->order;
        $order->initByOrderBookingId($orderId);
        $payments = Yii::app()->payments;
        $bookers = $payments->preProcessBookers($order->getBookers());
        foreach($bookers as $booker)
        {
            if($payments->getStatus($booker)!=="done")
                return false;
        }
        return true;
    }

}
