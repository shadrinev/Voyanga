<?php

class HotelsRatingComponent extends CApplicationComponent {
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

        $hotel_names_to_find = Array();
        foreach ($resultSearch['hotels'] as $hotel) {
            $hotel_names_to_find[$hotel->hotelName]=1;
        }
        $hotel_names_to_find = array_keys($hotel_names_to_find);
        $name_to_rating = HotelRating::model()
            ->findByNames($hotel_names_to_find, $city);

        foreach ($resultSearch['hotels'] as &$hotel) {
            $hotel_name = $hotel->hotelName;
            if(isset($name_to_rating[$hotel_name])){
                $hotel->rating=$name_to_rating[$hotel_name];
            }
        }

    }
}