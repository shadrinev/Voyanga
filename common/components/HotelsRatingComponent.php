<?php

class HotelsRatingComponent extends CApplicationComponent
{
    /**
     * Helper method, to inject user rating to search result
     * where possible
     * @param mixed &$resultSearch results from booking engine
     * @param City $city City model instance for this search
     */
    public function injectRating(&$resultSearch, $city)
    {
        if(!$resultSearch)
            return;
        if(!$resultSearch['hotels'])
            return;

        $hotelNamesToFind = Array();
        foreach ($resultSearch['hotels'] as $hotel) {
            $hotelNamesToFind[$hotel->hotelName]=1;
        }
        $hotelNamesToFind = array_keys($hotelNamesToFind);
        $nameToRating = HotelRating::model()
            ->findByNames($hotelNamesToFind, $city);

        foreach ($resultSearch['hotels'] as &$hotel) {
            $hotelName = $hotel->hotelName;
            if(isset($nameToRating[$hotelName])){
                $hotel->rating=$nameToRating[$hotelName];
            }
        }
    }
}