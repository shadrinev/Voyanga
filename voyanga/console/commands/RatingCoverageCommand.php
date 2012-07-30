<?php
/**
 * Command to insert/update hotels rating in database
 */
class RatingCoverageCommand extends FileProcessingCommand
{
    public function getHelp()
    {
        return <<<EOD
USAGE ImportRating path/to/cities/list.txt

EOD;
    }

    public function process($fh)
    {
        Yii::import('common.modules.hotel.models.HotelBookClient');
        while(!feof($fh))
        {
            $cityName = trim(fgets($fh));
            if($cityName)
            {
                $city = City::model()->guess($cityName);
                if(count($city)==0)
                {
                    $this->logError("Cant guess city '".$cityName."'");
                    continue;
                }
                $city = $city[0];
                if(!$city->hotelbookId) {
                    $this->logError("No hotelbook id for city '".$cityName."'");
                    continue;
                }
                $coverage = $this->coverageForCity($city);
                print $cityName . "|" . $city->localRu . "|" . $coverage . "\n";
            }
        }
    }

    private function coverageForCity($city)
    {
        $hotelSearchParams = new HotelSearchParams();
        $hotelSearchParams->checkIn = date('Y-m-d', strtotime('10.08.2012'));
        $hotelSearchParams->city = $city;
        $hotelSearchParams->duration = 1;
        $hotelSearchParams->addRoom(1, 0, false);
        $HotelClient = new HotelBookClient();
        $resultSearch = $HotelClient->fullHotelSearch($hotelSearchParams);
        Yii::app()->hotelsRating->injectRating($resultSearch, $hotelSearchParams->city);
        if(!@count($resultSearch['hotels']))
            return 'No results';
        $total = 0;
        $rated = 0;
        foreach ($resultSearch['hotels'] as $hotel) {
            $total++;
            if($hotel->rating && ($hotel->rating!=='-'))
                $rated++;
        }
        return "$rated/$total" . '=' . intval($rated/$total*100) . '%';
    }
}
