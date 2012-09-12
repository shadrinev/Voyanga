<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 07.09.12 11:09
 */
class AutocompleteController extends ApiController
{
    public function actionCity($query, $airport_req = false, $hotel_req = false)
    {
        $items = array();
        if ($airport_req && $hotel_req)
        {
            if ($airport_req > $hotel_req)
                $cities = CityManager::getCitiesWithAirportsAndHotels($query);
            else
                $cities = CityManager::getCitiesWithHotelsAndAirports($query);
        }
        elseif ($airport_req)
        {
            $cities = CityManager::getCitiesWithAirports($query);
        }
        elseif ($hotel_req)
        {
            $cities = CityManager::getCitiesWithHotels($query);
        }
        else
            $cities = CityManager::getCities($query);
        $this->send($cities);
    }
}
