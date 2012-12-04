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
}