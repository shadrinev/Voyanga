<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 19.08.12 18:37
 */
abstract class TripElementWorkflow extends CComponent implements ITripElementWorkflow
{
    public $finalStatus = 'notStarted';

    static protected $bookingContactInfo;

    protected $item;

    protected $workflow;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function setItem($val)
    {
        $this->item = $val;
    }

    public function getWorkflow()
    {
        return $this->workflow;
    }

    public function setWorkflow($val)
    {
        $this->workflow = $val;
    }

    public function bookItem()
    {
        $this->createBookingInfoForItem();
        $this->createWorkflowAndLinkItWithItem();
        $this->saveCredentialsForItem();
    }

    public function updateBookingId()
    {
        Yii::app()->shoppingCart->update($this->item, 1);
    }

    protected function createOrderBookingIfNotExist()
    {
        if (!self::$bookingContactInfo)
        {
            self::$bookingContactInfo = new OrderBooking();
            self::$bookingContactInfo->attributes = $this->getBookingContactFormData();
            if (!self::$bookingContactInfo->save())
            {
                $errMsg = 'Saving of order booking fails: '.CVarDumper::dumpAsString($this->bookingContactInfo->errors);
                $this->logAndThrowException($errMsg, 'OrderComponent.createOrderBookingIfNotExist');
            }
        }
        return self::$bookingContactInfo;
    }

    private function getBookingContactFormData()
    {
        $bookingForm = Yii::app()->user->getState('bookingForm');
        return array('email'=>$bookingForm->contactEmail, 'phone'=>$bookingForm->contactPhone);
    }
}
