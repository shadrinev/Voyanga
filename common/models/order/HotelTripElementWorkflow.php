<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 19.08.12 18:37
 */
class HotelTripElementWorkflow extends TripElementWorkflow
{
    public function createHotelWorkflowAndLinkItWithItem($position)
    {
        $hotelBookerComponent = $this->createHotelBookerComponent();
        $this->saveCredentialsForHotel($position, $hotelBookerComponent);
    }

    public function switchToSecondWorkflowStage()
    {
        $this->workflow->status('analyzing');
    }

    private function createHotelBookerComponent()
    {
        $hotelBookerComponent = new HotelBookerComponent();
        $hotelBookerComponent->setHotelBookerFromHotel($this->item->hotel);
        $currentHotelBooker = $hotelBookerComponent->getCurrent();
        $currentHotelBooker->orderBookingId = $this->bookingContactInfo->id;
        $currentHotelBooker->status = 'enterCredentials';
        if (!$currentHotelBooker->save())
        {
            $errMsg = 'Couldn\'t save hotel booker instanse'.PHP_EOL.CVarDumper::dumpAsString($currentHotelBooker->getErrors());
            throw CException($errMsg);
        }
        else
        {
            Yii::trace("HotelBooker successfully saved. It's id:" . $hotelBookerComponent->getCurrent()->id, 'HotelTripElementWorkflow.createWorkflowAndLinkItWithItem');
        }
        $this->item->hotelBookerId = $currentHotelBooker->getPrimaryKey();
    }

    public function saveCredentialsForItem()
    {
        $passports = $this->item->getPassports();
        $hotelBookerId = $this->item->hotelBookerId;
        foreach ($passports->roomsPassports as $i => $roomPassport)
        {
            $this->saveAdultsPassports($i, $roomPassport, $hotelBookerId);
            $this->saveChildrenPassports($i, $roomPassport, $hotelBookerId);
        }
    }

    public function createBookingInfoForItem()
    {
        $hotelBooker = $this->getWorkflow()->hotelBooker;
        $this->createOrderBookingIfNotExist($hotelBooker->orderBookingId);
    }

    private function saveAdultsPassports($i, $roomPassport, $hotelBookerId)
    {
        foreach ($roomPassport->adultsPassports as $adultInfo)
        {
            $hotelPassport = new HotelBookingPassport();
            $hotelPassport->scenario = 'adult';
            $hotelPassport->attributes = $adultInfo->attributes;
            $hotelPassport->hotelBookingId = $hotelBookerId;
            $hotelPassport->roomKey = $i;
            if (!$hotelPassport->save())
            {
                $errMsg = "Incorrect adult passport parameters" . PHP_EOL . CVarDumper::dumpAsString($hotelPassport->errors);
                Yii::trace($errMsg, 'HotelBooker.EnterCredentials.adultPassport');
                throw new CException($errMsg);
            }
        }
        return $hotelPassport;
    }

    private function saveChildrenPassports($i, $roomPassport, $hotelBookerId)
    {
        foreach ($roomPassport->childrenPassports as $childInfo)
        {
            $hotelPassport = new HotelBookingPassport();
            $hotelPassport->scenario = 'child';
            $hotelPassport->attributes = $childInfo->attributes;
            $hotelPassport->hotelBookingId = $hotelBookerId;
            $hotelPassport->roomKey = $i;
            if (!$hotelPassport->save())
            {
                $errMsg = 'Incorrect child passport data.' . PHP_EOL . CVarDumper::dumpAsString($hotelPassport->errors);
                Yii::trace($errMsg, 'HotelBooker.EnterCredentials.childPassport');
                throw new CException($errMsg);
            }
        }
    }
}
