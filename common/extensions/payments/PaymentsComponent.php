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
     * Uniteller shop id
     * @var int
     */
    private $_shopId;

    /**
     * Uniteller login. Required for some requests.
     * @var bool
     */
    private $_login;

    /**
     * Salt for request signing
     * @var string
     */
    private $_password;

    /**
     * Test mode flag, tells us which api endpoints to use.
     * @var bool
     */
    private $_testMode;

    public function createBill ($data)
    {

    }
    public function getFormForBill()
    {

    }

    public function getDataByOrderId($orderId)
    {
        //! FIXME switch to post here
        $data = Yii::app()->httpClient->get($this->statusUrl);
        var_dump($data);
        return Array();
    }

    public function setShopId($value)
    {
        $this->_shopId = $value;
    }

    public function getShopId()
    {
        return $this->_shopId;
    }

    public function setPassword($value)
    {
        $this->_password = $value;
    }

    /*    public function getPassword()
    {
        return $this->_password;
        }*/

    public function setTestMode($value)
    {
        $this->_testMode = $value;
    }

    public function setLogin($value)
    {
        $this->_login = $value;
    }

    public function getStatusUrl()
    {
        $result = $this->baseUrl . '/results/';
        $result.= '?Shop_ID='. $this->shopId;
        $result.= '&Login=' . $this->_login;
        $result.= '&Password=' . $this->_password;
        $result.= '&Format=1&Header=1';
        return $result;
    }

    public function getBaseUrl()
    {
        if($this->_testMode)
        {
            return 'https://test.wpay.uniteller.ru';
        }
        return 'https://wpay.uniteller.ru';
    }
}