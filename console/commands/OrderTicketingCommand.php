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
                if(! $booker instanceof Payments_MetaBooker)
                {
                    $bill = $payments->getBillForBooker($booker->getCurrent());
                    $payments->notifyNemo($booker, $bill);
                    $taskId = $booker->getCurrent()->getTaskInfo('paymentTimeLimit')->taskId
                    echo "Removing booking timelimit task #" . $taskId;
                    $result = Yii::app()->cron->delete($taskId);
                    echo " " . $result . "\n";
                }
                echo $payments->getStatus($booker) . "=>\n";
                $booker->status('ticketing');
                echo $payments->getStatus($booker) . "\n";
                echo "--------------------------------\n";
                //! Remove scheduled change to BookingTimilimit state
            }
            $order->initByOrderBookingId($orderId);
            $order->sendNotifications();
        }
        else
        {
            echo $this->getHelp();
        }
    }
}
