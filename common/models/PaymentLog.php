<?php
class PaymentLog extends EMongoDocument
{
    public $orderId;
    public $methodName;
    public $request;
    public $response;
    public $timestamp;
    public $executionTime;
    public $transactionId;
    public $errorDescription;
    private $startTime;

    public function getCollectionName()
    {
        return 'PaymentLog';
    }

    // We can define rules for fields, just like in normal CModel/CActiveRecord classes
    public function rules()
    {
        return array(
            array('orderId, methodName, timestamp, request', 'required'),
            array('transactionId, response, errorDescription', 'safe'),
        );
    }


    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function forMethod($methodName)
    {
        $entry = new self();
        $entry->timestamp = time();
        $entry->methodName = $methodName;
        $entry->executionTime = 0.0;
        return $entry;
    }

    public function startProfile()
    {
        $this->startTime = microtime(true);
    }

    public function finishProfile()
    {
        $endTime = microtime(true);
        $this->executionTime = ($endTime - $this->startTime);
    }
}