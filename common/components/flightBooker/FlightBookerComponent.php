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
        //getting pnr and other stuff
        //VarDumper::dump($this);
        //return;

        //$this->flightBooker->booking->bookingPassports;
        $flightBookingParams = new FlightBookingParams();

        //VarDumper::dump($this);die();
        $orderBooking = $this->flightBooker->orderBooking;
        $flightBookingParams->contactEmail = $orderBooking->email;
        $flightBookingParams->phoneNumber = $orderBooking->phone;
        $flightBookingParams->flightId = $this->flightBooker->flightVoyage->flightKey;

        foreach($this->flightBooker->flightBookingPassports as $passport)
        {
            $passenger = new Passenger();
            $passenger->type = Passenger::TYPE_ADULT;
            $passenger->passport = $passport;
            $flightBookingParams->addPassenger($passenger);
        }
        //$flightBookingParams->addPassenger();
        //echo 123;//die();
        /** @var FlightBookingResponse $flightBookingResponse  */
        $flightBookingResponse = Yii::app()->gdsAdapter->FlightBooking($flightBookingParams);

        SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,GDSNemoAgency::$requestIds);
        GDSNemoAgency::$requestIds = array();

        if($flightBookingResponse->responseStatus == ResponseStatus::ERROR_CODE_NO_ERRORS)
        {
            $this->flightBooker->nemoBookId = $flightBookingResponse->nemoBookId;
            $this->flightBooker->pnr = $flightBookingResponse->pnr;
            $this->flightBooker->timeout = date('y-m-d H:i:s',$flightBookingResponse->expiration);
            $this->status('waitingForPayment');
        }
        else
        {
            $this->status('bookingError');
        }
    }

    public function stageWaitingForPayment()
    {
        //maybe we need to remove it?
        //TODO: ставим таймер на отмену приема платежа
        //переход в состояние payment должен быть инициализирован из вне
        //$this->status('payment');
        //oleg: incorrect time assign
        sleep(3);
        //$this->flightBooker->saveTaskInfo('paymentTimeLimit',565657);

        $res = Yii::app()->cron->add(strtotime($this->flightBooker->timeout), 'FlightBooker','ChangeState',array('flightBookerId'=>$this->flightBooker->id,'newState'=>'bookingTimeLimitError'));
        if($res)
        {
            $this->flightBooker->saveTaskInfo('paymentTimeLimit',$res);
            return true;
        }/**/
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

    public function stageStartPayment()
    {
        $res = Yii::app()->cron->add(time() + appParams('time_for_payment'), 'FlightBooker', 'ChangeState', array('flightBookerId' => $this->flightBooker->id, 'newState' => 'waitingForPayment'));
        if ($res)
        {
            $this->flightBooker->saveTaskInfo('paymentTimeLimit', $res);
            return true;
        }
    }

    public function stageTicketing()
    {
        /** @var FlightBookingResponse $flightBookingResponse  */
        $flightTicketingParams = new FlightTicketingParams();
        $flightTicketingParams->nemoBookId = $this->flightBooker->nemoBookId;
        $flightTicketingParams->pnr = $this->flightBooker->pnr;
        $flightTicketingResponse = Yii::app()->gdsAdapter->FlightTicketing($flightTicketingParams);
        SWLogActiveRecord::$requestIds = array_merge(SWLogActiveRecord::$requestIds,GDSNemoAgency::$requestIds);
        GDSNemoAgency::$requestIds = array();

        //VarDumper::dump($flightTicketingResponse);
        //die();
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
                        }
                    }
                }
            }


        }
        else
        {
            $this->status('ticketingRepeat');
        }
        $this->status('ticketReady');
    }

    public function stageTicketReady()
    {
        $this->status('done');
    }

    public function stageTicketingRepeat()
    {
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

                    $this->status('ticketReady');
            }
            else
            {
                //TODO: переставить стутус через время T + считать количество раз.
                $res = Yii::app()->cron->add(time() + appParams('flight_repeat_time'), 'FlightBooker','ChangeState',array('flightBookerId'=>$this->flightBooker->id,'newState'=>'ticketingRepeat'));
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
        $this->status('moneyReturn');
    }

    public function stageManualError()
    {
        $this->status('error');
    }

    public function stageMoneyReturn()
    {
        //TODO: return money function
        $this->status('error');
    }

    public function stageManualSuccess()
    {
        $this->status('done');
    }

    public function stageBspTransfer()
    {
        //TODO: send money to BSP gate
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

    public function setFlightBookerFromFlightVoyage(FlightVoyage $flightVoyage)
    {
        $this->flightBooker = new FlightBooker();
        $this->flightBooker->flightVoyage = $flightVoyage;
        $this->flightBooker->status = 'enterCredentials';
        $this->flightBooker->price = $flightVoyage->price;
        $this->flightBooker->setFlightBookerComponent($this);
        //VarDumper::dump($this);


    }

    public function getFlightBookerId()
    {
        return $this->flightBooker->id;
    }
}
