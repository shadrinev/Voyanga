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
        $bill->channel=$channel;
        $bill->status = Bill::STATUS_NEW;
        $bill->amount = $booker->price;
        $bill->save();
        $booker->billId = $bill->id;
        $booker->save();
        return $bill;
    }

    public function getParamsForBillAndBooker($bill, $booker)
    {
        $credentials = $this->_credentials[$bill->channel];
        $params = Array();
        $params['MerchantId'] = $credentials['id'];
        //! FIXME: can this amount change?
        $params['Amount'] = sprintf("%.2f", $bill->amount);
        $params['Currency'] = 'RUB';
        $params['OrderId'] = 'adev-' . $bill->id;

        if ($bill->channel == 'gds_sabre')
        {
            //! FIXME: implement commission split
            $params['Commission'] = sprintf("%.2f", $booker->flightVoyage->commission);
            $params['PNR'] = $booker->pnr;
        }
        $params['SecurityKey'] = $this->getSignatureFor($bill->channel, $params);
        return $params;
    }

    public function getParamsFor($channel)
    {
        // FIXME
        return $result;
    }

    public function getSignatureFor($channel, $params)
    {
        $credentials = $this->_credentials[$channel];
        $keys = Array('MerchantId', 'DateTime', 'TransactionID', 'OrderId',
                      'IData', 'Amount', 'Currency', 'Commission', 'PNR',
                      'ValidUntil', 'TransactionId');
        $values = Array();
        foreach($keys as $key)
        {
            if(isset($params[$key]))
                $values[]= $key.'='.$params[$key];
        }
        $values[] = 'PrivateSecurityKey='.$credentials['key'];
        $stringToSign = implode('&', $values);
        return md5($stringToSign);
    }

    /**
     * Returns (almost) everything uniteller can tell us about given $billId
     * @return array
     */
    public function getDataByBillId($billId)
    {
        //! FIXME switch to post here
        //! FIXME it could throw exception
        $data = Yii::app()->httpClient->get(Yii::app()->payments->statusUrl);
        echo $data[1];
        $data = new SimpleXmlElement($data[1]);
        //! FIXME it possibly could be an array
        // var_dump($data->orders->order);
        if(count($data->orders->order)!=1)
            throw new Exception("Whoops");
        $keys = array("ordernumber",
                      "response_code",
                      "billnumber",
                      "currency",
                      "status",
                      "total");
        $result = array();
        foreach($keys as $key)
        {
            $result[$key]=(string)$data->orders->order[0]->{$key};
        }
        return $result;
    }

   public function setCredentials($value)
   {
       $this->_credentials = $value;
   }


   /**
    * Fetch status from payonline. Update database record.
    */
   public function updateBillStatus($bill)
   {
       //! FIXME shuld we only accept bills in certain states ?
       $url = 'https://secure.payonlinesystem.com/payment/search/';
       $context = $this->getParamsFor($bill->channel);
       $context['TransactionId'] = $bill->transactionId;
       $context['ContentType'] = 'text';
       $context['SecurityKey'] = $this->getSignatureFor($bill->channel, $context);
       $params = Array();
       foreach($context as $key=>$value)
       {
           $params[]=$key.'='.$value;
       }
       $url .= '?';
       $url.=implode('&', $params);
       list($code, $data) = Yii::app()->httpClient->get($url);
       if(strlen($data))
       {
           $result = Array();
           parse_str($data, $result);
           // FIXME check AMOUNT?
           if($result['Status'] == 'PreAuthorized')
           {
               $bill->status = Bill::STATUS_PREAUTH;
               $bill->save();
           }
       }
   }

   /*
    * Confirm preauth
    */
   public function confirm($bill, $booker=null)
   {
       //! FIXME shuld we only accept bills in certain states ?
       $url = 'https://secure.payonlinesystem.com/payment/transaction/complete/';
       $context = $this->getParamsFor($bill->channel);
       $context['TransactionId'] = $bill->transactionId;
       $context['ContentType'] = 'text';
       //! Add long tail record
       if ($bill->channel=='ltr') {
           $context['IData'] = $this->getIData($booker);
       }

       $context['SecurityKey'] = $this->getSignatureFor($bill->channel, $context);
       $params = Array();
       foreach($context as $key=>$value)
       {
           $params[]=$key.'='.$value;
       }
       $url .= '?';
       $url.=implode('&', $params);
       list($code, $data) = Yii::app()->httpClient->get($url);
       if(strlen($data))
       {
           $result = Array();
           parse_str($data, $result);
           // FIXME check AMOUNT?
           if($result['Result'] == 'Ok')
           {
               $bill->status = Bill::STATUS_PAID;
               $bill->save();
           }
       }

   }

   public function getIData($booker)
   {
       if(!$booker)
           throw new Exception("booker was not passed to confirm");
       $flightVoyage = $booker->flightVoyage;
       $ltr = "";
       $ltr.= "01"; // CONST
       $ltr.= "        "; // AGENCY CODE
       //! FIXME: WHAT IF WE HAVE MORE THAN 4 FLIGHTS????!!!1111
       $i = 0;
       foreach($flightVoyage->flights as $flight)
       {
           foreach($flight->flightParts as $part){
               $i++;
               $ltr.= $part->transportAirlineCode; //CARREIR LEGN
               $ltr.= "X"; //SERVICE CLASS LEGN
               $ltr.= " "; //STOPOVER CODE LEGN //optional
               $ltr.= $part->arrivalAirport->code; //DEST CITY LEGN

               if($i>3)
                   break 2;
           }
       }
       //! FIXME did not tested for edge case
       $ltr.= str_repeat('       ', 4-$i);
       $ltr.= "1"; // RESTRICTED TICKET INDICATOR // NELZA VERNUT
       //! FIXME !
       $ltr.= "12345678901234"; // TICKET NUMBER
       //! FIXME check how de focking php TZs are working
       list($date, $time) = explode("T",$flightVoyage->flights[0]->departureDate);
       list($year, $month, $date) = explode('-', $date);
       $ltr.= $month.$date.($year%100);
       //! FIXME Write getters for this
       $ltr.= $flightVoyage->flights[0]->flightParts[0]->departureAirport->code;
       //! FIXME can we get unicode here ?
       $name = $booker->flightBookingPassports[0]->firstName . ' ' .$booker->flightBookingPassports[0]->lastName;
       if(strlen($name)>20)
       {
           $name = substr($name, 0, 20);
       }
       else
       {
           $diff = 20-strlen($name);
           $name .= str_repeat(' ', $diff);
       }
       $ltr.= $name;

       if(!preg_match("~01[ \w]{8}[A-Z]{2}[ \w]{1}[ O]{1}[A-Z]{3}(([A-Z]{2}[ \w]{1}[ O]{1}[A-Z]{3})|[ ]{7}){3}[ 01]{1}[ \w-]{14}[\d]{6}[A-Z]{3}[ \w/-]{20}~", $ltr))
           throw new Exception("Wrong LTR generated");
       return $ltr;
   }

}