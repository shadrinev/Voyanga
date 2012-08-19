<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 19.08.12 18:37
 */
class TripElementWorkflow extends CComponent implements ITripElementWorkflow
{
    private $bookingContactInfo;

    private $item;

    private $workflow;

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
        $this->createWorkflowAndLinkItWithItem();
        $this->saveCredentialsForItem();
        $this->createBookingInfoForItem();
        $this->markItemGroupAsBooked();
        $this->saveWorkflowState();
    }

    public function createWorkflowAndLinkItWithItem()
    {
        throw Exception('You should implement createWorkflowAndLinkItWithItem in derived class');
    }

    public function saveCredentialsForItem()
    {
        throw Exception('You should implement saveCredentialsForItem in derived class');
    }

    public function createBookingInfoForItem()
    {
        throw Exception('You should implement createBookingInfoForItem in derived class');
    }

    public function switchToSecondWorkflowStage()
    {
        throw Exception('You should implement switchToSecondWorkflowStage in derived class');
    }

    private function createOrderBookingIfNotExist($orderBookingId)
    {
        if (!$this->bookingContactInfo)
        {
            $this->bookingContactInfo = OrderBooking::model()->findByPk($orderBookingId);
            if (!$this->bookingContactInfo)
            {
                $this->bookingContactInfo = new OrderBooking();
                $this->bookingContactInfo->attributes = $this->getBookingContactFormData();
                if (!$this->bookingContactInfo->save())
                {
                    $errMsg = 'Saving of order booking fails: '.CVarDumper::dumpAsString($this->bookingContactInfo->errors);
                    $this->logAndThrowException($errMsg, 'OrderComponent.createOrderBookingIfNotExist');
                }
            }
        }
        return $this->bookingContactInfo;
    }

    private function getBookingContactFormData()
    {
        //todo: implement returning booking form here
        return $this->getTestBookingContactFormData();
    }

    private function getTestBookingContactFormData()
    {
        return array('email' => 'test@test.ru', 'phone' => '9213546576');
    }
}
