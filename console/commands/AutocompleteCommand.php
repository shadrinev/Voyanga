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
            $result = array();
            $popularCitiesCodes = $this->getPopularCitiesIds();
            foreach ($popularCitiesCodes as $cityId)
            {
                $city = City::getCityByPk($cityId);
                $one = array(
                    'value' => $city->localRu,
                    'tokens' => $this->getTokensForCity($city),
                    'code' => $city->code,
                    'country' => $city->country->localRu,
                    'name' => $city->caseNom,
                    'nameAcc' => $city->caseAcc,
                    'nameGen' => $city->caseGen,
                    'namePre' => $city->casePre,
                );
                $result[] = $one;
            }
            $jsonResult = json_encode($result);
            $this->saveToOutputFolder($jsonResult);
        }
        else
        {
            echo "Unknown type\n";
            echo $this->getHelp();
        }
    }


    private function getPopularCitiesIds()
    {
        Yii::import('site.common.components.statistic.reports.*');
        $report = new PopularityOfFlightsSearch();
        $model = ReportExecuter::run($report);
        $directs = $model->findAll();
        $result = array();
        foreach ($directs as $direct)
        {
            $fromId = intval($direct->_id['departureCityId']);
            $toId = intval($direct->_id['arrivalCityId']);
            $result[] = $fromId;
            $result[] = $toId;
        }
        $result = array_unique($result);
        return $result;
    }

    private function getTokensForCity(City $city)
    {
        return array($city->localRu, $city->localEn, $city->code);
    }

    private function saveToOutputFolder($result)
    {
        $folderAlias = 'frontend.www.js';
        $folder = Yii::getPathOfAlias($folderAlias);
        $filename = 'cities.json';
        file_put_contents($folder . DIRECTORY_SEPARATOR . $filename, $result);
    }
}
