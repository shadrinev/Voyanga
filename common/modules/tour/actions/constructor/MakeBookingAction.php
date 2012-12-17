<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:08
 */
class MakeBookingAction extends CAction
{
    private $passportForms = array();
    private $tripItems;
    private $valid = true;
    private $bookingForm;

    public function run()
    {
        $this->getController()->layout = 'static';

        $dataProvider = new TripDataProvider();
        $this->tripItems = $dataProvider->getSortedCartItems();

        if (sizeof($this->tripItems)==0)
            Yii::app()->request->redirect('/');

        if ($this->areNotAllItemsLinked())
            throw new CHttpException(500, 'There are exists element inside trip that are not linked. You cannot continue booking');

        $passportManager = new PassportManager();
        $passportManager->tripItems = $this->tripItems;
        $orderBookingId = $this->createNewOrderBooking();
        $ambigousPassports = $passportManager->generatePassportForms();
        if ($this->weGotPassportsAndBooking())
        {
            $this->fillOutBookingForm();
            if ($this->fillOutPassports($ambigousPassports))
            {
                Yii::app()->user->setState('passportForms', $this->passportForms);
                Yii::app()->user->setState('bookingForm', $this->bookingForm);
                //$tripElementsWorkflow = Yii::app()->order->bookAndReturnTripElementWorkflowItems();
                // FIXME return status here
                header("Content-type: application/json");
                echo '{"status":"success"}';
                exit;
            }
        }
        $this->bookingForm = new BookingForm();
        $tripStorage = new TripDataProvider();
        $trip = $tripStorage->getSortedCartItemsOnePerGroupAsJson();
        list ($icon, $header) = $tripStorage->getIconAndTextForPassports();
        $viewData = array(
            'passportForms' => $this->passportForms,
            'ambigousPassports' => $ambigousPassports,
            'bookingForm' => $this->bookingForm,
            'trip' => $trip,
            'orderId' => $orderBookingId,
            'icon' => $icon,
            'header' => $header,
            'headersForAmbigous' => $tripStorage->getHeadersForPassportDataPage(),
            'roomCounters' => (sizeof($passportManager->roomCounters) > 0) ? $passportManager->roomCounters : false
        );
        $this->controller->render('makeBooking', $viewData);
    }

    private function weGotPassportsAndBooking()
    {
        foreach ($this->passportForms as $i=>$pf)
        {
            if (($pf instanceof FlightAdultPassportForm) and (!isset($_POST['FlightAdultPassportForm'][$i])))
                return false;

        }
        return isset($_POST['BookingForm']);
    }

    private function fillOutBookingForm()
    {
        $this->bookingForm = new BookingForm();
        $this->bookingForm->attributes = $_POST['BookingForm'];
    }

    private function fillOutPassports($ambigous)
    {
        if (!$ambigous)
        {
            $valid = $this->bookingForm->validate();
            $adultsPassports = array();
            $childrenPassports = array();
            $infantsPassports = array();
            if (isset($_POST['FlightAdultPassportForm']))
            {
                foreach ($_POST['FlightAdultPassportForm'] as $i=>$formData)
                {
                    $adultPassport = new FlightAdultPassportForm();
                    $adultPassport->attributes = $_POST['FlightAdultPassportForm'][$i];
                    $adultsPassports[] = $adultPassport;
                }
                foreach ($adultsPassports as $p)
                    $valid = $valid && $p->validate();
            }
            if (isset($_POST['FlightChildPassportForm']))
            {
                foreach ($_POST['FlightChildPassportForm'] as $i=>$formData)
                {
                    $childrenPassport = new FlightChildPassportForm();
                    $childrenPassport->attributes = $_POST['FlightChildPassportForm'][$i];
                    $childrenPassports[] = $childrenPassport;
                }
                foreach ($childrenPassports as $p)
                    $valid = $valid && $p->validate();
            }
            if (isset($_POST['FlightInfantPassportForm']))
            {
                foreach ($_POST['FlightInfantPassportForm'] as $i=>$formData)
                {
                    $infantsPassport = new FlightInfantPassportForm();
                    $infantsPassport->attributes = $_POST['FlightInfantPassportForm'][$i];
                    $infantsPassports[] = $infantsPassport;
                }
                foreach ($infantsPassports as $p)
                    $valid = $valid && $p->validate();
            }
            if (!$valid)
                return false;

            foreach ($this->tripItems as $item)
            {
                if ($item instanceof FlightTripElement)
                {
                    $item->setPassports($adultsPassports, $childrenPassports, $infantsPassports);
                    Yii::app()->shoppingCart->update($item, 1);
                }
                if ($item instanceof HotelTripElement)
                {
                    //todo: discuss with Oleg
                    foreach ($item->rooms as $i=>$room)
                    {
                        $roomPassport = ($i==0) ? array(
                            'adults' => $adultsPassports,
                            'children' => $childrenPassports
                        ) : array();
                        $roomPassports[] = $roomPassport;
                    }
                    $item->setPassports($this->bookingForm, $roomPassports);
                    Yii::app()->shoppingCart->update($item, 1);
                }
            }
            return true;
        }
    }

    private function areNotAllItemsLinked()
    {
        return !array_all($this->tripItems, function ($item)
        {
            return $item->isLinked();
        });
    }

    private function createNewOrderBooking()
    {
        if (is_numeric(Yii::app()->user->getState('todayOrderId')))
            return Yii::app()->user->getState('todayOrderId');
        $orderBooking = new OrderBooking();
        $orderBooking->secretKey = md5(microtime().time().appParams('salt'));
        $orderBooking->save();
        $todayOrderId = OrderBooking::model()->count(array('condition'=>"DATE(`timestamp`) = CURDATE()"));
        $readableNumber = OrderBooking::buildReadableNumber($todayOrderId);
        $orderBooking->saveAttributes(array('readableId'=>$readableNumber));
        Yii::app()->user->setState('orderBookingId', $orderBooking->id);
        Yii::app()->user->setState('todayOrderId', $readableNumber);
        Yii::app()->user->setState('secretKey', $orderBooking->secretKey);
        return $readableNumber;
    }
}
