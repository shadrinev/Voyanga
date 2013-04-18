<?php
class DumpPopularDirectionsCommand extends CConsoleCommand
{
    public function run($args)
    {
        $orderBookings = OrderBooking::model()->findAll("partnerId IS NOT NULL AND timestamp > TIMESTAMPADD(DAY, -33, NOW())");
        $results = [];
        foreach($orderBookings as $orderBook)
        {
            if(!count($orderBook->flightBookers))
                continue;
            $booking =  $orderBook->flightBookers[0];
            @$results[$key] = 1 + @$results[$key];
            $key = $booking->flightVoyage->departureCity->localRu . ' - ' . $booking->flightVoyage->arrivalCity->localRu;
            echo "\n";
         }
    }
}
