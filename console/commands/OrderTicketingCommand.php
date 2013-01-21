<?php
class OrderTicketingCommand extends CConsoleCommand
{

    public function getHelp()
    {
        return <<<EOD
orderticketing cron --orderId=FOO
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
#            $order->sendNotifications();
#            exit;
            $payments = Yii::app()->payments;
            $bookers = $payments->preProcessBookers($order->getBookers());
            foreach($bookers as $booker)
            {
                $this->notifyNemo($booker);
                echo $payments->getStatus($booker) . "=>\n";
                $booker->status('ticketing');
                echo $payments->getStatus($booker) . "\n";
                echo "--------------------------------\n";
                //! Remove scheduled change to BookingTimilimit state
            }
            if($this->isDone($orderId)) {
                $this->sendNotifications($orderId);
                $this->confirmPayment($orderId);
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


    private function notifyNemo($booker)
    {
        if($booker instanceof Payments_MetaBookerTour)
        {
            foreach($booker->getBookers() as $book)
            {
                $this->notifyNemo($book);
            }
            return;
        }

        if(! $booker instanceof Payments_MetaBooker)
        {
            $bill = $payments->getBillForBooker($booker);
            $payments->notifyNemo($booker, $bill);
            $taskId = $booker->getCurrent()->getTaskInfo('paymentTimeLimit')->taskId;
            echo "Removing booking timelimit task #" . $taskId;
            $result = Yii::app()->cron->delete($taskId);
            echo " " . $result . "\n";
        }
    }




    /**
       Confirms preauthorized transactions
    */
    private function confirmPayment($orderId) {
        $order = Yii::app()->order;
        $order->initByOrderBookingId($orderId);
        $payments = Yii::app()->payments;
        $bookers = $payments->preProcessBookers($order->getBookers());
        foreach($bookers as $booker)
        {
            $bill = $payments->getBillForBooker($booker);
            if (!$bill->getChannel()->confirm()) {
                //! fixme log exceptions
                echo "Confirm failed for bill #" . $bill->id . " transaction #" . $bill->transactionId . "\n";
            }
        }
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
