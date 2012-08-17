<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.06.12
 * Time: 16:25
 */
class OrderComponent extends CApplicationComponent
{
    public $shoppingCartComponent = 'shoppingCart';
    public $orderBooking;
    public $isValid;

    private $endStatuses = array();
    private $bookedElements = array();
    private $bookingModel;
    private $validSaving;
    private $flightGroupId;

    private $orderPositions;

    public function getPositions($asJson = true)
    {
        $this->orderPositions = Yii::app()->{$this->shoppingCartComponent}->getPositions();
        $result = array();
        $time = array();
        $weight = array();
        foreach ($this->orderPositions as $position)
        {
            if ($asJson)
            {
                $element = $position->getJsonObject();
            }
            else
            {
                $element = $position;
            }
            $time[] = $position->getTime();
            $weight[] = $position->getWeight();
            if ($asJson)
            {
                if ($position instanceof FlightTripElement)
                    $element['isFlight'] = true;
                if ($position instanceof HotelTripElement)
                {
                    $element['isHotel'] = true;
                }
                $element['isLinked'] = $position->isLinked();
            }
            $result['items'][] = $element;
            unset($element);
        }
        if (sizeof($time) > 0)
        {
            array_multisort($time, SORT_ASC, SORT_NUMERIC, $weight, SORT_ASC, SORT_NUMERIC, $result['items']);
        }
        if ($asJson)
            return json_encode($result);
        else
            return $result;
    }

    public function create($name)
    {
        $order = new Order;
        $order->userId = Yii::app()->user->id;
        $order->name = $name;
        if ($result = $order->save())
        {
            $items = $this->getPositions(false);
            foreach ($items['items'] as $item)
            {
                if ($saved = $item->saveToOrderDb())
                {
                    $item->saveReference($order);
                }
                else
                {
                    $result = false;
                    break;
                }
            }

        }
        echo json_encode(array('result' => $result));
    }

    public function forceValidate()
    {
        $this->orderPositions = $this->getPositions(false);
        $allValid = true;
        /** @var FlightVoyage[] $this->orderPositions */
        foreach ($this->orderPositions as $position)
        {
            $valid = $position->getIsValid();
            if (!$valid)
            {
                //$position->
            }
            $allValid &= $valid;
        }
        $this->isValid = $allValid;
    }

    public function init()
    {
        $this->orderPositions = $this->getPositions(false);
    }

    public function booking()
    {
        Yii::trace("Get all items inside order", "OrderComponent.booking");
        $orderItems = $this->getOrderItems();
        Yii::trace("Total: ".sizeof($orderItems)." items", "OrderComponent.booking");

        Yii::trace("Analyzing items", "OrderComponent.booking");
        foreach ($orderItems as $item)
            $this->analyzeItem($item);

        Yii::trace("Saving credentials to db", "OrderComponent.booking");
        foreach ($orderItems as $item)
            $this->saveCredentialsForItem($item);

        Yii::trace("Check correctness of statuses", "OrderComponent.booking");
        return $this->areAllStatusesCorrect();
    }

    private function areAllStatusesCorrect()
    {
        if ($this->endStatuses)
        {
            $validStates = true;
            foreach ($this->endStatuses as $stateDesc)
            {
                if (strpos($stateDesc, 'aiting') === false)
                {
                    $validStates = false;
                }
            }
            return $validStates;
        }
        else
        {
            return false;
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
        $groupId = $position->getGroupId();
        if (!isset($this->bookedElements[$groupId]) and (!$position->flightBookerId))
        {
            echo "processing Flight";
            /** @var FlightBookerComponent $flightBookerComponent  */
            $flightBookerComponent = new FlightBookerComponent();
            $flightBookerComponent->setFlightBookerFromFlightVoyage($position->flightVoyage);

            $this->bookedElements[$groupId] = $groupId;
            $flightBookerComponent->getCurrent()->orderBookingId = $this->bookingModel->id;
            $flightBookerComponent->getCurrent()->save();
            $position->flightBookerId = $flightBookerComponent->getCurrent()->getPrimaryKey();
            Yii::app()->shoppingCart->update($position, 1);

            //VarDumper::dump($flightBookerComponent);
            $flightBookerId = $flightBookerComponent->getFlightBookerId();
            echo "FlightBookerId : $flightBookerId";
            //VarDumper::dump($passports);
            //die();
            /** @var $passports PassengerPassportForm[] */
            //foreach($passports as $passport)
            //{

            foreach ($passports->adultsPassports as $adultInfo)
            {
                $flightPassport = new FlightBookingPassport();
                //$flightPassport->attributes = $adultInfo->attributes;
                //$flightPassport->flightBookingId = $flightBookerId;
                $flightPassport->populate($adultInfo, $flightBookerId);
                $flightPassport->save();
            }
            foreach ($passports->childrenPassports as $childInfo)
            {
                $flightPassport = new FlightBookingPassport();
                //$flightPassport->attributes = $childInfo->attributes;
                //$flightPassport->flightBookingId = $flightBookerId;
                $flightPassport->populate($childInfo, $flightBookerId);
                $flightPassport->save();
            }
            foreach ($passports->infantPassports as $infantInfo)
            {
                $flightPassport = new FlightBookingPassport();
                //$flightPassport->attributes = $infantInfo->attributes;
                //$flightPassport->flightBookingId = $flightBookerId;
                $flightPassport->populate($infantInfo, $flightBookerId);
                $flightPassport->save();
            }
            //Yii::trace(CVarDumper::dumpAsString($flightPassport->errors), 'FlightBooker.EnterCredentials.flightPassport');
            //}
            //VarDumper::dump($flightBookerComponent);
            echo "GoTo Booking";
            $flightBookerComponent->status('booking');
            $status = $flightBookerComponent->getCurrent()->status;
            if ($status)
            {
                $this->endStatuses[$status] = $status;
            }
        }
    }

    public function processHotel($position)
    {
        Yii::trace("Processing HotelTrip", 'OrderComponent.processHotel');
        $groupId = $position->getId();
        if (isset($this->bookedElements[$groupId]) or (!$position->hotelBookerId))
        {
            Yii::trace("Already proccessed", 'OrderComponent.processHotel');
            return;
        }

        $this->bookedElements[$groupId] = $groupId; //mark as already booked

        Yii::trace("Create hotel booker", 'OrderComponent.proccessHotel');
        $hotelBookerComponent = $this->createHotelBookerComponent($position, $groupId);

        Yii::trace("Saving credentials for hotel", 'OrderComponent.proccessHotel');
        $this->saveCredentialsForHotel($position, $hotelBookerComponent);

        Yii::trace("Going to analyzing state", 'OrderComponent.proccessHotel');
        $hotelBookerComponent->status('analyzing');
        $status = $hotelBookerComponent->getCurrent()->status;

        $this->endStatuses[$status] = $status; //store info about current state
        Yii::trace("Processing HotelTrip Successfull", 'OrderComponent.processHotel');
    }

    private function saveCredentialsForHotel($position, $hotelBookerComponent)
    {
        $passports = $position->getPassports();
        $hotelBookerId = $hotelBookerComponent->getHotelBookerId();
        /** @var $passports HotelPassportForm */
        foreach ($passports->roomsPassports as $i => $roomPassport)
        {
            foreach ($roomPassport->adultsPassports as $adultInfo)
            {
                $hotelPassport = new HotelBookingPassport();
                $hotelPassport->scenario = 'adult';
                $hotelPassport->attributes = $adultInfo->attributes;
                $hotelPassport->hotelBookingId = $hotelBookerId;
                $hotelPassport->roomKey = $i;
                $validSaving = $hotelPassport->save();
                Yii::trace(CVarDumper::dumpAsString($hotelPassport->errors), 'HotelBooker.EnterCredentials.adultPassport');
            }
            foreach ($roomPassport->childrenPassports as $childInfo)
            {
                $hotelPassport = new HotelBookingPassport();
                $hotelPassport->scenario = 'child';
                $hotelPassport->attributes = $childInfo->attributes;
                $hotelPassport->hotelBookingId = $hotelBookerId;
                $hotelPassport->roomKey = $i;
                $validSaving = $hotelPassport->save();
                Yii::trace(CVarDumper::dumpAsString($hotelPassport->errors), 'HotelBooker.EnterCredentials.childPassport');
            }
        }
        return $validSaving;
    }

    public function createHotelBookerComponent($position)
    {
        /** @var HotelBookerComponent $hotelBookerComponent  */
        $hotelBookerComponent = new HotelBookerComponent();
        $hotelBookerComponent->setHotelBookerFromHotel($position->hotel);
        $hotelBookerComponent->getCurrent()->orderBookingId = $this->bookingModel->id;
        $hotelBookerComponent->getCurrent()->status = 'enterCredentials';
        $hotelBookerComponent->getCurrent()->save();
        $position->hotelBookerId = $hotelBookerComponent->getCurrent()->getPrimaryKey();
        Yii::app()->shoppingCart->update($position, 1);
        if ($hotelBookerComponent->getCurrent()->getErrors())
        {
            $errorMsg = VarDumper::dumpAsString($hotelBookerComponent->getCurrent()->getErrors());
            Yii::trace($errorMsg, 'OrderComponent.saveCredentialsForHotel');
            throw new Exception($errorMsg);
        }
        else
        {
            Yii::trace("HotelBooker id:" . $hotelBookerComponent->getCurrent()->id, 'OrderComponent.saveCredentialsForHotel');
        }
        return $hotelBookerComponent;
    }

    public function getOrderItems()
    {
        return $this->orderPositions['items'];
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
            $this->flightGroupId = $item->getGroupId();
            $this->bookedElements[$this->flightGroupId] = $this->flightGroupId;
            if ($flightBooker)
            {
                $this->endStatuses[$flightBooker->status] = $flightBooker->status;
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
                $this->endStatuses[$hotelBooker->status] = $hotelBooker->status;
                $this->createOrderBookingIfNotExist($hotelBooker->orderBookingId);
            }
        }
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
        if ($this->bookingModel = null)
        {
            $orderBooking = OrderBooking::model()->findByPk($orderBookingId);
            if ($orderBooking)
            {
                $this->bookingModel = $orderBooking;
            }
            else
            {
                $this->bookingModel = new OrderBooking();
                $this->bookingModel->attributes = ($this->getOrderParams()) ? $this->getOrderParams()->attributes : array('email' => 'test@test.ru', 'phone' => '9213546576');
                if (!$this->bookingModel->validate())
                {
                    $error = 'Validation of order booking fails: '.CVarDumper::dumpAsString($this->bookingModel->errors);
                    Yii::log($error, CLogger::LEVEL_ERROR, 'OrderComponent.createOrderBookingIfNotExist');
                    throw new CException($error);
                }
                $this->bookingModel->save();
            }
        }
        return $this->bookingModel;
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
        $this->orderPositions = $this->getPositions(false);
        foreach ($this->orderPositions['items'] as $item)
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
