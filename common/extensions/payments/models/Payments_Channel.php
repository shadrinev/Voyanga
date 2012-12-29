<?php

abstract class Payments_Channel {
    protected $bill;
    protected $name = 'foo';

    public function __construct($bill, $booker) {
        $this->bill = $bill;
        $this->booker = $booker;
        $this->credentials = Yii::app()->payments->getCredentials($this->name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function formParams() {
        $credentials = $this->credentials;
        $order = Yii::app()->order->getOrderBooking();
        $params = Array();
        $params['MerchantId'] = $credentials['id'];
        //! FIXME: can this amount change?
        $params['Amount'] = sprintf("%.2f", $this->bill->amount);
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

    public function getSignature($params, $strategy='ignorerebill')
    {
        $credentials = $this->credentials;
        if($strategy=='ignorerebill') {
            $keys = Array('MerchantId', 'DateTime', 'TransactionID','TransactionId', 'OrderId',
                          'IData', 'Amount', 'Currency', 'Commission', 'PNR',
                          'ValidUntil');
        } else {
            $keys = Array('MerchantId', 'RebillAnchor', 'OrderId', 'Amount', 'Currency');
        }
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
        $url = 'transaction/complete/';
        $context = array();
        $context['TransactionId'] = $this->bill->transactionId;
        $context['ContentType'] = 'text';
        $context = $this->contributeToConfirm($context);

        $context['SecurityKey'] = $this->getSignatureFor($bill->channel, $context);
        list($code, $result) = $this->callApi($url, $context);
            // FIXME check AMOUNT?
        if($result['Result'] == 'Ok')
        {
            $bill->status = Bill::STATUS_PAID;
            $bill->save();
        }
    }

    public function rebill($anchor)
    {
        $allParams = $this->formParams();
        $entry = PaymentLog::forMethod('rebill');
        $entry->orderId = $allParams['OrderId'];
 
        # FIXME FIXME FIXME doublecheck status
        if($this->bill->transactionId) {
            $entry->errorDescription = "Attemp to rebill bill with transaction, skipping";
            $entry->save();
            return true;
        }

        $params['MerchantId'] = $allParams['MerchantId'];
        $params['RebillAnchor'] = $anchor;
        $params['OrderId'] = $allParams['OrderId'];
        $params['Amount'] = $allParams['Amount'];
        $params['Currency'] = $allParams['Currency'];
        $params['SecurityKey'] = $this->getSignature($params, 'rebill');
        $entry->request = json_encode($params);
        $entry->startProfile();
        $entry->save();
        list($code,$result) = $this->callApi('transaction/rebill', $params);
        $entry->finishProfile();
        $entry->response = json_encode($result);
        $entry->save();
        if(strtolower($result['Result']) == 'ok')
            return true;
        $e = new RebillError($this->rawResponse);
        $entry->errorDescription = "RebillError: " . $this->rawResponse;
        $entry->save();
        yii::app()->RSentryException->logException($e);
        return false;
    }

    public function refund()
    {
        $allParams = $this->formParams();
        $entry = PaymentLog::forMethod('refund');
        $entry->orderId = $allParams['OrderId'];

        $params = Array();
        $params['MerchantId'] = $this->credentials['id'];
        //! FIXME: can this amount change?
//        $params['Amount'] = sprintf("%.2f", $this->amount);//  $this->bill->amount);
        $params['TransactionId'] = $this->bill->transactionId;
        $params['SecurityKey'] = $this->getSignature($params);
        $entry->request = json_encode($params);
        $entry->save();
        $entry->startProfile();
        list($code,$result) = $this->callApi('transaction/void', $params);
        $entry->finishProfile();
        $entry->response = json_encode($result);
        $entry->save();
        if($result['Result'] == 'Ok')
            return true;
        $entry->errorDescription = "RefundError: " . $this->rawResponse;
        $entry->save();
        return false;
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
            $params[]=$key.'='.urlencode($value);
        }
        $url.= '/?';
        $url.= implode('&', $params);
        list($code, $data) =  Yii::app()->httpClient->get('https://secure.payonlinesystem.com/payment/' . $url);
//        Yii::trace('https://secure.payonlinesystem.com/payment/' . $url, "payments.channel.apicall");
//        Yii::trace($data, "payments.channel.apicall");

        //! FIXME SOMEHOW
        $this->rawResponse = $data;
        $result = array();
        if(strlen($data))
        {
            parse_str($data, $result);
        }
        return Array($code, $result);
    }


}
