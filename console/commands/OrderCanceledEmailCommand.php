<?php
class OrderCanceledEmailCommand extends CConsoleCommand
{

    public function getHelp()
    {
        return <<<EOD
ordercanceledemail cron --orderId=FOO
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
            if($this->isCanceled($orderId)) {
                $this->sendNotification($orderId);
            }
        }
        else
        {
            echo $this->getHelp();
        }
    }

   private function sendNotification($orderId) {
        $order = Yii::app()->order;
        $order->initByOrderBookingId($orderId);
        $order->sendCanceled();
    }

    private function isCanceled($orderId) {
        $order = Yii::app()->order;
        $order->initByOrderBookingId($orderId);
        $payments = Yii::app()->payments;
        $bookers = $payments->preProcessBookers($order->getBookers());
        foreach($bookers as $booker)
        {
            if($payments->getStatus($booker)!=="canceled")
                return false;
        }
        return true;
    }

}
