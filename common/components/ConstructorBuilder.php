<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 25.07.12
 * Time: 12:52
 */
class ConstructorBuilder
{
    public static function build(TourBuilderForm $form)
    {
        //we are starting from our city
        $prev = $form->getStartCityId();
        $prevDate = $form->trips[0]->startDate;

        //building using scheme home - [flight-hotel-flight]... - home
        /** @var $tripPlan TripForm */
        foreach ($form->trips as $tripPlan)
        {
            $next = $tripPlan->cityId;
            $flight = new FlightTripElement();
            $flight->departureCity = $prev;
            $flight->arrivalCity = $next;
            $flight->departureDate = $tripPlan->startDate;
            Yii::app()->shoppingCart->put($flight);

            $hotel = new HotelTripElement();
            $hotel->city = $next;
            $hotel->checkIn = $tripPlan->startDate;
            $hotel->checkOut = $tripPlan->endDate;
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
        Yii::app()->shoppingCart->put($flight);
    }
}
