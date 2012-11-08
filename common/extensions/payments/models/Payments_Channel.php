<?php

abstract class Payments_Channel {
    protected $bill;
    protected $name = 'foo';

    public function __construct($bill) {
        $this->bill = $bill;
        $this->credentials = Yii::app()->payments->getCredentials($this->name);
    }

    public function formParams($booker) {
        $credentials = $this->credentials;
        $params = Array();
        $params['MerchantId'] = $credentials['id'];
        //! FIXME: can this amount change?
        $params['Amount'] = sprintf("%.2f", 50);//  $this->bill->amount);
        $params['Currency'] = 'RUB';
        $params['OrderId'] = $this->bill->id;

        // FIXME
        $params['Email'] = 'zz@rialabs.org';
        $params['Phone'] = '79271317518';
        $params['Country'] = 'Russia';
        $params['City'] = 'Moscow';
        $params['Zip'] = '12354';

/*        if ($bill->channel == 'gds_sabre')
        {
            //! FIXME: implement commission split
            $params['Commission'] = sprintf("%.2f", $booker->flightVoyage->commission);
            $params['PNR'] = $booker->pnr;
            $params['query'] = $this->generateSabreQuery($booker);
            } */
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

    public function confirm($booker)
    {
        //! FIXME shuld we only accept bills in certain states ?
        $url = 'https://secure.payonlinesystem.com/payment/transaction/complete/';
        $context = array();
        $context['TransactionId'] = $$this->bill->transactionId;
        $context['ContentType'] = 'text';
        $context = $this->contributeToConfirm($context, $booker);

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
    protected function contributeToConfirm($context, $booker)
    {
        return $context;
    }
}