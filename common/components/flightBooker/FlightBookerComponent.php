<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 19.06.12
 * Time: 10:42
 */
class FlightBookerComponent extends CApplicationComponent
{
    /** @var FlightBooker */
    private $flightBooker;
    /** @var FlightVoyage */
    private $flightVoyage;

    public function init()
    {
        Yii::setPathOfAlias('flightBooker', realpath(dirname(__FILE__)));
        Yii::import('flightBooker.actions.*');
        Yii::import('flightBooker.*');
    }

    public function setFlightVoyage($value)
    {
        $this->flightVoyage = $value;
    }

    private function loadModel()
    {
        if ($this->flightBooker==null)
        {
            $id = Yii::app()->user->getState('flightVoyageId');
            $this->flightBooker = FlightBooker::model()->findByAttributes(array('flightVoyageId'=>$id));
            $this->flightBooker->setFlightBookerComponent($this);
        }
        return $this->flightBooker;
    }

    public function getCurrent()
    {
        return $this->loadModel();
    }

    public function getStatus()
    {
        if ($this->flightBooker!=null)
            return $this->flightBooker->status;
        return 'search';
    }

    public function book()
    {
        //if we don't have a flight OR we moved to another flight
        if ($this->getCurrent()==null || ($this->getCurrent()->flightVoyage->id != $this->flightVoyage->getId()))
        {
            //if we don't have a flight AND we moved to another flight
            if (($this->getCurrent()!=null) and $this->getCurrent()->flightVoyage->id != $this->flightVoyage->getId())
            {
                $this->flightBooker = FlightBooker::model()->findByAttributes(array('flightVoyageId'=>$this->flightVoyage->getId()));
                if(!$this->flightBooker)
                {
                    $this->flightBooker = new FlightBooker();
                    $this->flightBooker->flightVoyageId = $this->flightVoyage->getId();
                    $this->flightBooker->flightVoyage = $this->flightVoyage;
                    $this->flightBooker->status = 'enterCredentials';
                    $this->flightBooker->setFlightBookerComponent($this);
                    if(!$this->flightBooker->save())
                    {
                        VarDumper::dump($this->flightBooker->getErrors());
                    }
                }
                $this->flightBooker->setFlightBookerComponent($this);
            }
            if ($this->flightBooker == null)
            {
                Yii::trace('New flightBooker to db', 'FlightBookerComponent.book');
                $this->flightBooker = new FlightBooker();
                $this->flightBooker->flightVoyageId = $this->flightVoyage->getId();
                $this->flightBooker->flightVoyage = $this->flightVoyage;
                $this->flightBooker->status = 'enterCredentials';
                $this->flightBooker->setFlightBookerComponent($this);
                if(!$this->flightBooker->save())
                {
                    VarDumper::dump($this->flightBooker->getErrors());
                }
            }

        }

        Yii::trace(CVarDumper::dumpAsString($this->flightBooker->getErrors()), 'FlightBookerComponent.book');
        if (!$this->flightBooker->id)
        {
            $this->flightBooker->id = $this->flightBooker->primaryKey;
        }

        Yii::app()->user->setState('flightVoyageId', $this->flightBooker->flightVoyage->id);
    }

    public function status($newStatus)
    {
        $this->flightBooker->status = $newStatus;
        $this->flightBooker->save();
    }

    public function stageBooking()
    {
        $flightBookingParams = new FlightBookingParams();
        $orderBooking = $this->flightBooker->orderBooking;
        $flightBookingParams->contactEmail = $orderBooking->email;
        $flightBookingParams->phoneNumber = $orderBooking->phone;
        $flightBookingParams->flightId = $this->flightBooker->flightVoyage->flightKey;

        foreach($this->flightBooker->flightBookingPassports as $passport)
        {
            $passenger = new Passenger();
            $passenger->type = $passport->getType();
            $passenger->passport = $passport;
            $flightBookingParams->addPassenger($passenger);
        }

        $flightBookingResponse = Yii::app()->gdsAdapter->FlightBooking($flightBookingParams);

        SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,GDSNemoAgency::$requestIds);
        GDSNemoAgency::$requestIds = array();

        if($flightBookingResponse->responseStatus == ResponseStatus::ERROR_CODE_NO_ERRORS)
        {
            $this->flightBooker->nemoBookId = $flightBookingResponse->nemoBookId;
            $this->flightBooker->pnr = $flightBookingResponse->pnr;
            $this->flightBooker->timeout = date('y-m-d H:i:s',$flightBookingResponse->expiration);
//            $res = Yii::app()->cron->add(strtotime($this->flightBooker->timeout), 'flightbooking','ChangeState',array('flightBookerId'=>$this->flightBooker->id,'newState'=>'bookingTimeLimitError'));
//            if($res)
//            {
//                $this->flightBooker->saveTaskInfo('paymentTimeLimit',$res);
//            }
            $this->status('waitingForPayment');
        }
        else
        {
            $this->status('bookingError');
        }
    }

    public function stageWaitingForPayment()
    {
    }

    public function stageBookingError()
    {
        echo "Booking error happens!";
        $this->status('error');
    }

    public function stageBookingTimeLimitError()
    {
        echo 'Try cancel flight booking';
        $bookingId = $this->flightBooker->nemoBookId;
        echo 'Booking id '.$bookingId;
        $result = Yii::app()->gdsAdapter->cancelBooking($bookingId);
        CVarDumper::dump($result);
        SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,GDSNemoAgency::$requestIds);
        CVarDumper::dump(SWLogActiveRecord::$requestIds);
        GDSNemoAgency::$requestIds = array();
        if($result){
            //$this->status('error');
        }
    }

/*    public function stageStartPayment()
    {
        $res = Yii::app()->cron->add(time() + appParams('time_for_payment'), 'flightbooking', 'ChangeState', array('flightBookerId' => $this->flightBooker->id, 'newState' => 'waitingForPayment'));
        if ($res)
        {
            $this->flightBooker->saveTaskInfo('paymentTimeLimit', $res);
            return true;
        }
    } */

    public function stageTicketing()
    {
        /** @var FlightBookingResponse $flightBookingResponse  */
        $flightTicketingParams = new FlightTicketingParams();
        $flightTicketingParams->nemoBookId = $this->flightBooker->nemoBookId;
        $flightTicketingParams->pnr = $this->flightBooker->pnr;
        $flightTicketingResponse = Yii::app()->gdsAdapter->FlightTicketing($flightTicketingParams);
        SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,GDSNemoAgency::$requestIds);
        GDSNemoAgency::$requestIds = array();

#        VarDumper::dump($flightTicketingResponse);
        if($flightTicketingResponse->responseStatus == ResponseStatus::ERROR_CODE_NO_ERRORS)
        {
            //saving tickets numbers to DB
            $flightPassports = FlightBookingPassport::model()->findAllByAttributes(array('flightBookingId'=>$this->flightBooker->id));
            $docSortPassports = array();
            if($flightPassports)
            {
                foreach($flightPassports as $passport)
                {
                    $docNum = $passport->series . $passport->number;
                    $docSortPassports[$docNum] = $passport;
                }
                if($flightTicketingResponse->tickets)
                {
                    foreach($flightTicketingResponse->tickets as $ticketInfo){
                        if(isset($ticketInfo['documentNumber']) and isset($docSortPassports[$ticketInfo['documentNumber']])){
                            //TODO: add ticketNumber field to DB (FlightBookingPassport) and save it;
                            //$docSortPassports[$ticketInfo['documentNumber']]->
                            $docSortPassports[$ticketInfo['documentNumber']]->ticketNumber = $ticketInfo['ticketNumber'];
                            $docSortPassports[$ticketInfo['documentNumber']]->save();
                        }
                    }
                }
            }
            $flightVoyage = $this->flightBooker->flightVoyage;
            $flightVoyage->updateFlightParts($flightTicketingResponse->aParts);
            $this->flightBooker->flightVoyage = $flightVoyage;
        }
        else
        {
            $this->status('ticketingRepeat');
            return;
        }
        $this->status('done');
    }

    public function stageTicketingRepeat()
    {
        $this->status('ticketingError');
        return;
//pass;
        $this->flightBooker->tryCount++;
        $this->flightBooker->save();
        if ($this->flightBooker->tryCount > 3)
        {
            $this->status('ticketingError');
        }
        else
        {

            $flightTicketingParams = new FlightTicketingParams();
            $flightTicketingParams->nemoBookId = $this->flightBooker->nemoBookId;
            $flightTicketingParams->pnr = $this->flightBooker->pnr;
            /** @var FlightTicketingResponse $flightTicketingResponse  */
            $flightTicketingResponse = Yii::app()->gdsAdapter->FlightTicketing($flightTicketingParams);
            SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,GDSNemoAgency::$requestIds);
            GDSNemoAgency::$requestIds = array();

            if ($flightTicketingResponse->status == 1)
            {

                    $this->status('done');
            }
            else
            {
                //TODO: переставить стутус через время T + считать количество раз.
                $res = Yii::app()->cron->add(time() + appParams('flight_repeat_time'), 'flightbooking','ChangeState',array('flightBookerId'=>$this->flightBooker->id,'newState'=>'ticketingRepeat'));
                if($res)
                {
                    $this->flightBooker->saveTaskInfo('ticketingRepeat',$res);
                    return true;
                }
                //$this->status('ticketingRepeat');
            }
        }
    }

    public function stageManualProcessing()
    {

    }

    public function stageManualTicketing()
    {

    }

    public function stageTicketingError()
    {
//        $this->status('moneyReturn');
    }

    public function stageManualError()
    {
        $this->status('error');
    }

    public function stageMoneyReturn()
    {
        // TODO: return money function
        // $this->status('error');
    }

    public function stageManualSuccess()
    {
        $this->status('done');
    }

    public function stageConfirmMoney()
    {
        Yii::app()->payments->confirm($bill);
    }

    public function stageDone()
    {

    }

    public function stageError()
    {
        Yii::log('Flight booking finsihed with error', CLogger::LEVEL_ERROR);
        Yii::app()->end();
    }

    public function setFlightBookerFromId($flightBookerId)
    {
        $this->flightBooker = FlightBooker::model()->findByPk($flightBookerId);

        if(!$this->flightBooker) throw new CException('FlightBooker with id '.$flightBookerId.' not found');
        $this->flightBooker->setFlightBookerComponent($this);
    }

    public function setFlightBookerFromFlightVoyage(FlightVoyage $flightVoyage, $searchParams)
    {
        $this->flightBooker = new FlightBooker();
        $this->flightBooker->flightVoyage = $flightVoyage;
        $this->flightBooker->status = 'enterCredentials';
        $this->flightBooker->price = $flightVoyage->price;
        $this->flightBooker->searchParams = serialize($searchParams);
        $this->flightBooker->setFlightBookerComponent($this);
    }

    public function getFlightBookerId()
    {
        return $this->flightBooker->id;
    }

    public function getPriceBreakdown()
    {
        $payments = Yii::app()->payments;
        $bill = $payments->getBillForBooker($this->flightBooker);
        $channel = $bill->getChannelName();
        $result = Array();
        $charges = $this->flightBooker->getFlightVoyage()->charges;
        if ($channel=='ltr') {
            // Show single transaction
            $result[] = Array("title" => "тариф, таксы и сбор", "price" => $this->flightBooker->price);
            return $result;
        }
        if($charges < 0)
            $charges = 0;
        $result[] = Array("title" => "тариф и таксы", "price" => $this->flightBooker->price - $charges);
        if($charges > 0 )
           $result[] = Array("title" => "сервисный сбор", "price" => $this->flightBooker->getFlightVoyage()->charges);
        return $result;
    }

    //! return MOW - LED
    public function getSmallDescription()
    {
        return $this->flightBooker->getSmallDescription();
    }

    public function getSKU()
    {
        return $this->flightBooker->getSKU();

    }

}
