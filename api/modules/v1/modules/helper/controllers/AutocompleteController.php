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
        $cities = $this->addMoreInfo($cities);
        $result = $this->buildResult($query, $cities);
        $this->send($result);
    }

    private function buildResult($query, $cities)
    {
        /*
         * query:'Li', // Оригинальный запрос
           suggestions:['Liberia','Libyan Arab Jamahiriya','Liechtenstein','Lithuania'], // Список подсказок
           data:['LR','LY','LI','LT'] // Необязательный параметр, список ключей вариантов подсказок. Используется в callback функции
         */
        $result = array();
        $result['query'] = $query;
        foreach ($cities as $i => $city)
        {
            $suggestion = $city['label'];
            $data = $city['code'];
            $result['suggestions'][] = $suggestion;
            $result['data'][] = $data;
        }
        return $result;
    }

    private function addMoreInfo($cities)
    {
        $new = $cities;
        foreach ($cities as $i => $one)
        {
            $city = City::getCityByPk($one['id']);
            $cities[$i]['code'] = $city->code;
        }
        return $cities;
    }
}
