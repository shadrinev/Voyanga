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
        $cacheKey = 'apiAutocompleteCities5' . md5(serialize(array($query, $airport_req, $hotel_req)));
        $citiesCache = Yii::app()->cache->get($cacheKey);
        if ($citiesCache) {
            $this->send($citiesCache);
            Yii::app()->end();
        }
        if ($airport_req && $hotel_req) {
            if ($airport_req > $hotel_req)
                $cities = CityManager::getCitiesWithAirportsAndHotels($query);
            else
                $cities = CityManager::getCitiesWithHotelsAndAirports($query);
        } elseif ($airport_req) {
            $cities = CityManager::getCitiesWithAirports($query);
        }
        elseif ($hotel_req) {
            $cities = CityManager::getCitiesWithHotels($query);
        }
        else
            $cities = CityManager::getCities($query);
        $cities = $this->addMoreInfo($cities);
        $result = $this->buildResult($query, $cities);
        Yii::app()->cache->set($cacheKey, $result, appParams('autocompleteCacheTime'));
        $this->send($result);
    }

    public function actionCitiesReadable(array $codes)
    {
        $cacheKey = 'apiAutocompleteCitiesReadable4' . md5(serialize($codes));
        $citiesCache = Yii::app()->cache->get($cacheKey);
        if ($citiesCache) {
            $this->send($citiesCache);
            Yii::app()->end();
        }

        $result = array();
        foreach ($codes as $cityCode) {
            $city = City::getCityByCode($cityCode);
            $element = array();
            $element['id'] = $city->id;
            $result[$cityCode] = $element;
        }
        $result = $this->addMoreInfo($result);
        Yii::app()->cache->set($cacheKey, $result, appParams('autocompleteCacheTime'));
        $this->send($result);
    }

    private function buildResult($query, $cities)
    {
        $result = array();
        foreach ($cities as $i => $city) {
            $data['value'] = $city['name'];
            $data['tokens'] = $city['tokens'];
            $data['code'] = $city['code'];
            $data['country'] = $city['country'];
            $data['name'] = $city['name'];
            $data['nameGen'] = $city['nameGen'];
            $data['nameAcc'] = $city['nameAcc'];
            $data['namePre'] = $city['namePre'];
            $result[] = $data;
        }
        return $result;
    }

    private function addMoreInfo($cities)
    {
        foreach ($cities as $i => $one) {
            $city = City::getCityByPk($one['id']);
            $cities[$i]['code'] = $city->countAirports > 0 ? $city->code : '';
            $cities[$i]['country'] = $city->country->localRu;
            $cities[$i]['name'] = $city->caseNom;
            $cities[$i]['tokens'] = $this->getTokensForCity($city);
            $cities[$i]['nameGen'] = $city->caseGen;
            $cities[$i]['nameAcc'] = $city->caseAcc;
            $cities[$i]['namePre'] = $city->casePre;
            $cities[$i]['country'] = $city->country->localRu;
        }
        return $cities;
    }

    private function getTokensForCity(City $city)
    {
        return array($city->localRu, $city->localEn, $city->code);
    }
}
