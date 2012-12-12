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
        $this->updateBookingInfoForItem();
        $this->createWorkflowAndLinkItWithItem();
        $this->saveCredentialsForItem();
    }

    public function updateBookingId()
    {
        Yii::app()->shoppingCart->update($this->item, 1);
    }

    protected function updateOrderBookingInfo()
    {
        if (!self::$bookingContactInfo)
        {
            $orderBookingId = Yii::app()->user->getState('orderBookingId');
            $orderBooking = OrderBooking::model()->findByPk($orderBookingId);
            if (!$orderBooking)
                throw new CHttpException(500, "Your order is gone away");
            self::$bookingContactInfo = $orderBooking;
            self::$bookingContactInfo->attributes = $this->getBookingContactFormData();
            $user = Yii::app()->user->getUserWithEmail(self::$bookingContactInfo->email);
            self::$bookingContactInfo->userId = $user->id;
            if (!self::$bookingContactInfo->save())
            {
                $errMsg = 'Saving of order booking fails: '.CVarDumper::dumpAsString($this->bookingContactInfo->errors);
                $this->logAndThrowException($errMsg, 'OrderComponent.updateOrderBookingInfo');
            }
            if (appParams('autoAssignCurrentOrders'))
            {
                $criteria = new CDbCriteria();
                $criteria->addColumnCondition(array('email'=>self::$bookingContactInfo->email));
                OrderBooking::model()->updateAll(array('userId'=>$user->id), $criteria);
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
