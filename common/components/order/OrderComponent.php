<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.06.12
 * Time: 16:25
 */
class OrderComponent extends CApplicationComponent
{
    /**
     * Name of shoppingCart application component
     * @see http://yiiext.github.com/extensions/shopping-cart-component/readme.ru.html
     * @var string
     */
    public $shoppingCartComponent = 'shoppingCart';

    private $sortedItems = array();
    private $bookedItems = array();
    private $finalWorkflowStatuses = array();
    private $bookingContactInfo;

    public function init()
    {
        $this->sortedItems = $this->sortItemsFromCartAndGetThem();
    }

    public function getSortedItemsAsJson()
    {
        $items = $this->sortedItems;
        array_map(function ($item) { return $this->injectAdditionalInfo($item); }, $items);
        return json_encode($items);
    }

    private function sortItemsFromCartAndGetThem()
    {
        $items = $this->getCartItems();
        $times = $this->getTimesForCartItems($items);
        $weights = $this->getWeightsForCartItems($items);
        return $this->getItemsSortedByTimeAndWeights($items, $times, $weights);
    }

    private function getCartItems()
    {
        return Yii::app()->{$this->shoppingCartComponent}->getPositions();
    }

    private function getTimesForCartItems($items)
    {
        return array_map(function ($item) { return $item->getTime(); }, $items);
    }

    private function getWeightsForCartItems($items)
    {
        return array_map(function ($item) { return $item->getWeight(); }, $items);
    }

    private function getItemsSortedByTimeAndWeights($items, $time, $weight)
    {
        array_multisort($time, SORT_ASC, SORT_NUMERIC, $weight, SORT_ASC, SORT_NUMERIC, $items);
        return $items;
    }

    private function injectAdditionalInfo(&$element)
    {
        $element['isFlight'] = $element instanceof FlightTripElement;
        $element['isHotel'] = $element instanceof HotelTripElement;
        $element['isLinked'] = $element->isLinked();
    }

    public function saveOrder($name)
    {
        $order = $this->createOrderAndSaveIt($name);
        $this->saveItemsOfOrder($order);
        echo json_encode(array('result' => true));
    }

    private function createOrderAndSaveIt($name)
    {
        $order = new Order;
        $order->userId = Yii::app()->user->id;
        $order->name = $name;
        if (!$order->save())
        {
            $errMsg = "Could not save named order to database" . PHP_EOL . CVarDumper::dumpAsString($order->getErrors());
            $this->logAndThrowException($errMsg, 'OrderComponent.saveOrder');
            return $order;
        }
        return $order;
    }

    private function saveItemsOfOrder($order)
    {
        foreach ($this->sortedItems as $item)
        {
            if (!$item->saveToOrderDb())
            {
                $errMsg = "Could not save order's item" . PHP_EOL . CVarDumper::dumpAsString($item);
                $this->logAndThrowException($errMsg, 'OrderComponent.saveOrder');
            }
            $item->saveReference($order);
        }
    }

    public function validateItemsOfOrder()
    {
        return array_all($this->sortedItems, array($this, 'isItemValid'));
    }

    private function isItemValid($item)
    {
        return $item->getIsValid();
    }

    public function booking()
    {
        Yii::trace("Get all items inside order", "OrderComponent.booking");
        Yii::trace("Total: ".sizeof($this->sortedItems)." items", "OrderComponent.booking");

        Yii::trace("Analyzing items", "OrderComponent.booking");
        foreach ($this->sortedItems as $item)
            $this->analyzeItem($item);

        Yii::trace("Saving credentials to db", "OrderComponent.booking");
        foreach ($this->sortedItems as $item)
            $this->saveCredentialsForItem($item);

        Yii::trace("Check correctness of statuses", "OrderComponent.booking");
        return $this->areAllStatusesCorrect();
    }

    private function analyzeItem($item)
    {
        if ($item instanceof HotelTripElement)
            $this->analyzeHotelItem($item);

        if ($item instanceof FlightTripElement)
            $this->analyzeFlightItem($item);
    }

    private function analyzeFlightItem($item)
    {
        if ($item->flightBookerId)
        {
            $flightBooker = FlightBooker::model()->findByPk($item->flightBookerId);
            $this->bookedItems[$item->getGroupId()] = $item->getGroupId();
            if ($flightBooker)
            {
                $this->finalWorkflowStatuses[$flightBooker->status] = $flightBooker->status;
                $this->createOrderBookingIfNotExist($flightBooker->orderBookingId);
            }
        }
    }

    private function analyzeHotelItem($item)
    {
        if ($item->hotelBookerId)
        {
            $hotelBooker = HotelBooker::model()->findByPk($item->hotelBookerId);
            if ($hotelBooker)
            {
                $this->finalWorkflowStatuses[$hotelBooker->status] = $hotelBooker->status;
                $this->createOrderBookingIfNotExist($hotelBooker->orderBookingId);
            }
        }
    }

    public function saveCredentialsForItem($position)
    {
        if ($position instanceof HotelTripElement)
            $this->processHotel($position);

        if ($position instanceof FlightTripElement)
            $this->saveCredentialsForFlight($position);
    }

    private function saveCredentialsForFlight($position)
    {
        $passports = $position->getPassports();
        if ($this->isBooked($$position))
        {
            Yii::trace("Trying to save credentials for already booked flight", "OrderComponent.saveCredentialsForFlight");
            return;
        }
        Yii::trace("Save credentials for flight", "OrderComponent.saveCredentialsForFlight");

        $flightBookerComponent = new FlightBookerComponent();
        $flightBookerComponent->setFlightBookerFromFlightVoyage($position->flightVoyage);
        $this->bookedItems[$position->getGroupId()] = $position->getGroupId();
        $currentFlightBookerComponent = $flightBookerComponent->getCurrent();
        $currentFlightBookerComponent->orderBookingId = $this->bookingContactInfo->id;
        if (!$currentFlightBookerComponent->save())
        {
            $errMsg = "Could not save ";
        }
        $position->flightBookerId = $currentFlightBookerComponent->getPrimaryKey();
        $flightBookerId = $flightBookerComponent->getFlightBookerId();
        echo "FlightBookerId : $flightBookerId";

        foreach ($passports->adultsPassports as $adultInfo)
        {
            $flightPassport = new FlightBookingPassport();
            $flightPassport->populate($adultInfo, $flightBookerId);
            $flightPassport->save();
        }
        foreach ($passports->childrenPassports as $childInfo)
        {
            $flightPassport = new FlightBookingPassport();
            $flightPassport->populate($childInfo, $flightBookerId);
            $flightPassport->save();
        }
        foreach ($passports->infantPassports as $infantInfo)
        {
            $flightPassport = new FlightBookingPassport();
            $flightPassport->populate($infantInfo, $flightBookerId);
            $flightPassport->save();
        }
        echo "GoTo Booking";
        $flightBookerComponent->status('booking');
        $status = $flightBookerComponent->getCurrent()->status;
        if ($status)
        {
            $this->finalWorkflowStatuses[$status] = $status;
        }
    }

    private function isBooked($position)
    {
        return !isset($this->bookedItems[$position->getGroupId]) and (!$position->flightBookerId);
    }

    private function areAllStatusesCorrect()
    {
        return array_all($this->finalWorkflowStatuses, array($this, 'isCorrectState'));
    }

    private function isCorrectState($state)
    {
        return (strpos($state,'aiting') !== false);
    }


    public function processHotel($position)
    {
        Yii::trace("Processing HotelTrip", 'OrderComponent.processHotel');
        $groupId = $position->getId();
        if (isset($this->bookedItems[$groupId]) or (!$position->hotelBookerId))
        {
            Yii::trace("Already proccessed", 'OrderComponent.processHotel');
            return;
        }

        $this->bookedItems[$groupId] = $groupId; //mark as already booked

        Yii::trace("Create hotel booker", 'OrderComponent.proccessHotel');
        $hotelBookerComponent = $this->createHotelBookerComponent($position, $groupId);

        Yii::trace("Saving credentials for hotel", 'OrderComponent.proccessHotel');
        $this->saveCredentialsForHotel($position, $hotelBookerComponent);

        Yii::trace("Going to analyzing state", 'OrderComponent.proccessHotel');
        $hotelBookerComponent->status('analyzing');
        $status = $hotelBookerComponent->getCurrent()->status;

        $this->finalWorkflowStatuses[$status] = $status; //store info about current state
        Yii::trace("Processing HotelTrip Successfull", 'OrderComponent.processHotel');
    }

    private function saveCredentialsForHotel($position, $hotelBookerComponent)
    {
        $passports = $position->getPassports();
        $hotelBookerId = $hotelBookerComponent->getHotelBookerId();
        /** @var $passports HotelPassportForm */
        foreach ($passports->roomsPassports as $i => $roomPassport)
        {
            $this->saveAdultsPassports($i, $roomPassport, $hotelBookerId);
            $this->saveChildrenPassports($i, $roomPassport, $hotelBookerId);
        }
        return true;
    }

    private function saveChildrenPassports($i, $roomPassport, $hotelBookerId)
    {
        foreach ($roomPassport->childrenPassports as $childInfo)
        {
            $hotelPassport = new HotelBookingPassport();
            $hotelPassport->scenario = 'child';
            $hotelPassport->attributes = $childInfo->attributes;
            $hotelPassport->hotelBookingId = $hotelBookerId;
            $hotelPassport->roomKey = $i;
            if (!$hotelPassport->save())
            {
                $errMsg = 'Incorrect child passport data.' . PHP_EOL . CVarDumper::dumpAsString($hotelPassport->errors);
                Yii::trace($errMsg, 'HotelBooker.EnterCredentials.childPassport');
                throw new CException($errMsg);
            }
        }
    }

    private function saveAdultsPassports($i, $roomPassport, $hotelBookerId)
    {
        foreach ($roomPassport->adultsPassports as $adultInfo)
        {
            $hotelPassport = new HotelBookingPassport();
            $hotelPassport->scenario = 'adult';
            $hotelPassport->attributes = $adultInfo->attributes;
            $hotelPassport->hotelBookingId = $hotelBookerId;
            $hotelPassport->roomKey = $i;
            if (!$hotelPassport->save())
            {
                $errMsg = "Incorrect adult passport parameters" . PHP_EOL . CVarDumper::dumpAsString($hotelPassport->errors);
                Yii::trace($errMsg, 'HotelBooker.EnterCredentials.adultPassport');
                throw new CException($errMsg);
            }
        }
        return $hotelPassport;
    }

    public function createHotelBookerComponent($position)
    {
        $hotelBookerComponent = new HotelBookerComponent();
        $hotelBookerComponent->setHotelBookerFromHotel($position->hotel);
        $currentHotelBooker = $hotelBookerComponent->getCurrent();
        $currentHotelBooker->orderBookingId = $this->bookingContactInfo->id;
        $currentHotelBooker->status = 'enterCredentials';
        if (!$currentHotelBooker->save())
        {
            $errMsg = 'Couldn\'t save hotel booker instanse'.PHP_EOL.CVarDumper::dumpAsString($currentHotelBooker->getErrors());
            $this->logAndThrowException($errMsg, 'OrderComponent.saveCredentialsForHotel');
        }
        else
        {
            Yii::trace("HotelBooker successfully saved. It's id:" . $hotelBookerComponent->getCurrent()->id, 'OrderComponent.saveCredentialsForHotel');
        }
        $position->hotelBookerId = $currentHotelBooker->getPrimaryKey();
        return $hotelBookerComponent;
    }

    public function logAndThrowException($errorMsg, $codePosition)
    {
        Yii::log($errorMsg, CLogger::LEVEL_ERROR, $codePosition);
        throw new Exception($errorMsg);
    }

    /**
     * Tries to find out order booking with given id
     * If can't = tries to create new one
     * Return current instance of OrderBooking
     * 
     * @param $orderBookingId id of possible existing OrderBooking
     * @return OrderBooking
     * @throw CException if validation fails
     */
    private function createOrderBookingIfNotExist($orderBookingId)
    {
        if ($this->bookingContactInfo = null)
        {
            $this->bookingContactInfo = OrderBooking::model()->findByPk($orderBookingId);
            if (!$this->bookingContactInfo)
            {
                $this->bookingContactInfo = new OrderBooking();
                $this->bookingContactInfo->attributes = ($orderParams = $this->getOrderParams()) ? $orderParams->attributes : $this->getTestContactData();
                if (!$this->bookingContactInfo->save())
                {
                    $errMsg = 'Saving of order booking fails: '.CVarDumper::dumpAsString($this->bookingContactInfo->errors);
                    $this->logAndThrowException($errMsg, 'OrderComponent.createOrderBookingIfNotExist');
                }
            }
        }
        return $this->bookingContactInfo;
    }

    private function getTestContactData()
    {
        return array('email' => 'test@test.ru', 'phone' => '9213546576');
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
        foreach ($this->sortedItems as $item)
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

    /**
     * @return BookingForm
     */
    private function getOrderParams()
    {
        //todo: implement returning booking form here
        $return = '';
        return $return;
    }
}
