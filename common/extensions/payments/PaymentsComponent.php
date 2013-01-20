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

class PaymentsComponent extends CApplicationComponent
{
    /**
     * Array of credentials for different payment scenarios
     *
     * @var array
     */
    private $_credentials;
    public $nemoCallbackSecret;

    public function setCredentials($value)
    {
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
    public function getBillForBooker($booker)
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
            }

        }
        Yii::import("common.extensions.payments.models.Bill");
        if($booker->billId)
        {
            return Bill::model()->findByPk($booker->billId);
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
//        $bill = $this->getBillForBooker($booker);
//        $channel = $bill->getChannel();
        return $result;
    }


    /*
       prepare bookers for payments component,
       ATM only wraps hotels into Payments_MetaBooker
     */
    public function preProcessBookers($bookers)
    {
        $rest = array();
        $hotels = array();
        foreach($bookers as $booker){
            if($booker instanceof HotelBookerComponent)
                $hotels[] = $booker;
            else
                $rest[] = $booker;
        }
        $hasHotels = count($hotels);
        $hasFlights = count($rest);
        if($hasHotels && !$hasFlights)
            return $this->onlyHotelsCase($hotels);
        if(!$hasHotels && ($hasFlights == 1))
            return $rest;
        if($hasFlights)
            return $this->tourCase($rest, $hotels);
    }

    private function onlyHotelsCase($hotels) {
        # FIXME: check if this call is needed
        $bill = $this->getBillForBooker($hotels[0]->getCurrent());
        $billId = $bill->id;
        $metaBooker = new Payments_MetaBooker($hotels, $billId);
        return array($metaBooker);
    }

    private function tourCase($flights, $hotels) {
        # FIXME: check if this call is needed
        $bill = $this->getBillForBooker($flights[0]);
        $bill->setChannel('ltr');
        $bill->save();
        $billId = $bill->id;
        $metaBooker = new Payments_MetaBookerTour(array_merge($flights, $hotels), $flights[0], $billId);
        //! FIXME FIXME
        $bill->amount = $metaBooker->getPrice();
        $bill->save();
        return array($metaBooker);
    }


    //! tell nemo we are done with payment for given pnr
    public function notifyNemo($booker, $bill)
    {
        if($booker instanceof FlightBookerComponent)
        {
            $booker = $booker->getCurrent();
        }

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
        list($code, $data) =  Yii::app()->httpClient->get('http://easytrip.nemo-ibe.com' . $url);
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