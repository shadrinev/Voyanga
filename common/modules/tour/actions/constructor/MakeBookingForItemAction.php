<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:08
 */
class MakeBookingForItemAction extends CAction
{
    private $counters = array();
    private $passportForms = array();
    private $tripItems;
    private $valid = true;
    private $bookingForm;
    private $index;

    public function run()
    {
        $this->index = intval($_GET['index']);
        $this->passportForms = Yii::app()->user->getState('passportForms');
        $this->bookingForm = Yii::app()->user->getState('bookingForm');

        $dataProvider = new TripDataProvider();
        $this->tripItems = $dataProvider->getSortedCartItems();

        if ($this->areNotAllItemsLinked())
            throw new CHttpException(500, 'There are exists element inside trip that are not linked. You cannot continue booking');

        if ($this->weGotPassportsAndBooking())
        {
            $tripElementsWorkflow = Yii::app()->order->bookAndReturnTripElementWorkflowItem($this->index);
            $bookerId = $tripElementsWorkflow[0]->getBookerId();
            header("Content-type: application/json");
            echo '{"status":"success", "bookerId":"'.$bookerId.'"}';
            exit;
        }

        throw new CHttpException(500, 'Error while booking '.$this->index.'-th segment');
    }

    private function weGotPassportsAndBooking()
    {
        foreach ($this->passportForms as $i=>$pf)
        {
            if (!($pf instanceof FlightAdultPassportForm)
                and !($pf instanceof FlightChildPassportForm)
                and !($pf instanceof FlightInfantPassportForm)
               )
            return false;

        }
        return $this->bookingForm instanceof BookingForm;
    }

    private function areNotAllItemsLinked()
    {
        return !array_all($this->tripItems, function ($item)
        {
            return $item->isLinked();
        });
    }
}
