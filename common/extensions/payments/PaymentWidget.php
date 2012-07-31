<?php
/**
 * Widget, displays payment form w/hidden fields for given bill..
 *
 * @author Anatoly Kudinov <kudinov@voyanga.com>
 * @copyright Copyright (c) 2012, EasyTrip LLC
 * @package payments
 */
class PaymentWidget extends Widget
{
    private $_bill;

    public function setBill($value)
    {
        //FIXME check class
        $this->_bill = $value;
    }

    public function run()
    {
        $fields = Array();
        $fields['Shop_IDP']=Yii::app()->payments->merchantId;
        $fields['Order_IDP']='1';
        $fields['Subtotal_P']='10.00';
        $fields['URL_RETURN']='http://test.com/payments/result/';
        $fields['Signature']=$this->sign($fields);

        $this->render("form", array(
            'fields'=>$fields
        ));
    }

    /**
     * Helper function to sign form data
     *
     * @param array $params form data
     * @return string signature
     */
    private function sign($params)
    {
        $hash = array();
        $hash[] = $this->md5ForKey($params, 'Shop_IDP');
        $hash[] = $this->md5ForKey($params, 'Order_IDP');
        $hash[] = $this->md5ForKey($params, 'Subtotal_P');
        $hash[] = $this->md5ForKey($params, 'MeanType');
        $hash[] = $this->md5ForKey($params, 'EMoneyType');
        $hash[] = $this->md5ForKey($params, 'Lifetime');
        $hash[] = $this->md5ForKey($params, 'Customer_IDP');
        $hash[] = $this->md5ForKey($params, 'Card_IDP');
        $hash[] = $this->md5ForKey($params, 'IData');
        $hash[] = $this->md5ForKey($params, 'PT_Code');
        //! It should be in CONFEG ??!?!?!?
        $hash[] = md5(Yii::app()->payments->password);
        print_r(Yii::app()->payments->password);
        print_r($hash);
        $hash = implode('&', $hash);
        print_r($hash);
        $hash = md5(strtolower($hash));
        return strtoupper($hash);
    }

    /**
     * Helper function returns md5 hash of value if $key exists in $array
     * or empty string hash otherwise
     *
     * @param array $array Form parameters
     * @param string $key Key we want hash for
     * @return string md5 hash
     */
    private function md5ForKey($array, $key)
    {
        if(!isset($array[$key]))
            return 'd41d8cd98f00b204e9800998ecf8427e';
        return md5($array[$key]);
    }
}