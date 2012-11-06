<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 19.08.12 18:37
 */
class FlightTripElementWorkflow extends TripElementWorkflow
{
    public function createWorkflowAndLinkItWithItem()
    {
        $this->workflow = $this->createFlightBookerWorkflow();
        $this->item->flightBookerId = $this->workflow->getFlightBookerId();
    }

    private function createFlightBookerWorkflow()
    {
        Yii::trace("Create FlightBooker", "FlightTripElementWorkflow.createFlightBookerWorkflow");
        $flightBookerComponent = new FlightBookerComponent();
        $flightBookerComponent->setFlightBookerFromFlightVoyage($this->item->flightVoyage);
        $currentFlightBookerComponent = $flightBookerComponent->getCurrent();
        $currentFlightBookerComponent->orderBookingId = $this->bookingContactInfo->id;
        if (!$currentFlightBookerComponent->save())
        {
            $errMsg = "Could not save FlightBooker" . PHP_EOL . CVarDumper::dumpAsString($currentFlightBookerComponent->getErrors());
            $this->logAndThrowException($errMsg, 'OrderComponent.saveCredentialsForFlight');
        }
        Yii::trace("FlightBooker saved. FlighBooker id is " . $currentFlightBookerComponent->getPrimaryKey(), "OrderComponent.saveCredentialsForFlight");
        return $flightBookerComponent;
    }

    public function runWorkflowAndSetFinalStatus()
    {
        $this->workflow->status('booking');
        $this->finalStatus = $this->workflow->getCurrent()->swGetStatus()->toString();
    }

    public function saveCredentialsForItem()
    {
        $this->savePassports();
    }

    public function createBookingInfoForItem()
    {
        $this->createOrderBookingIfNotExist();
    }



    private function savePassports()
    {
        $item = $this->item;
        $passports = $item->getPassports();
        $flightBookerId = $item->flightBookerId;
        $this->saveAdultFlightPassports($passports, $flightBookerId);
        $this->saveChildFlightPassports($passports, $flightBookerId);
        $this->saveInfantFlightPassports($passports, $flightBookerId);
    }

    private function saveInfantFlightPassports($passports, $flightBookerId)
    {
        foreach ($passports->infantPassports as $infantInfo)
        {
            $flightPassport = new FlightBookingPassport();
            $flightPassport->populate($infantInfo, $flightBookerId);
            $flightPassport->save();
        }
    }

    private function saveChildFlightPassports($passports, $flightBookerId)
    {
        foreach ($passports->childrenPassports as $childInfo)
        {
            $flightPassport = new FlightBookingPassport();
            $flightPassport->populate($childInfo, $flightBookerId);
            $flightPassport->save();
        }
    }

    private function saveAdultFlightPassports($passports, $flightBookerId)
    {
        foreach ($passports->adultsPassports as $adultInfo)
        {
            $flightPassport = new FlightBookingPassport();
            $flightPassport->populate($adultInfo, $flightBookerId);
            $flightPassport->save();
        }
    }
}
