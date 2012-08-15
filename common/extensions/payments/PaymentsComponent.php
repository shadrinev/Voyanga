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
     * @return Bill bill for given hotel booker
     */
    public function getBillForHotelBooker($hotelBooker)
    {
        Yii::import("common.extensions.payments.models.Bill");
        if($hotelBooker->billId) {
            return Bill::model()->findByPk($hotelBooker->billId);
        }
        $bill = new Bill();
        $bill->status = Bill::STATUS_NEW;
        $bill->amount = $hotelBooker->price;
        $bill->save();
        $hotelBooker->billId = $bill->id;
        $hotelBooker->save();
        return $bill;
    }

    public function getParamsFor($channel)
    {
        // FIXME
        $credentials = $this->_credentials[$channel];
        $result = Array();
        $result['MerchantId'] = $credentials['id'];
        return $result;
    }

    public function getSignatureFor($channel, $params)
    {
        $credentials = $this->_credentials[$channel];
        $keys = Array('MerchantId', 'OrderId', 'Amount', 'Currency', 'ValidUntil', 'TransactionId');
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
           if($result['Status'] == 'Pending')
           {
               $bill->status = Bill::STATUS_PREAUTH;
               $bill->save();
           }
       }
   }

   /*
    * Confirm preauth
    */
   public function confirm($bill)
   {
       //! FIXME shuld we only accept bills in certain states ?
       $url = 'https://secure.payonlinesystem.com/payment/transaction/complete/';
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
       var_dump($url);
       list($code, $data) = Yii::app()->httpClient->get($url);
       if(strlen($data))
       {
           $result = Array();
           parse_str($data, $result);
           var_dump($result);
           // FIXME check AMOUNT?
           if($result['Result'] == 'Ok')
           {
               $bill->status = Bill::STATUS_PAID;
               $bill->save();
           }
       }

   }

}