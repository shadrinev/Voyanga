<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 25.07.12
 * Time: 12:52
 */
class ConstructorBuilder
{
    public static function buildAndPutToCart(TourBuilderForm $form)
    {
        Yii::app()->shoppingCart->clear();
        //we are starting from our city
        $prev = $form->getStartCityId();
        $prevDate = $form->trips[0]->startDate;

        $ind = rand(10,200);
        $firstGroup = null;
        //building using scheme flight-hotel-flight
        /** @var $tripPlan TripForm */
        foreach ($form->trips as $tripPlan)
        {
            $next = $tripPlan->cityId;
            $flight = new FlightTripElement();
            $flight->departureCity = $prev;
            $flight->arrivalCity = $next;
            $flight->departureDate = $tripPlan->startDate;
            $flight->id = $ind++;
            $flight->adultCount = $form->adultCount;
            $flight->childCount = $form->childCount;
            $flight->infantCount = $form->infantCount;
            if($firstGroup)
            {
                $flight->groupId = substr(md5('group'. uniqid('',true)),0,10);
            }
            else
            {
                $flight->groupId = $firstGroup = substr(md5('group'. uniqid('',true)),0,10);
            }
            Yii::app()->shoppingCart->put($flight);

            $hotel = new HotelTripElement();
            $hotel->city = $next;
            $hotel->checkIn = $tripPlan->startDate;
            $hotel->checkOut = $tripPlan->endDate;
            $hotel->adultCount = $form->adultCount;
            $hotel->childCount = $form->childCount;
            $hotel->infantCount = $form->infantCount;
            $hotel->id = $ind++;
            Yii::app()->shoppingCart->put($hotel);

            $prev = $next;
            $prevDate = $hotel->checkOut;
        }

        //back to home now
        $next = $form->getStartCityId();
        $flight = new FlightTripElement();
        $flight->departureCity = $prev;
        $flight->arrivalCity = $next;
        $flight->departureDate = $prevDate;
        $flight->adultCount = $form->adultCount;
        $flight->childCount = $form->childCount;
        $flight->infantCount = $form->infantCount;
        $flight->id = $ind++;
        if(count($form->trips) == 1)
        {
            $flight->groupId = $firstGroup;
        }
        else
        {
            $flight->groupId = $firstGroup = substr(md5('group'. uniqid('',true)),0,10);
        }
        Yii::app()->shoppingCart->put($flight);
    }
}
