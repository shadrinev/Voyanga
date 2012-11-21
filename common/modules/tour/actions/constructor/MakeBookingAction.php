<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:08
 */
class MakeBookingAction extends CAction
{
    private $counters = array();
    private $passportForms = array();
    private $tripItems;
    private $valid = true;
    private $bookingForm;

    public function run()
    {
        $this->getController()->layout = 'static';

        $dataProvider = new TripDataProvider();
        $this->tripItems = $dataProvider->getSortedCartItems();

        if ($this->areNotAllItemsLinked())
            throw new CHttpException(500, 'There are exists element inside trip that are not linked. You cannot continue booking');

        $ambigousPassports = $this->generatePassportForms();
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
        $viewData = array(
            'passportForms' => $this->passportForms,
            'ambigousPassports' => $ambigousPassports,
            'bookingForm' => $this->bookingForm,
            'trip' => $trip
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

    private function generatePassportForms()
    {
        $this->initCounters();
        $ambigous = $this->checkIfAmbigous();
        if ($ambigous)
        {
            $this->generatePassportFormsForEachTripElement();
        }
        else
        {
            $this->generatePassportFormsForAllTrip();
        }
        return $ambigous;
    }

    public function generatePassportFormsForAllTrip()
    {
        for ($i = 0; $i < $this->counters['adultCount']; $i++)
        {
            $this->passportForms[] = new FlightAdultPassportForm();
        }
        for ($i = 0; $i < $this->counters['childCount']; $i++)
        {
            $this->passportForms[] = new FlightChildPassportForm();
        }
        for ($i = 0; $i < $this->counters['infantCount']; $i++)
        {
            $this->passportForms[] = new FlightInfantPassportForm();
        }
    }

    public function generatePassportFormsForEachTripElement()
    {
        foreach ($this->tripItems as $item)
        {
            if ($item instanceof FlightTripElement)
            {
                for ($i = 0; $i < $item->adultCount; $i++)
                {
                    $this->passportForms[] = new FlightAdultPassportForm();
                }
                for ($i = 0; $i < $item->childCount; $i++)
                {
                    $this->passportForms[] = new FlightChildPassportForm();
                }
                for ($i = 0; $i < $item->infantCount; $i++)
                {
                    $this->passportForms[] = new FlightInfantPassportForm();
                }
            }
            elseif ($item instanceof HotelTripElement)
            {
                foreach ($item->rooms as $room)
                {
                    for ($i = 0; $i < $room->adultCount; $i++)
                    {
                        $this->passportForms[] = new HotelAdultPassportForm();
                    }
                    for ($i = 0; $i < $room->childCount; $i++)
                    {
                        $this->passportForms[] = new HotelChildPassportForm();
                    }
                    for ($i = 0; $i < $room->infantCount; $i++)
                    {
                        $this->passportForms[] = new HotelInfantPassportForm();
                    }
                }
            }
        }
        return $item;
    }

    private function initCounters()
    {
        $this->counters = array(
            'adultCount' => 0,
            'childCount' => 0,
            'infantCount' => 0
        );
        foreach ($this->tripItems as $item)
        {
            if ($item instanceof FlightTripElement)
            {
                $this->counters = array(
                    'adultCount' => $item->adultCount,
                    'childCount' => $item->childCount,
                    'infantCount' => $item->infantCount
                );
                break;
            }
            elseif ($item instanceof HotelTripElement)
            {
                foreach ($item->rooms as $room)
                {
                    $this->counters['adultCount'] += $room['adt'];
                    $this->counters['childCount'] += $room['chd'];
                    $this->counters['infantCount'] += ($room['cots'] > 0) ? 1 : 0;
                }
                break;
            }
        }
    }

    public function checkIfAmbigous()
    {
        foreach ($this->tripItems as $item)
        {
            if ($item instanceof FlightTripElement)
            {
                if (
                    $this->counters['adultCount'] != $item->adultCount
                    || $this->counters['childCount'] != $item->childCount
                    || $this->counters['infantCount'] != $item->infantCount
                )
                    return true;
            }
            if ($item instanceof HotelTripElement)
            {
                $counters = array(
                    'adultCount' => 0,
                    'childCount' => 0,
                    'infantCount' => 0
                );
                foreach ($item->rooms as $room)
                {
                    $counters['adultCount'] += $room['adt'];
                    $counters['childCount'] += $room['chd'];
                    $counters['infantCount'] += ($room['cots'] > 0) ? 1 : 0;
                }
                if (
                    $this->counters['adultCount'] != $counters['adultCount']
                    || $this->counters['childCount'] != $counters['childCount']
                    || $this->counters['infantCount'] != $counters['infantCount']
                )
                    return true;
            }
        }
        return false;
    }

    private function areNotAllItemsLinked()
    {
        return !array_all($this->tripItems, function ($item)
        {
            return $item->isLinked();
        });
    }
}
