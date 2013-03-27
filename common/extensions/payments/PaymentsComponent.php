<?php
/**
 * Component to deal with payments.
 * Provides easy to use api for payment initiation and status checks.
 *
 * @author Anatoly Kudinov <kudinov@voyanga.com>
 * @copyright Copyright (c) 2012, EasyTrip LLC
 * @package payments
 */
Yii::import("common.extensions.payments.models.Payments_MetaBooker");
Yii::import("common.extensions.payments.models.Payments_MetaBookerTour");
Yii::import("common.extensions.payments.models.Bill");


class PaymentsComponent extends CApplicationComponent
{
    /**
     * Array of credentials for different payment scenarios
     *
     * @var array
     */
    private $_credentials;
    public  $testMode;

    public $nemoCallbackSecret;

    public function setCredentials($value)
    {
        foreach ($value as $key=>$val) {
            if($val === false)
                unset($value[$key]);
        }

        $this->_credentials = $value;
    }

    public function getCredentials($channel)
    {
        return $this->_credentials[$channel];
    }

    /**
     *
     *
     *
     *
     * @return Bill bill for given booker
     */
    public function getBillForBooker($booker, $newBill=false)
    {
        $channel = 'ecommerce';
        if($booker instanceof FlightBookerComponent)
        {
            $booker = $booker->getCurrent();
        }

        if($booker instanceof FlightBooker)
        {
            if($booker->flightVoyage->webService=='SABRE')
            {
                $channel = $booker->flightVoyage->valAirline->payableViaSabre?'gds_sabre':'ltr';
                $channel = 'ltr';
            }
            if($booker->flightVoyage->webService=='GALILEO')
            {
                $channel = $booker->flightVoyage->valAirline->payableViaGalileo?'gds_galileo':'ltr';
#                $channel = 'ltr';
            }
        }

       if($booker->billId)
        {
            $billId = $booker->billId;

            if($newBill) {
                //! store reverse relation
                $connection = Yii::app()->db;
                $queries = array();
                $queries[] = "INSERT INTO bill_hotel_booking_history SELECT id, $billId FROM hotel_booking WHERE billId = $billId";
                $queries[] = "INSERT INTO bill_flight_booking_history SELECT id, $billId FROM flight_booking WHERE billId = $billId";
                foreach ($queries as $q) {
                    $connection->createCommand($q)->execute();
                }
            } else {
                return Bill::model()->findByPk($booker->billId);
            }
        }

        $bill = new Bill();
        $bill->setChannel($channel);
        $bill->status = Bill::STATUS_NEW;

        $bill->amount = $booker->price;
        $bill->save();
        $booker->billId = $bill->id;
        $booker->save();
        return $bill;
    }


    public function getFormParamsForBooker($booker)
    {
        $bill = $this->getBillForBooker($booker);
        $channel = $bill->getChannel();
        return $channel->formParams();
    }

    public function getTransactionsForBookers($bookers)
    {
        $result = array();
        foreach($bookers as $booker)
        {
            $isHotel = ($booker instanceof Payments_MetaBooker);
            $isTour = ($booker instanceof Payments_MetaBookerTour);

            if (!$isHotel&&!$isTour) {
                $entry = Array("transactions" => $booker->getPriceBreakDown(), "isHotel"=> $isHotel);
                if(!$isHotel)
                    $entry['title'] = "Перелет " . $booker->getSmallDescription();
                $result[] = $entry;
            }
        }
        return $result;
    }


    /*
      Группирует набор букеров для платежки.
      
      @param array $bookers оригинальный набор букеров привязанных к текущему заказу
      @param bool $newBill создавать ли новые счета взамен существующих
      @return array Возвращает массив сгруппированных для платежки букеров.
     */
    public function preProcessBookers($bookers, $newBill=false)
    {
        $rest = array();
        $hotels = array();
        foreach($bookers as $booker){
            if($booker instanceof HotelBooker) {
                $hotelBookerComponent = new HotelBookerComponent();
                $hotelBookerComponent->setHotelBookerFromId($booker->id);
                $booker = $hotelBookerComponent;
            }
            if($booker instanceof HotelBookerComponent)
                $hotels[] = $booker;
            else
                $rest[] = $booker;
        }
        $hasHotels = count($hotels);
        $hasFlights = count($rest);
        if($hasHotels && !$hasFlights)
            return $this->onlyHotelsCase($hotels, $newBill);
        if(!$hasHotels && ($hasFlights == 1)) {
            $this->getBillForBooker($rest[0], $newBill);
            return $rest;
        }
        if($hasFlights)
            return $this->tourCase($rest, $hotels, $newBill);
    }

    private function onlyHotelsCase($hotels, $newBill) {
        # FIXME: check if this call is needed
        $bill = $this->getBillForBooker($hotels[0]->getCurrent(), $newBill);
        $billId = $bill->id;
        $metaBooker = new Payments_MetaBooker($hotels, $billId);
        return array($metaBooker);
    }

    private function tourCase($flights, $hotels, $newBill) {
        # FIXME: check if this call is needed
        $bill = $this->getBillForBooker($flights[0], $newBill);
        $bill->setChannel('ltr');
        $bill->save();
        $billId = $bill->id;
        $metaBooker = new Payments_MetaBookerTour(array_merge($flights, $hotels), $flights[0], $billId);
        //! FIXME FIXME
        $bill->amount = $metaBooker->getPrice();
        $bill->save();
        return array($metaBooker);
    }

    public function notifyNemo($booker)
    {
        if($booker instanceof Payments_MetaBookerTour)
        {
            foreach($booker->getBookers() as $book)
            {
                $this->notifyNemo($book);
            }
            return;
        }
        //! Notify on avia only
        $isHotel = $booker instanceof HotelBookerComponent;
        $isMetaHotel =  $booker instanceof Payments_MetaBooker;
        if(!$isHotel&&!$isMetaHotel)
        {
            $bill = $this->getBillForBooker($booker);
            $this->notifyNemoRaw($booker, $bill);
            //$taskId = $booker->getCurrent()->getTaskInfo('paymentTimeLimit')->taskId;
            //echo "Removing booking timelimit task #" . $taskId;
            //$result = Yii::app()->cron->delete($taskId);
            //echo " " . $result . "\n";
        }
    }


    //! tell nemo we are done with payment for given pnr
    private function notifyNemoRaw($booker, $bill)
    {
        if($booker instanceof FlightBookerComponent)
        {
            $booker = $booker->getCurrent();
        }
//        if($bill->getChannelName()=='gds_galileo')
//            return;
        $fb = $booker;
        $params['locator'] = $fb->pnr;
        $params['type']='FLIGHTS';
        $params['booking_id']=$fb->nemoBookId;
        $params['user_id']='44710';
        $params['ext_id'] = $bill->transactionId;
        $stringToSign = implode('', $params);
        $stringToSign .= $this->nemoCallbackSecret;
        $params['sig'] = md5($stringToSign);
        $requestParams = Array();
        foreach ($params as $key => $value) {
            $requestParams[]=$key.'='.urlencode($value);
        }
        $url = '/index.php?go=payment/bill&';
        $url.= implode('&', $requestParams);
        list($code, $data) =  Yii::app()->httpClient->get(Yii::app()->params['GDSNemo']['apiHost'] . $url);
        if($code!=200)
            return false;
//            throw new Exception("Nemo callback failure");
        return true;
    }
    //! FIXME MOVE TO ORDER?
    public function getStatus($booker)
    {
        $status = $booker->getStatus();
        $parts = explode("/", $status);
        if(count($parts)==2)
            return $parts[1];
        return $parts[0];
    }
}