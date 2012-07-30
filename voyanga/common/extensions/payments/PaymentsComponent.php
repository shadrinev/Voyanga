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
     * Uniteller merchant Id
     * @var int
     */
    private $_merchantId;

    /**
     * Salt for request signing
     * @var string
     */
    private $_password;

    public function createBill ($data)
    {

    }
    public function getFormForBill()
    {

    }

    public function getStatus()
    {

    }

    public function setMerchantId($value)
    {
        $this->_merchantId = $value;
    }

    public function setPassword($value)
    {
        $this->_password = $value;
    }

    public function getMerchantId()
    {
        return $this->_merchantId;
    }

    public function getPassword()
    {
        return $this->_password;
    }
}