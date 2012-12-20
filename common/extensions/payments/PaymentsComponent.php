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
            if($booker instanceof FlightBookerComponent)
            {
                $booker = $booker->getCurrent();
            }

            $isHotel = ($booker instanceof Payments_MetaBooker);
            $entry = Array("amount" => $booker->price, "isHotel"=> $isHotel);
            if(!$isHotel)
                $entry['title'] = $booker->getSmallDescription();
            $result[] = $entry;

        }
//        $bill = $this->getBillForBooker($booker);
//        $channel = $bill->getChannel();
        return $result;
    }


    /**
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
        if(count($hotels)) {
            $billId = $hotels[0]->getCurrent()->billId;
            foreach($hotels as $hotel) {
                if($hotel->getCurrent()->billId != $billId)
                    throw new Exception("Hotel set for payment is broken");
            }
            $metaBooker = new Payments_MetaBooker($hotels, $billId);
            $rest[]=$metaBooker;
        }
        return $rest;
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