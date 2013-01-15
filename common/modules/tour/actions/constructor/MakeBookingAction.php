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
    private $validationErrors = array();

    public function run()
    {
        $this->getController()->layout = 'static';

        $dataProvider = new TripDataProvider();
        $this->tripItems = $dataProvider->getSortedCartItems();

        if ($this->areNotAllItemsLinked())
            throw new CHttpException(500, 'There are exists element inside trip that are not linked. You cannot continue booking');

        $passportManager = new PassportManager();
        $passportManager->tripItems = $this->tripItems;
        $orderBookingId = $this->createNewOrderBooking();
        $ambigousPassports = $passportManager->generatePassportForms();
        $this->passportForms = $passportManager->passportForms;
        if ($this->weGotPassportsAndBooking())
        {
            $flag1 = $this->fillOutBookingForm();
            $flag2 = $this->fillOutPassports($ambigousPassports);
            if ($flag1 && $flag2)
            {
                Yii::app()->user->setState('passportForms', $this->passportForms);
                Yii::app()->user->setState('bookingForm', $this->bookingForm);
                //$tripElementsWorkflow = Yii::app()->order->bookAndReturnTripElementWorkflowItems();
                // FIXME return status here
                header("Content-type: application/json");
                echo '{"status":"success"}';
                Yii::app()->end();
            }
            else
            {
                header("Content-type: application/json");
                echo json_encode(array('status'=>'error', 'message' => $this->validationErrors));
                Yii::app()->end();
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
        $this->bookingForm->validate();
        $this->validationErrors['booking'] = $this->bookingForm->errors;
        return !$this->bookingForm->hasErrors(); //just to get passport data ability to check too
    }

    private function fillOutPassports($ambigous)
    {
        if (!$ambigous)
        {
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
                {
                    if (!$p->validate())
                        $this->validationErrors['passports'][] = $p->errors;
                }
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
                {
                    if (!$p->validate())
                        $this->validationErrors['passports'][] = $p->errors;
                }
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
                {
                    if ($p->validate())
                        $this->validationErrors['passports'][] = $p->errors;
                }
            }
            if (isset($this->validationErrors['passports'][0]))
                return false;

            foreach ($this->tripItems as $item)
            {
                if ($item instanceof FlightTripElement)
                {
                    $item->setPassports($adultsPassports, $childrenPassports, $infantsPassports);
                    Yii::app()->shoppingCart->update($item, 1);
                }
                elseif ($item instanceof HotelTripElement)
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
