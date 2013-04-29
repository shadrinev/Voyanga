<?php

class AutocompleteCommand extends CConsoleCommand
{

    public function getHelp()
    {
        return <<<EOD
Generates datums for typeahead.js and put it inside web accessible folder (see https://github.com/twitter/typeahead.js#datum).

Examples:
autocomplete build --type=cities
EOD;
    }

    public function actionBuild($type)
    {
        if ($type == 'cities')
        {
            $popularCitiesCodes = $this->getPopularCitiesIds();
            echo "End getting ids from cache logs\n";
            $connection=Yii::app()->db;
            $places = $this->getPlaces();
            foreach($places as $place){
                if($place['aviaCitiesIds']){
                    foreach($place['aviaCitiesIds'] as $cityId){
                        $popularCitiesCodes[$cityId] = $cityId;
                    }
                }
                if($place['hotelCitiesIds']){
                    foreach($place['hotelCitiesIds'] as $cityId){
                        $popularCitiesCodes[$cityId] = $cityId;
                    }
                }
            }

            $sql = 'SELECT id,localRu FROM `country`';
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $countries = array();
            while(($row=$dataReader->read())!==false) {
                $countries[$row['id']] = $row;
            }
            $sql = 'SELECT * FROM `city` WHERE id IN ('.join(',',$popularCitiesCodes).') ORDER BY position desc';
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $i = 0;
            $result = array();
            $cityIdMap = array();
            while(($row=$dataReader->read())!==false) {
                $one = array(
                    'value' => $row['localRu'],
                    'tokens' => $this->getTokensForCity($row),
                    'code' => $row['code'],
                    'country' => $countries[$row['countryId']]['localRu'],
                    'name' => $row['caseNom'],
                    'nameAcc' => $row['caseAcc'],
                    'nameGen' => $row['caseGen'],
                    'namePre' => $row['casePre'],
                    't' => 1,
                    'i'=>$i
                );
                $result[$i] = $one;
                $cityIdMap[$row['id']] = $i;
                $i++;
            }
            $placeResult = array();
            foreach($places as $ind=>$place){

                $country = '';
                if($place['aviaCitiesIds']){
                    $citiesIds = array();
                    foreach($place['aviaCitiesIds'] as $cityId){
                        $citiesIds[] = $cityIdMap[$cityId];
                        if(!$country){
                            $country = $result[$cityIdMap[$cityId]]['country'];
                        }
                    }
                    $place['aviaCitiesIds'] = $citiesIds;
                }
                if($place['hotelCitiesIds']){
                    $citiesIds = array();
                    foreach($place['hotelCitiesIds'] as $cityId){
                        $citiesIds[] = $cityIdMap[$cityId];
                        if(!$country){
                            $country = $result[$cityIdMap[$cityId]]['country'];
                        }
                    }
                    $place['hotelCitiesIds'] = $citiesIds;
                }
                $one = array(
                    'value' => $place['localRu'],
                    'tokens' => array($place['localRu'],$place['localEn']),
                    'code' => '',
                    'country' => $country,
                    'name' => $place['caseNom'],
                    'nameAcc' => $place['caseAcc'],
                    'nameGen' => $place['caseGen'],
                    'namePre' => $place['casePre'],
                    'aviaIds' => $place['aviaCitiesIds'],
                    'hotelIds' => $place['hotelCitiesIds'],
                    't' => 2,
                );
                $result[] = $one;
            }
            echo "Cities added ".count($result).' '.count($placeResult)."\n";

            $jsonResult = json_encode($result,JSON_UNESCAPED_UNICODE);
            $this->saveToOutputFolder($jsonResult);
        }
        else
        {
            echo "Unknown type\n";
            echo $this->getHelp();
        }
    }

    public function getPlaces()
    {
        $connection=Yii::app()->db;

        $sql = 'SELECT * FROM `place`';
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();

        $places = array();
        while(($row=$dataReader->read())!==false) {
            $places[$row['id']] = $row;
            $places[$row['id']]['aviaCitiesIds'] = array();
            $places[$row['id']]['hotelCitiesIds'] = array();
        }

        $sql = 'SELECT * FROM `city_has_place`';
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        while(($row=$dataReader->read())!==false) {
            $places[$row['placeId']]['hotelCitiesIds'][$row['cityId']] = $row['cityId'];
        }

        $sql = 'SELECT * FROM `avia_city_has_place`';
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        while(($row=$dataReader->read())!==false) {
            $places[$row['placeId']]['aviaCitiesIds'][$row['cityId']] = $row['cityId'];
        }

        return $places;
    }

    public function actionConvert()
    {
        $folderAlias = 'frontend.www.js';
        $folder = Yii::getPathOfAlias($folderAlias);
        $filename = 'cities.json';
        $result = file_get_contents($folder . DIRECTORY_SEPARATOR . $filename);
        $result = json_decode($result, JSON_UNESCAPED_UNICODE);
        $filename2 = 'cities2.json';
        $result2 = json_encode($result);
        $result = file_put_contents($folder . DIRECTORY_SEPARATOR . $filename2, $result2);
    }


    private function getPopularCitiesIds()
    {
        $connection=Yii::app()->db;
        $sql = 'SELECT distinct `from`,`to` FROM `flight_cache`';
        $command=$connection->createCommand($sql);
        $dataReader=$command->queryAll();
        $fids = array();
        foreach ($dataReader as $one)
        {
            $fids[$one['from']] = 1;
            $fids[$one['to']] = 1;
        }

        $sql = 'SELECT distinct `cityId` FROM `hotel_cache`';
        $command=$connection->createCommand($sql);
        $dataReader=$command->queryAll();
        foreach ($dataReader as $one)
        {
            $fids[$one['cityId']] = 1;
        }

        return array_keys($fids);
    }

    private function getTokensForCity($cityRow)
    {
        $airports = Airport::model()->findAll(array('condition'=>'cityId = '.$cityRow['id']));
        $ret = array($cityRow['localRu'], $cityRow['localEn'], $cityRow['code']);
        foreach ($airports as $a)
        {
            $ret[] = $a->code;
        }
        return $ret;
    }

    private function saveToOutputFolder($result, $filename = 'cities.json')
    {
        $folderAlias = 'frontend.www.js';
        $folder = Yii::getPathOfAlias($folderAlias);
        file_put_contents($folder . DIRECTORY_SEPARATOR . $filename, $result);
    }
}
