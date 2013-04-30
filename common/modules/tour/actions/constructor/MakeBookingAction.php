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
    private $orderBooking;

    public function run($secretKey)
    {
        if (!$this->orderBooking)
        {
            $this->orderBooking = OrderBooking::model()->findByAttributes(array('secretKey' => $secretKey));
            if ($this->orderBooking)
            {
                Yii::app()->user->setState('orderBookingId', $this->orderBooking->id);
            }
        }

        $this->controller->assignTitle('enterCredentials');
        $this->getController()->layout = 'enterCredentials';

        $dataProvider = new TripDataProvider();
        $dataProvider->restoreOrderBookingFromDb($this->orderBooking->id);
        $this->markPreviousCredentialsAsInactive();
        $this->tripItems = $dataProvider->getSortedCartItems();
        $haveHotels = false;
        $haveFlights = false;
        $flightParams = array();
        $valAirline = null;
        foreach ($this->tripItems as $tripItem)
        {
            if ($tripItem instanceof HotelTripElement)
            {
                $haveHotels = true;
            }
            elseif ($tripItem instanceof FlightTripElement)
            {
                $haveFlights = true;
                $flightParams['cityFrom'] = $tripItem->flightVoyage->getDepartureCity(0)->code;
                $flightParams['cityTo'] = $tripItem->flightVoyage->getArrivalCity(0)->code;
                $dateTime = new DateTime($tripItem->flightVoyage->getDepartureDate(0));
                if (!$valAirline)
                {
                    $valAirline = $tripItem->flightVoyage->valAirline;
                }
                $dateTime->setTime(0, 0, 0);
                $flightParams['checkIn'] = $dateTime->format('d.m.Y');
                $flightParams['duration'] = 7;
                $flightParams['rt'] = 0;
                if ($tripItem->flightVoyage->isRoundTrip())
                {
                    $flightParams['rt'] = 1;

                    $dateTimeBack = new DateTime($tripItem->flightVoyage->getDepartureDate(1));
                    $dateTimeBack->setTime(0, 0, 0);
                    $interval = $dateTime->diff($dateTimeBack);
                    $flightParams['duration'] = $interval->format('%d');
                }
                $dateTime->modify("+{$flightParams['duration']} day");
                $flightParams['checkOut'] = $dateTime->format('d.m.Y');
            }
        }

        if ($this->areNotAllItemsLinked())
            throw new CHttpException(500, 'There are exists element inside trip that are not linked. You cannot continue booking');

        $passportManager = new PassportManager();
        $passportManager->tripItems = $this->tripItems;
        $orderBookingId = $this->getOrderBooking($secretKey);
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
                // FIXME return status here
                header("Content-type: application/json");
                echo '{"status":"success"}';
                Yii::app()->end();
            }
            else
            {
                header("Content-type: application/json");
                echo json_encode(array(
                                      'status' => 'error',
                                      'message' => $this->validationErrors
                                 ));
                Yii::app()->end();
            }
        }
        $this->bookingForm = new BookingForm();
        $this->bookingForm->tryToPrefetch();
        $tripStorage = new TripDataProvider();
        $trip = $tripStorage->getSortedCartItemsOnePerGroupAsJson();
        list ($icon, $header) = $tripStorage->getIconAndTextForPassports();
        $alliance = null;
        $allianceAirlines = array();
        if ($valAirline && $valAirline->allianceId)
        {
            $airlines = Airline::model()->findAllByAttributes(array('allianceId' => $valAirline->allianceId));
            foreach ($airlines as $airline)
            {
                $allianceAirlines[$airline->code] = $airline->localRu;
            }
            $alliance = Alliances::model()->findByPk($valAirline->allianceId);
        }
        elseif ($valAirline)
        {
            $allianceAirlines[$valAirline->code] = $valAirline->localRu;
        }
        $viewData = array(
            'passportForms' => $this->passportForms,
            'ambigousPassports' => $ambigousPassports,
            'bookingForm' => $this->bookingForm,
            'trip' => $trip,
            'orderId' => $orderBookingId,
            'textForOrder' => $tripStorage->getTextForOrder(),
            'icon' => $icon,
            'header' => $header,
            'flightCross' => $haveFlights && !$haveHotels,
            'flightParams' => $flightParams,
            'headersForAmbigous' => $tripStorage->getHeadersForPassportDataPage(),
            'roomCounters' => (sizeof($passportManager->roomCounters) > 0) ? $passportManager->roomCounters : false,
            'secretKey' => $secretKey,
            'valAirline' => $valAirline,
            'alliance' => $alliance,
            'allianceAirlines' => $allianceAirlines
        );
        $this->controller->render('makeBooking', $viewData);
    }

    private function markPreviousCredentialsAsInactive()
    {
        foreach ($this->orderBooking->flightBookers as $flightBooker)
        {
            $dbCriteria = new CDbCriteria();
            $dbCriteria->addColumnCondition(array(
                                                 'is_actual' => 1,
                                                 'flightBookingId' => $flightBooker->id
                                            ));
            FlightBookingPassport::model()->updateAll(array('is_actual' => 0), $dbCriteria);
        }

        foreach ($this->orderBooking->hotelBookers as $hotelBooker)
        {
            $dbCriteria = new CDbCriteria();
            $dbCriteria->addColumnCondition(array(
                                                 'is_actual' => 1,
                                                 'hotelBookingId' => $hotelBooker->id
                                            ));
            HotelBookingPassport::model()->updateAll(array('is_actual' => 0), $dbCriteria);
        }
    }

    private function weGotPassportsAndBooking()
    {
        foreach ($this->passportForms as $i => $pf)
        {
            if (($pf instanceof FlightAdultPassportForm) and (!isset($_POST['FlightAdultPassportForm'])))
                return false;
            if (($pf instanceof FlightChildPassportForm) and (!isset($_POST['FlightChildPassportForm'])))
                return false;
            if (($pf instanceof FlightInfantPassportForm) and (!isset($_POST['FlightInfantPassportForm'])))
                return false;
            if (($pf instanceof HotelAdultPassportForm) and (!isset($_POST['FlightAdultPassportForm'])))
                return false;
            if (($pf instanceof HotelChildPassportForm) and (!isset($_POST['FlightChildPassportForm'])))
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

    public static function formCompare($a, $b)
    {
        if (isset($a['formData']['birthdayYear'], $a['formData']['birthdayMonth'], $a['formData']['birthdayDay']) && ($a['formData']['birthdayYear'] && $a['formData']['birthdayMonth'] && $a['formData']['birthdayDay'])
        )
        {
            $abd = $a['formData']['birthdayYear'] . '-' . $a['formData']['birthdayMonth'] . '-' . $a['formData']['birthdayDay'];
        }
        else
        {
            $abd = '1900-01-01';
        }
        $at = DateTime::createFromFormat('Y-m-d', $abd);

        if (isset($b['formData']['birthdayYear'], $b['formData']['birthdayMonth'], $b['formData']['birthdayDay']) && ($b['formData']['birthdayYear'] && $b['formData']['birthdayMonth'] && $b['formData']['birthdayDay'])
        )
        {
            $bbd = $b['formData']['birthdayYear'] . '-' . $b['formData']['birthdayMonth'] . '-' . $b['formData']['birthdayDay'];
        }
        else
        {
            $bbd = '1900-01-01';
        }
        $bt = DateTime::createFromFormat('Y-m-d', $bbd);
        $diff = $bt->diff($at);
        $ret = (($diff->days * ($diff->invert ? -1 : 1)) > 0) ? 1 : ((($diff->days * ($diff->invert ? -1 : 1)) < 0) ? -1 : 0);
        return $ret;
    }

    private function fillOutPassports($ambigous)
    {
        $errorCounter = 0;
        if (!$ambigous)
        {
            $adultsPassports = array();
            $childrenPassports = array();
            $infantsPassports = array();
            $adcnt = isset($_POST['FlightAdultPassportForm']) ? count($_POST['FlightAdultPassportForm']) : 0;
            $chcnt = isset($_POST['FlightChildPassportForm']) ? count($_POST['FlightChildPassportForm']) : 0;
            $incnt = isset($_POST['FlightInfantPassportForm']) ? count($_POST['FlightAdultPassportForm']) : 0;
            $formsData = array();
            $i = 0;
            if (isset($_POST['FlightAdultPassportForm']))
            {
                foreach ($_POST['FlightAdultPassportForm'] as $formData)
                {
                    $formsData[$i] = array(
                        'i' => $i,
                        'formData' => $formData
                    );
                    $i++;
                }
            }
            if (isset($_POST['FlightChildPassportForm']))
            {
                foreach ($_POST['FlightChildPassportForm'] as $formData)
                {
                    $formsData[$i] = array(
                        'i' => $i,
                        'formData' => $formData
                    );
                    $i++;
                }
            }
            if (isset($_POST['FlightInfantPassportForm']))
            {
                foreach ($_POST['FlightInfantPassportForm'] as $formData)
                {
                    $formsData[$i] = array(
                        'i' => $i,
                        'formData' => $formData
                    );
                    $i++;
                }
            }

            if (count($formsData) > 1)
            {
                usort($formsData, 'MakeBookingAction::formCompare');
            }
            foreach ($formsData as $formInfo)
            {
                if ($adcnt > 0)
                {
                    $adcnt--;
                    $adultPassport = new FlightAdultPassportForm();
                    $adultPassport->attributes = $formInfo['formData'];
                    $adultPassport->sequence = $formInfo['i'];
                    $adultPassport->handleFields();
                    $adultsPassports[] = $adultPassport;
                    $p = $adultsPassports[(count($adultsPassports) - 1)];

                }
                elseif ($chcnt > 0)
                {
                    $chcnt--;
                    $childrenPassport = new FlightChildPassportForm();
                    $childrenPassport->attributes = $formInfo['formData'];
                    $childrenPassport->sequence = $formInfo['i'];
                    $childrenPassport->handleFields();
                    $childrenPassports[] = $childrenPassport;
                    $p = $childrenPassports[(count($childrenPassports) - 1)];

                }
                elseif ($incnt > 0)
                {
                    $incnt--;
                    $infantsPassport = new FlightInfantPassportForm();
                    $infantsPassport->attributes = $formInfo['formData'];
                    $infantsPassport->sequence = $formInfo['i'];
                    $infantsPassport->handleFields();
                    $infantsPassports[] = $infantsPassport;
                    $p = $infantsPassports[(count($infantsPassports) - 1)];

                }
                if (!$p->validate())
                {
                    $this->validationErrors['passports'][$formInfo['i']] = $p->errors;
                    $errorCounter++;
                }
            }

            if ($errorCounter > 0)
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
                    foreach ($item->rooms as $i => $room)
                    {
                        $roomPassport = ($i == 0) ? array(
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
        else
            return $this->fillOutAmbigousPassports();
    }

    private function fillOutAmbigousPassports()
    {
        $formsData = array();
        $errorCounter = 0;
        if (isset($_POST['FlightAdultPassportForm']))
        {
            foreach ($_POST['FlightAdultPassportForm'] as $j => $formData)
            {
                foreach ($formData as $i => $data)
                {
                    $adultPassport = new FlightAdultPassportForm();
                    $adultPassport->attributes = $data;
                    $adultPassport->handleFields();
                    $formsData[$j][$i] = $adultPassport;
                }
            }
        }
        if (isset($_POST['FlightChildPassportForm']))
        {
            foreach ($_POST['FlightChildPassportForm'] as $j => $formData)
            {
                foreach ($formData as $i => $data)
                {
                    $childPassport = new FlightChildPassportForm();
                    $childPassport->attributes = $data;
                    $childPassport->handleFields();
                    $formsData[$j][$i] = $childPassport;
                }
            }
        }
        if (isset($_POST['FlightInfantPassportForm']))
        {
            foreach ($_POST['FlightInfantPassportForm'] as $formData)
            {
                foreach ($formData as $i => $data)
                {
                    $infantPassport = new FlightInfantPassportForm();
                    $infantPassport->attributes = $data;
                    $infantPassport->handleFields();
                    $formsData[$j][$i] = $infantPassport;
                }
            }
        }

        foreach ($formsData as $i => $datas)
        {
            foreach ($datas as $j => $data)
            {
                if (!$data->validate())
                {
                    $this->validationErrors['passports'][$i][$j] = $data->errors;
                    $errorCounter++;
                }
            }
        }

        if ($errorCounter > 0)
            return false;

        $i = 0;
        foreach ($this->tripItems as $item)
        {
            if ($item instanceof FlightTripElement)
            {
                $adultsPassports = $this->getPassportsByType('FlightAdultPassportForm', $formsData[$i]);
                $childrenPassports = $this->getPassportsByType('FlightChildPassportForm', $formsData[$i]);
                $infantsPassports = $this->getPassportsByType('FlightInfantPassportForm', $formsData[$i]);
                $item->setPassports($adultsPassports, $childrenPassports, $infantsPassports);
                Yii::app()->shoppingCart->update($item, 1);
            }
            elseif ($item instanceof HotelTripElement)
            {
                $adultsPassports = $this->getPassportsByType('FlightAdultPassportForm', $formsData[$i]);
                $childrenPassports = $this->getPassportsByType('FlightChildPassportForm', $formsData[$i]);

                foreach ($item->rooms as $i => $room)
                {
                    $roomPassport = ($i == 0) ? array(
                        'adults' => $adultsPassports,
                        'children' => $childrenPassports
                    ) : array();
                    $roomPassports[] = $roomPassport;
                }
                $item->setPassports($this->bookingForm, $roomPassports);
                Yii::app()->shoppingCart->update($item, 1);
            }
            $i++;
        }
        return true;
    }

    private function getPassportsByType($className, $elements)
    {
        $result = array();
        foreach ($elements as $element)
            if (get_class($element) == $className)
                $result[] = $element;
        return $result;
    }

    private function areNotAllItemsLinked()
    {
        return !array_all($this->tripItems, function ($item)
        {
            return $item->isLinked();
        });
    }

    private function getOrderBooking($secretKey)
    {
        if (!$this->orderBooking)
            throw new CHttpException(404, 'Page not found');
        return $this->orderBooking->readableId;
    }
}
