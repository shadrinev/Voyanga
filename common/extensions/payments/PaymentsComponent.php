<?php
/**
 * Component to deal with payments.
 * Provides easy to use api for payment initiation and status checks.
 *
 * @author Anatoly Kudinov <kudinov@voyanga.com>
 * @copyright Copyright (c) 2012, EasyTrip LLC
 * @package payments
 */
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


}