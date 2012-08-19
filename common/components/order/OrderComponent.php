<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.06.12
 * Time: 16:25
 */
class OrderComponent extends CApplicationComponent
{
    private $itemsOnePerGroup = array();
    private $bookedItems = array();
    private $finalWorkflowStatuses = array();

    public function init()
    {
        $dataProvider = new TripDataProvider();
        $this->itemsOnePerGroup = $dataProvider->getSortedCartItemsOnePerGroup();
    }

    public function bookAndReturnTripElementWorkflowItems()
    {
        Yii::trace("Get all items inside order", "OrderComponent.booking");
        Yii::trace("Total: ".sizeof($this->itemsOnePerGroup)." items", "OrderComponent.booking");
        Yii::trace("Analyzing items", "OrderComponent.booking");
        $bookedTripElementWorkflow = array();
        foreach ($this->itemsOnePerGroup as $item)
        {
            $tripElementWorkflow = $item->createTripElementWorkflow();
            $tripElementWorkflow->bookItem();
            $this->markItemGroupAsBooked($tripElementWorkflow->getItem());
            $status = $tripElementWorkflow->executeFromStageAndReturnStatus();
            $this->saveWorkflowState($status);
            $bookedTripElementWorkflow[] = $tripElementWorkflow;
        }
        Yii::trace("Check correctness of statuses", "OrderComponent.booking");
        if ($this->areAllStatusesCorrect())
        {
            return $bookedTripElementWorkflow;
        }
        else
        {
            throw new CException('At least one of workflow status at step 1 is incorrect:'.CVarDumper::dumpAsString($this->finalWorkflowStatuses));
        }
    }

    public function validateItemsOfOrder()
    {
        return array_all($this->itemsOnePerGroup, array($this, 'isItemValid'));
    }

    public function isItemValid($item)
    {
        return $item->getIsValid();
    }

    private function saveWorkflowState($status)
    {
        $this->finalWorkflowStatuses[$status] = $status;
    }

    private function markItemGroupAsBooked($item)
    {
        $this->bookedItems[$item->getGroupId()] = $item->getGroupId();
    }

    private function areAllStatusesCorrect()
    {
        return array_all($this->finalWorkflowStatuses, array($this, 'isCorrectState'));
    }

    public function isCorrectState($state)
    {
        $validStates = array('swFlightBooker/waitingForPayment', 'analyzing');
        return in_array($state, $validStates);
    }

    public function logAndThrowException($errorMsg, $codePosition)
    {
        Yii::log($errorMsg, CLogger::LEVEL_ERROR, $codePosition);
        throw new Exception($errorMsg);
    }

    public function startPayment()
    {
        // perekluchaem v state startpayment
        // i proveraem

        $bookingModel = $this->getOrderBooking();

        //test states and time

        //make StartPayment State
        $validForPayment = true;
        $nowTime = time();
        /** @var $bookingModel OrderBooking */
        if ($bookingModel)
        {
            foreach ($bookingModel->flightBookers as $flightBooker)
            {
                $flightBookerComponent = new FlightBookerComponent();
                $flightBookerComponent->setFlightBookerFromId($flightBooker->id);
                $expiration = strtotime($flightBookerComponent->getCurrent()->timeout);
                if (appParams('time_for_payment') < ($expiration - $nowTime))
                {
                    //next state
                    $status = strtolower($flightBookerComponent->getStatus());
                    if (strpos($status, 'waitingforpayment') !== false)
                    {
                        $flightBookerComponent->status('startPayment');
                    }
                    else
                    {
                        $validForPayment = false;
                    }
                }
                else
                {
                    $validForPayment = false;
                }

            }
            foreach ($bookingModel->hotelBookers as $hotelBooker)
            {
                $hotelBookerComponent = new HotelBookerComponent();
                $hotelBookerComponent->setHotelBookerFromId($hotelBooker->id);
                $expiration = $hotelBookerComponent->hotel->cancelExpiration;
                if (appParams('time_for_payment') < ($expiration - $nowTime))
                {
                    //next state
                    $status = $hotelBookerComponent->getStatus();
                    if (strpos($status, 'soft') !== false)
                    {
                        $hotelBookerComponent->status('softStartPayment');
                    }
                    elseif (strpos($status, 'hard') !== false)
                    {
                        $hotelBookerComponent->status('hardStartPayment');
                    }
                    else
                    {
                        $validForPayment = false;
                    }

                }
                else
                {
                    $validForPayment = false;
                }
            }
        }
        else
        {
            return false;
        }
        return $validForPayment;
    }

    public function getPayment()
    {
        $bookingModel = $this->getOrderBooking();

        $haveStateStartPayment = true;
        $allTicketingValid = true;

        /** @var FlightBooker[] $flightBookers  */
        $flightBookers = FlightBooker::model()->findAllByAttributes(array('orderBookingId' => $bookingModel->primaryKey));
        foreach ($flightBookers as $flightBooker)
        {
            $status = $flightBooker->status;
            if (strpos($status, '/') !== false)
            {
                $status = substr($status, strpos($status, '/') + 1);
            }
            if ($status !== 'startPayment')
            {
                echo 'flight';
                $haveStateStartPayment = false;
            }

            //$flightBooker->timeout;
        }

        $hotelBookers = HotelBooker::model()->findAllByAttributes(array('orderBookingId' => $bookingModel->primaryKey));
        foreach ($hotelBookers as $hotelBooker)
        {
            $status = $hotelBooker->status;
            if (strpos($status, '/') !== false)
            {
                $status = substr($status, strpos($status, '/') + 1);
            }
            if (($status !== 'softStartPayment') AND ($status !== 'hardStartPayment'))
            {
                echo 'hotel' . $status;
                $haveStateStartPayment = false;
            }
        }

        if (!$haveStateStartPayment)
        {
            //TODO: make return money and go to find new objects
            $allTicketingValid = false;
            echo '=((';
        }
        else
        {
            //TODO: make tiketing
            echo 'make ticketing';
            /** @var FlightBooker[] $flightBookers  */

            $flightBookers = FlightBooker::model()->findAllByAttributes(array('orderBookingId' => $bookingModel->primaryKey));
            foreach ($flightBookers as $flightBooker)
            {
                $status = $flightBooker->status;
                if (strpos($status, '/') !== false)
                {
                    $status = substr($status, strpos($status, '/') + 1);
                }
                if (($status == 'startPayment') and ($allTicketingValid))
                {
                    $flightBookerComponent = new FlightBookerComponent();
                    $flightBookerComponent->setFlightBookerFromId($flightBooker->id);
                    $flightBookerComponent->status('ticketing');

                    //check that status is good
                    //else $allTicketingValid = false;
                    $newStatus = $flightBookerComponent->getStatus();
                    if ($newStatus == 'ticketingRepeat')
                    {
                        $allTicketingValid = false;
                    }
                }

                //$flightBooker->timeout;
            }

            /** @var HotelBooker[] $hotelBookers  */
            $hotelBookers = HotelBooker::model()->findAllByAttributes(array('orderBookingId' => $bookingModel->primaryKey));

            foreach ($hotelBookers as $hotelBooker)
            {
                $status = $hotelBooker->status;
                if (strpos($status, '/') !== false)
                {
                    $status = substr($status, strpos($status, '/') + 1);
                }
                if (($status == 'softStartPayment') and ($allTicketingValid))
                {
                    $hotelBookerComponent = new HotelBookerComponent();
                    $hotelBookerComponent->setHotelBookerFromId($hotelBooker->id);
                    //TODO: may be task to cron???
                    $hotelBookerComponent->status('moneyTransfer');
                }
            }
            foreach ($hotelBookers as $hotelBooker)
            {
                $status = $hotelBooker->status;
                if (strpos($status, '/') !== false)
                {
                    $status = substr($status, strpos($status, '/') + 1);
                }
                if (($status == 'hardStartPayment') and ($allTicketingValid))
                {
                    $hotelBookerComponent = new HotelBookerComponent();
                    $hotelBookerComponent->setHotelBookerFromId($hotelBooker->id);
                    $res = $hotelBookerComponent->checkValid();


                    if (!$res)
                    {
                        $allTicketingValid = false;
                    }
                }
            }

            $haveProblems = false;
            foreach ($hotelBookers as $hotelBooker)
            {
                $status = $hotelBooker->status;
                if (strpos($status, '/') !== false)
                {
                    $status = substr($status, strpos($status, '/') + 1);
                }
                if (($status == 'hardStartPayment') and ($allTicketingValid))
                {
                    $hotelBookerComponent = new HotelBookerComponent();
                    $hotelBookerComponent->setHotelBookerFromId($hotelBooker->id);
                    $hotelBookerComponent->status('ticketing');


                    $newStatus = $hotelBookerComponent->getCurrent()->status;
                    if (strpos($newStatus, '/') !== false)
                    {
                        $newStatus = substr($newStatus, strpos($newStatus, '/') + 1);
                    }
                    if ($newStatus == 'ticketingRepeat')
                    {
                        $allTicketingValid = false;
                        $haveProblems = true;
                    }
                }
            }

            if (!$allTicketingValid)
            {
                $this->returnMoney($haveProblems);
            }
        }

    }

    public function getOrderBooking()
    {
        foreach ($this->itemsOnePerGroup as $item)
        {
            if ($item instanceof HotelTripElement)
            {
                if ($item->hotelBookerId)
                {
                    $hotelBooker = HotelBooker::model()->findByPk($item->hotelBookerId);
                    if ($hotelBooker)
                    {
                        //$status = $hotelBooker->status;
                        //$endSates[$status] = $status;
                        if (!isset($bookingModel))
                        {
                            $bookingModel = OrderBooking::model()->findByPk($hotelBooker->orderBookingId);
                            if ($bookingModel)
                            {
                                break;
                            }
                        }
                    }
                }
            }
            elseif ($item instanceof FlightTripElement)
            {
                if ($item->flightBookerId)
                {
                    $flightBooker = FlightBooker::model()->findByPk($item->flightBookerId);
                    if ($flightBooker)
                    {
                        //$status = $flightBooker->status;
                        //$endSates[$status] = $status;
                        if (!isset($bookingModel))
                        {
                            $bookingModel = OrderBooking::model()->findByPk($flightBooker->orderBookingId);
                            if ($bookingModel)
                            {
                                break;
                            }
                        }
                    }
                }
            }
        }
        if (!isset($bookingModel))
        {
            return false;
        }
        else
        {
            return $bookingModel;
        }
    }

    public function returnMoney($haveProblems = false)
    {
        $bookingModel = $this->getOrderBooking();

        /** @var FlightBooker[] $flightBookers  */
        $flightBookers = FlightBooker::model()->findAllByAttributes(array('orderBookingId' => $bookingModel->primaryKey));
        foreach ($flightBookers as $flightBooker)
        {
            $status = $flightBooker->status;
            if (strpos($status, '/') !== false)
            {
                $status = substr($status, strpos($status, '/') + 1);
            }
            $flightBookerComponent = new FlightBookerComponent();
            $flightBookerComponent->setFlightBookerFromId($flightBooker->id);
            $flightBookerComponent->status('moneyReturn');

            //$flightBooker->timeout;
        }

        $hotelBookers = HotelBooker::model()->findAllByAttributes(array('orderBookingId' => $bookingModel->primaryKey));
        foreach ($hotelBookers as $hotelBooker)
        {
            $hotelBookerComponent = new HotelBookerComponent();
            $hotelBookerComponent->setHotelBookerFromId($hotelBooker->id);
            $hotelBookerComponent->status('moneyReturn');
        }
    }

    public function transferMoney()
    {
        $bookingModel = $this->getOrderBooking();

        /** @var FlightBooker[] $flightBookers  */
        $flightBookers = FlightBooker::model()->findAllByAttributes(array('orderBookingId' => $bookingModel->primaryKey));
        foreach ($flightBookers as $flightBooker)
        {
            $status = $flightBooker->status;
            if (strpos($status, '/') !== false)
            {
                $status = substr($status, strpos($status, '/') + 1);
            }
            $flightBookerComponent = new FlightBookerComponent();
            $flightBookerComponent->setFlightBookerFromId($flightBooker->id);
            $flightBookerComponent->status('transferMoney');

            //$flightBooker->timeout;
        }

        $hotelBookers = HotelBooker::model()->findAllByAttributes(array('orderBookingId' => $bookingModel->primaryKey));
        foreach ($hotelBookers as $hotelBooker)
        {
            $status = $hotelBooker->status;
            if (strpos($status, '/') !== false)
            {
                $status = substr($status, strpos($status, '/') + 1);
            }
            $hotelBookerComponent = new HotelBookerComponent();
            $hotelBookerComponent->setHotelBookerFromId($hotelBooker->id);
            $hotelBookerComponent->status('transferMoney');
        }
    }
}
