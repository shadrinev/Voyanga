<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 21.05.12
 * Time: 16:34
 * To change this template use File | Settings | File Templates.
 */
class UpdateDictionariesCommand extends CConsoleCommand
{
    public function getHelp()
    {
        return <<<EOD
USAGE UpdateDictionaries [OPTIONS]
   ...
Options:
--type=(value) - Default value airports
   ...
EOD;
    }

    public function getCities($countryId,$name,$nameRu = '')
    {
        if((strlen($name)== 3) && (strtoupper($name) == $name)){
            $cities = City::model()->findAllByAttributes(array('countryId'=>$countryId,'code'=>$name));
        }
        else
        {
            if(!$nameRu){
                $nameRu = $name;
            }

            $criteria = new CDbCriteria();
            $criteriaOr = array();
            $criteriaParams = array();

            if($nameRu)
            {
                $criteriaOr[] = 'localRu LIKE :localRu';
                $criteriaParams[':localRu'] = $nameRu.'%';
            }
            if($name)
            {
                if(strpos($name,','))
                {
                    $tmp = explode(',',$name);
                    $criteriaOr[] = 'localEn LIKE :localEn';
                    $criteriaParams[':localEn'] = $tmp[0].'%';
                }
                else
                {
                    $criteriaOr[] = 'localEn LIKE :localEn';
                    $criteriaParams[':localEn'] = $name.'%';
                }

            }
            $criteria->addCondition('('.implode(' OR ',$criteriaOr).')');
            $criteria->addCondition(' countryId=:countryId');
            $criteriaParams[':countryId'] = $countryId;
            $criteria->params = $criteriaParams;

            $cities = City::model()->findAll($criteria);
        }
        return $cities;
    }

    public function selectAirport($data,$column,$lineData,$outfp,$city)
    {
        if(!($data[$column['icao_code']]))
        {
            echo "Dont found ICAO code. Skipping line: $lineData \n";
            fwrite($outfp,$lineData."|icao\n");
            return;
        }
        if(!($data[$column['iata_code']]))
        {
            echo "Dont found IATA code. Skipping line: $lineData \n";
            fwrite($outfp,$lineData."|iata\n");
            return;
        }
        try{
            $airport = Airport::getAirportByCode($data[$column['iata_code']]);
        }catch (CException $e){
            $airport = null;
        }
        if(!$airport)
        {
            if((strpos($data[$column['airport_name_ru']],'Metropolitan') !== FALSE) && (strpos($data[$column['airport_name_ru']],'Airport') === FALSE))
            {
                return;
            }
            echo "Do you want create airport? type y\n";
            echo "Data line: $lineData \n";
            $char = trim(fgets(STDIN));
            if($char == 'y')
            {
                $airport = new Airport();
                $airport->cityId = $city->id;
                $data[$column['airport_name_ru']] = $data[$column['airport_name_ru']] ? $data[$column['airport_name_ru']] : $data[$column['airport_name_en']];
                if(!$data[$column['airport_name_ru']]){
                    echo "Airport name not found. Skipped line: $lineData \n";
                    fwrite($outfp,$lineData."|iata\n");
                    return;
                }
                $airport->localRu = $data[$column['airport_name_ru']];
                $airport->localEn = $data[$column['airport_name_en']];
                $airport->code = $data[$column['iata_code']];
                $airport->icaoCode = $data[$column['icao_code']];
                $airport->latitude = $data[$column['latitude']];
                $airport->longitude = $data[$column['longitude']];
                $airport->position = 0;
                $airport->site = $data[$column['website']];
                $airport->validate();
                print_r($airport->errors);
                $airport->save();

                $airport->id = $airport->getPrimaryKey();

            }

        }
        if($airport)
        {
            $changed = false;
            if(!$airport->localRu){
                if($data[$column['airport_name_ru']]){
                    $airport->localRu = $data[$column['airport_name_ru']];
                    $changed = true;
                }
            }
            if(!$airport->localEn){
                if($data[$column['airport_name_en']]){
                    $airport->localEn = $data[$column['airport_name_en']];
                    $changed = true;
                }
            }
            if(!$airport->latitude){
                if($data[$column['latitude']]){
                    $airport->latitude = $data[$column['latitude']];
                    $changed = true;
                }
            }
            if(!$airport->longitude){
                if($data[$column['longitude']]){
                    $airport->longitude = $data[$column['longitude']];
                    $changed = true;
                }
            }
            if(!$airport->icaoCode){
                if($data[$column['icao_code']]){
                    $airport->icaoCode = $data[$column['icao_code']];
                    $changed = true;
                }
            }
            if(!$airport->site){
                if($data[$column['website']]){
                    $airport->site = $data[$column['website']];
                    $changed = true;
                }
            }
            if($changed){
                $airport->save();
            }

        }


    }

    /**
     * Execute the action.
     * @param array command line parameters specific for this command
     */
    public function actionIndex($type = 'airports',$filename = '')
    {
        if($type == 'airports' )
        {
            if($filename)
            {
                $path = Yii::getPathOfAlias('application.runtime');
                if(file_exists($filename))
                {
                    $fp = fopen($filename,'r');
                    $outfp = fopen($path.'/'.basename($filename),'w');
                    if(!$outfp)
                    {
                        echo "Cant open file ".$path.'/'.basename($filename)." for writing\n";
                        die();
                    }
                    $formatLine = fgets($fp);
                    $formatLine = str_replace(array("\r","\n"),array(''),$formatLine);
                    $columnNames = explode('|',$formatLine);
                    //print_r($formatLine);die();
                    $column = array();
                    foreach($columnNames as $i=>$columnName){
                        $column[$columnName] = $i;
                    }
                    //print_r($column);
                    $skipAll = false;
                    while(!feof($fp)){
                        $lineData = fgets($fp);
                        $lineData = str_replace(array("\r","\n"),array(''),$lineData);
                        $lineData = iconv("cp1251","UTF-8",$lineData);
                        $data = explode('|',$lineData);
                        $needSave = true;

                        $country = Country::getCountryByCode($data[$column['country_code']]);
                        if($country)
                        {
                            $cities = $this->getCities($country->id,$data[$column['city_en']],$data[$column['city_ru']]);
                            if(!$cities){
                                $cities = City::model()->findAllByAttributes(array('countryId'=>$country->id,'code'=>$data[$column['iata_code']]));
                            }
                            $abort = false;
                            while(!$abort)
                            {
                                if($cities)
                                {
                                    if(count($cities)>1){
                                        $citiesTmp = City::model()->findAllByAttributes(array('countryId'=>$country->id,'code'=>$data[$column['iata_code']]));
                                        if($citiesTmp && count($citiesTmp) == 1)
                                        {
                                            $cities = $citiesTmp;
                                        }
                                    }
                                    if(count($cities)>1)
                                    {
                                        echo "Found ".count($cities)." cities for line: $lineData\n";
                                        echo str_pad('code', 5, " ", STR_PAD_RIGHT).str_pad('localRu', 15, " ", STR_PAD_LEFT).str_pad('localEn', 15, " ", STR_PAD_LEFT)."\n";
                                        foreach($cities as $city)
                                        {
                                            echo str_pad($city->code, 5, " ", STR_PAD_RIGHT).str_pad($city->localRu, 15, " ", STR_PAD_LEFT).str_pad($city->localEn, 15, " ", STR_PAD_LEFT)."\n";
                                        }
                                        echo "Type code of the selected city:\n";
                                        $name = trim(fgets(STDIN));
                                        $cities = $this->getCities($country->id,$name);

                                    }else{
                                        $this->selectAirport($data,$column,$lineData,$outfp,$cities[0]);
                                        $abort = true;
                                    }
                                }else{
                                    echo "City not found in Db. Type s - skip, c - create, f - try find in db:\n";
                                    echo "$lineData\n";
                                    //$char = trim(fgets(STDIN));
                                    $char = 's';
                                    if($char == 's'){
                                        echo "Dont found any same city. Skipping line: $lineData \n";
                                        fwrite($outfp,$lineData."|nocity\n");
                                        $abort = true;
                                    }elseif($char == 'c'){
                                        $city = new City();
                                        echo "Creating new city...\n";
                                        $city->countryId = $country->id;
                                        if(!$data[$column['city_ru']]){
                                            $data[$column['city_ru']] = $data[$column['city_en']];
                                        }
                                        echo "City name rus (default {$data[$column['city_ru']]}):\n";
                                        $newName = trim(fgets(STDIN));
                                        if($newName){
                                            $city->localRu = $newName;
                                        }else{
                                            $city->localRu = $data[$column['city_ru']];
                                        }
                                        echo "City name eng (default {$data[$column['city_en']]}):\n";
                                        $newName = trim(fgets(STDIN));
                                        if($newName){
                                            $city->localEn = $newName;
                                        }else{
                                            $city->localEn = $data[$column['city_en']];
                                        }
                                        echo "City code:\n";
                                        $newCode = trim(fgets(STDIN));
                                        if($newCode){
                                            $city->code = $newCode;
                                        }else{
                                            continue;
                                        }
                                        $city->position = 0;
                                        $city->save();
                                        $city->id = $city->getPrimaryKey();
                                        $this->selectAirport($data,$column,$lineData,$outfp,$city);
                                    }else{
                                        echo "Type part of city name:\n";
                                        $name = trim(fgets(STDIN));
                                        $cities = $this->getCities($country->id,$name);
                                    }
                                }
                            }
                        }else{
                            echo "Country not found, skipping line: $lineData \n";
                            fwrite($outfp,$lineData."|nocountry\n");
                        }
                    }
                    fclose($fp);
                    fclose($outfp);
                }
                else
                {
                    echo 'Import file not found on path: '.$filename;
                }
            }
            else
            {
                echo 'Option --filename cant be empty';
            }
        }
        if($type == 'iconv' )
        {
            if($filename)
            {
                $path = Yii::getPathOfAlias('application.runtime');
                if(file_exists($filename))
                {
                    $data = file_get_contents($filename);
                    echo iconv('cp1251','UTF-8',$data);
                }
            }
        }
        if($type == 'test'){
            $cities = City::model()->findAllByAttributes(array('code'=>'LED','countryId'=>174));
            if($cities)
            {
                echo "Found ".count($cities)." cities for line: \n";
                echo str_pad('code', 5, " ", STR_PAD_RIGHT).str_pad('localRu', 19, " ", STR_PAD_LEFT).str_pad('localEn', 19, " ", STR_PAD_LEFT)."\n";
                foreach($cities as $city)
                {
                    echo str_pad($city->code, 5, " ", STR_PAD_RIGHT).str_pad($city->localRu, 19, " ", STR_PAD_LEFT).str_pad($city->localEn, 19, " ", STR_PAD_LEFT).' '.$city->country->id."\n";
                }
            }
        }


    }
}
