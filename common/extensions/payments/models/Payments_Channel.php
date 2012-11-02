<?php

class Payments_Channel {
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
        $params['Amount'] = sprintf("%.2f", $this->bill->amount);
        $params['Currency'] = 'RUB';
        $params['OrderId'] = 'adev-' . $this->bill->id;

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

    private function getSignature($params)
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

    public function confirmParams()
    {

    }
}