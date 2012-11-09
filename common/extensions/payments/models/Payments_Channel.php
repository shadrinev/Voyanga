<?php

abstract class Payments_Channel {
    protected $bill;
    protected $name = 'foo';

    public function __construct($bill, $booker) {
        $this->bill = $bill;
        $this->booker = $booker;
        $this->credentials = Yii::app()->payments->getCredentials($this->name);
    }

    public function formParams() {
        $credentials = $this->credentials;
        $order = Yii::app()->order->getOrderBooking();
        $params = Array();
        $params['MerchantId'] = $credentials['id'];
        //! FIXME: can this amount change?
        $params['Amount'] = sprintf("%.2f", 50);//  $this->bill->amount);
        $params['Currency'] = 'RUB';
        # FIXME FIXME FIXME FIXME
        $params['OrderId'] = $order->id . '-' . $this->bill->id;
        // FIXME
        $params['Email'] = $order->email;
        $params['Phone'] = $order->phone;
        $params['Country'] = 'Russia';
        $params['City'] = 'Moscow';
        $params['Zip'] = '12354';

        $params['SecurityKey'] = $this->getSignature($params);
        return $params;
    }

    public function getSignature($params)
    {
        $credentials = $this->credentials;
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

    public function confirm()
    {
        //! FIXME shuld we only accept bills in certain states ?
        $url = 'https://secure.payonlinesystem.com/payment/transaction/complete/';
        $context = array();
        $context['TransactionId'] = $this->bill->transactionId;
        $context['ContentType'] = 'text';
        $context = $this->contributeToConfirm($context);

        $context['SecurityKey'] = $this->getSignatureFor($bill->channel, $context);
        list($code, $data) = $this->callApi($url, $context);
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

    public function refund()
    {


    }

    protected function contributeToConfirm($context)
    {
        return $context;
    }

    protected function callApi($url, $context)
    {
        $params = Array();
        foreach($context as $key=>$value)
        {
            $params[]=$key.'='.$value;
        }
        $url = '?';
        $url.= implode('&', $params);
        return Yii::app()->httpClient->get($url);
    }
}