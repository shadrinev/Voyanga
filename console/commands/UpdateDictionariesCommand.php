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

    public static $sng = array('RU','UA','BY','AZ','AM','BG','GE','KZ','KG','LV','LT','MD','PL','SK','SI','TJ','TM','UZ');

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

    public function getCountries($name,$nameRu = '')
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
        if($criteriaOr)
        {
            $criteria->addCondition('('.implode(' OR ',$criteriaOr).')');
        }
        //$criteria->addCondition(' hotelbookId IS NULL');
        $criteria->params = $criteriaParams;

        $countries = Country::model()->findAll($criteria);

        return $countries;
    }

    public function isIataCode($str)
    {
        $ret = false;
        if(mb_strlen($str) == 3){
            $out = mb_ereg_replace("[^A-Z]","",$str);
            if($out === $str) $ret = true;
        }
        return $ret;
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
        if($type == 'airlineWeight' )
        {
            echo 'INN';
            if($filename)
            {
                $path = Yii::getPathOfAlias('application.runtime');
                if(file_exists($filename))
                {
                    $airlinessxe = simplexml_load_string(file_get_contents($filename));
                    foreach($airlinessxe->Item as $item){
                        $airlineCode = (string)$item['id'];
                        $modified = false;
                        try{
                            $airline = Airline::getAirlineByCode($airlineCode);
                        }catch (CException $e){
                            $airline = false;
                        }
                        if($airline){

                            $airlineLocalRu = (string)$item['rusname'];
                            $airlineLocalEn = (string)$item['name'];
                            if(UtilsHelper::countRussianCharacters($airline->localRu) <= 0){
                                $airline->localRu = $airlineLocalRu;
                                $modified = true;
                            }else{
                                echo "o_O !!! ".$airline->localRu;
                            }
                            if(!$airline->localEn && $airlineLocalEn){
                                $airline->localEn = $airlineLocalEn;
                                $modified = true;
                            }

                            $economPrice = (string)$item->luggage->econom->price;

                            $economFreeWeight = (string)$item->luggage->econom->weight;

                            if( $airline->economFreeWeight && $economFreeWeight){
                                if($economPrice == 'charge'){
                                    $airline->economFreeWeight = 0;
                                }else{
                                    $airline->economFreeWeight = str_replace('kg','',$economFreeWeight);
                                }
                                $modified = true;
                            }

                            $businessPrice = (string)$item->luggage->business->price;

                            $businessFreeWeight = (string)$item->luggage->business->weight;
                            if( $airline->businessFreeWeight && $businessFreeWeight){
                                if($businessPrice == 'charge'){
                                    $airline->businessFreeWeight = 0;
                                }else{
                                    $airline->businessFreeWeight = str_replace('kg','',$businessFreeWeight);
                                }
                                $modified = true;
                            }
                            $economDescription = (string)$item->luggage->econom->description;
                            if( $airline->economDescription && $economDescription){
                                $economDescription = substr($economDescription, strpos($economDescription,'Багаж не должен'));
                                $airline->economDescription = trim($economDescription);
                                $modified = true;
                            }
                            $businessDescription = (string)$item->luggage->business->description;
                            if( $airline->businessDescription && $businessDescription){
                                $businessDescription = substr($businessDescription, strpos($businessDescription,'Багаж не должен'));
                                $airline->businessDescription = trim($businessDescription);
                                echo "try modyf ";
                                $modified = true;
                            }

                            if($modified){
                                $airline->save();
                            }
                        }else{
                            echo "!!!! NOT FOUND {$airlineCode}";
                            $airline = new Airline();
                            $airline->code = $airlineCode;
                            $airlineLocalRu = (string)$item['rusname'];
                            $airlineLocalEn = (string)$item['name'];
                            if(UtilsHelper::countRussianCharacters($airline->localRu) <= 0){
                                $airline->localRu = $airlineLocalRu;
                                $airline->localEn = $airlineLocalEn;
                                $modified = true;
                            }

                            $economFreeWeight = (string)$item->luggage->econom->weight;
                            if(!$airline->economFreeWeight && $economFreeWeight){
                                $airline->economFreeWeight = str_replace('kg','',$economFreeWeight);
                                $modified = true;
                            }
                            $businessFreeWeight = (string)$item->luggage->business->weight;
                            if(!$airline->businessFreeWeight && $businessFreeWeight){
                                $airline->businessFreeWeight = str_replace('kg','',$businessFreeWeight);
                                $modified = true;
                            }
                            $economDescription = (string)$item->luggage->econom->description;
                            if(!$airline->economDescription && $economDescription){
                                $airline->economDescription = $economDescription;
                                $modified = true;
                            }
                            $businessDescription = (string)$item->luggage->business->description;
                            if(!$airline->businessDescription && $businessDescription){
                                $airline->businessDescription = $businessDescription;
                                $modified = true;
                            }
                            if(!$airline->save()){
                                CVarDumper::dump($airline->getErrors());
                            }
                        }
                        echo "airline {$airlineCode} {$airlineLocalRu} {$economFreeWeight}\n";
                    }
                    echo 'ютф?';
                    //CVarDumper::dump($airlinessxe);
                }
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
        if($type == 'hotelbookCountries')
        {
            Yii::import('site.common.modules.hotel.models.*');

            $HotelClient = new HotelBookClient();
            $countries = $HotelClient->getCountries();
            print_r($countries);
            foreach($countries as $hotelCountry)
            {
                $nameRu = $hotelCountry['nameRu'];
                $name = $hotelCountry['nameEn'];
                $ourCountries = $this->getCountries($name,$nameRu);
                if($ourCountries){
                    if(count($ourCountries) > 1){
                        echo "Found ".count($ourCountries)." countries for nameEn:{$hotelCountry['nameEn']} nameRu:{$hotelCountry['nameRu']} \n";
                        echo str_pad('code', 5, " ", STR_PAD_RIGHT).str_pad('localRu', 19, " ", STR_PAD_LEFT).str_pad('localEn', 19, " ", STR_PAD_LEFT)."\n";
                        foreach($ourCountries as $country)
                        {
                            echo str_pad($country->code, 5, " ", STR_PAD_RIGHT).str_pad($country->localRu, 19, " ", STR_PAD_LEFT).str_pad($country->localEn, 19, " ", STR_PAD_LEFT).' '.$country->id."\n";
                        }
                        echo "Enter id of country or 's' - for skip \n";
                        $oldId = trim(fgets(STDIN));
                        if($oldId !== 's')
                        {
                            $country = Country::getCountryByPk(intval($oldId));
                            $country->hotelbookId = $hotelCountry['id'];
                            $country->save();
                        }
                    }else{
                        $country = $ourCountries[0];
                        if(!$country->hotelbookId)
                        {
                            $country->hotelbookId = $hotelCountry['id'];
                            $country->save();
                        }
                    }
                }else{
                    $nameRu = substr($hotelCountry['nameRu'],0,strpos($hotelCountry['nameRu'],' '));
                    $name = substr($hotelCountry['nameEn'],0,strpos($hotelCountry['nameRu'],' '));
                    $ourCountries = $this->getCountries($name,$nameRu);
                    if($ourCountries){
                        if(count($ourCountries) > 1){
                            echo "Found ".count($ourCountries)." countries for nameEn:{$hotelCountry['nameEn']} nameRu:{$hotelCountry['nameRu']} \n";
                            echo str_pad('code', 5, " ", STR_PAD_RIGHT).str_pad('localRu', 19, " ", STR_PAD_LEFT).str_pad('localEn', 19, " ", STR_PAD_LEFT)."\n";
                            foreach($ourCountries as $country)
                            {
                                echo str_pad($country->code, 5, " ", STR_PAD_RIGHT).str_pad($country->localRu, 19, " ", STR_PAD_LEFT).str_pad($country->localEn, 19, " ", STR_PAD_LEFT).' '.$country->id."\n";
                            }
                            echo "Enter id of country or 's' - for skip \n";
                            $oldId = trim(fgets(STDIN));
                            if($oldId !== 's')
                            {
                                $country = Country::getCountryByPk(intval($oldId));
                                $country->hotelbookId = $hotelCountry['id'];
                                $country->save();
                            }
                        }else{
                            if(!$country->hotelbookId)
                            {
                                $country = $ourCountries[0];
                                $country->hotelbookId = $hotelCountry['id'];
                                $country->save();
                            }
                        }
                    }
                }
            }
        }

        if($type == 'geonames')
        {
            $criteria=new CDbCriteria;
            $criteria->condition='hotelbookId IS NOT NULL';
            //$criteria->params=array(':postID'=>10);
            $start = false;

            $countries = Country::model()->findAll($criteria);

            $formatLine = 'geonameid|name_mixed|name|names|latitude|longitude|feature|feature_code|country_code|country_codes|state_code|admin2_code|admin3_code|admin4_code|population|elevation|dem|timezone|modification_date';
            $formatLine = str_replace(array("\r","\n"),array(''),$formatLine);
            $columnNames = explode('|',$formatLine);
            //print_r($formatLine);die();
            $column = array();
            foreach($columnNames as $i=>$columnName){
                $column[$columnName] = $i;
            }

            foreach($countries as $country)
            {
                $filename = '/srv/www/oleg.voyanga/public_html/console/data_files/'.$country->code.'.txt';
                if(!$start)
                {
                    if($country->code == 'HR')
                    {
                        $start = true;
                    }else{
                        continue;
                    }
                }

                if($filename)
                {
                    $path = Yii::getPathOfAlias('application.runtime');
                    if(file_exists($filename))
                    {
                        echo "Starting parse ".$country->code.".txt\n";
                        $fp = fopen($filename,'r');
                        //$countryCode = basename($filename);
                        //$countryCode = substr($countryCode,0,strpos($countryCode,'.'));

                        $outfp = fopen($path.'/'.basename($filename),'w');
                        if(!$outfp)
                        {
                            echo "Cant open file ".$path.'/'.basename($filename)." for writing\n";
                            die();
                        }


                        //print_r($column);
                        $skipAll = false;
                        $lineCount = 0;
                        $addedCount = 0;
                        $haveAltNames = 0;
                        $haveRuName = 0;
                        $haveIataCode = 0;
                        echo str_pad('Parsed', 10, " ", STR_PAD_RIGHT).str_pad('Added', 10, " ", STR_PAD_RIGHT).str_pad('AltNames', 10, " ", STR_PAD_RIGHT).str_pad('RuName', 10, " ", STR_PAD_RIGHT).str_pad('IataCode', 10, " ", STR_PAD_RIGHT)."\n";

                        while(!feof($fp)){
                            $lineData = fgets($fp);
                            $lineData = str_replace(array("\r","\n"),array(''),$lineData);
                            $data = explode("\t",$lineData);
                            if(!$data){
                                continue;
                            }
                            if(count($data)<=11){
                                continue;
                            }
                            $lineCount++;
                            $geoNames = new GeoNames();
                            if($data[$column['feature_code']])
                            {
                                if($data[$column['feature_code']] == 'PPL' || $data[$column['feature_code']] == 'PPLA' || $data[$column['feature_code']] == 'PPLC' || $data[$column['feature_code']] == 'AIRP')
                                {
                                    $geoNames->type = $data[$column['feature_code']];
                                    $geoNames->nameEn = $data[$column['name']];
                                    $geoNames->soundexEn = UtilsHelper::soundex($geoNames->nameEn);
                                    $geoNames->countryCode = $data[$column['country_code']];
                                    $geoNames->latitude = $data[$column['latitude']];
                                    $geoNames->longitude = $data[$column['longitude']];
                                    if(in_array($data[$column['country_code']],self::$sng))
                                    {
                                        $geoNames->nameRu = UtilsHelper::fromTranslite($geoNames->nameEn);
                                        $ruFactor = 6;
                                    }else
                                    {
                                        $geoNames->nameRu = UtilsHelper::ruTranscript($geoNames->nameEn);
                                        $ruFactor = 12;
                                    }

                                    if($data[$column['state_code']])
                                    {
                                        $geoNames->stateCode = $data[$column['state_code']];
                                    }
                                    if($data[$column['names']])
                                    {
                                        $names = explode(',',$data[$column['names']]);
                                        $rusWord = array('index'=>0,'count'=>0,'replacements'=>20);
                                        $manyRusWords = false;
                                        $altNames = array();
                                        $iataCode = '';
                                        foreach($names as $ind=>$altName)
                                        {
                                            $altNames[] = $altName;
                                            if($this->isIataCode($altName)){
                                                if($altName != mb_strtoupper($data[$column['name']]))
                                                {
                                                    $iataCode = $altName;
                                                    $geoNames->iataCode = $iataCode;
                                                    $haveIataCode++;
                                                    unset($altNames[$ind]);
                                                    continue;
                                                }
                                            }
                                            $n = UtilsHelper::countRussianCharacters($altName);
                                            if($n > 0)
                                            {
                                                $altCase = mb_convert_case($altName, MB_CASE_TITLE, "UTF-8");
                                                $k = levenshtein($geoNames->nameRu,$altName);
                                                $l = levenshtein($geoNames->nameRu,$altCase);
                                                if($l < $k)
                                                {
                                                    $k = $l;
                                                    $altNames[$ind] = $altCase;
                                                    $names[$ind] = $altCase;
                                                }
                                            }
                                            if(($n > 0) && ($k < $rusWord['replacements'])){
                                                if($rusWord['count']>0){
                                                    $manyRusWords = true;
                                                }
                                                $rusWord['index'] = $ind;
                                                $rusWord['replacements'] = $k;
                                                $rusWord['count'] = $n;
                                            }
                                        }
                                        if($manyRusWords)
                                        {
                                            fwrite($outfp, "{$geoNames->latitude}|{$geoNames->longitude}|{$data[$column['name']]}|{$names[$rusWord['index']]}\n");
                                        }
                                        if($rusWord['count']>0)
                                        {
                                            if($rusWord['replacements'] < $ruFactor)
                                            {
                                                $geoNames->nameRu = $names[$rusWord['index']];
                                                $haveRuName++;
                                                //unset($altNames[$rusWord['index']]);
                                            }
                                        }
                                        $geoNames->alternateNames = implode(',',$altNames);
                                        unset($names);
                                        unset($altNames);
                                        unset($rusWord);
                                        $haveAltNames++;
                                    }
                                    $geoNames->soundexRu = UtilsHelper::soundex($geoNames->nameRu,'RU');
                                    $geoNames->metaphoneRu = UtilsHelper::ruMetaphone($geoNames->nameRu);
                                    $geoNames->save();
                                    $addedCount++;
                                }
                            }
                            unset($geoNames);
                            unset($lineData);
                            unset($data);
                            if(($lineCount % 30000) == 0)
                            {
                                echo str_pad($lineCount, 10, " ", STR_PAD_RIGHT).str_pad($addedCount, 10, " ", STR_PAD_RIGHT).str_pad($haveAltNames, 10, " ", STR_PAD_RIGHT).str_pad($haveRuName, 10, " ", STR_PAD_RIGHT).str_pad($haveIataCode, 10, " ", STR_PAD_RIGHT)."\n";
                            }
                        }
                        echo str_pad($lineCount, 10, " ", STR_PAD_RIGHT).str_pad($addedCount, 10, " ", STR_PAD_RIGHT).str_pad($haveAltNames, 10, " ", STR_PAD_RIGHT).str_pad($haveRuName, 10, " ", STR_PAD_RIGHT).str_pad($haveIataCode, 10, " ", STR_PAD_RIGHT)."\n";
                        fclose($outfp);
                    }//endif file exists
                    else
                    {
                        echo "Not found ".$country->code.".txt\n";
                    }
                }//endif filename
            }//endforeach countries
        }
        if($type == 'hotelbookCities')
        {
            Yii::import('site.common.modules.hotel.models.*');
            $HotelClient = new HotelBookClient();

            $criteria=new CDbCriteria;
            $criteria->condition='hotelbookId IS NOT NULL';
            $path = Yii::getPathOfAlias('application.runtime');
            //$criteria->params=array(':postID'=>10);

            $countries = Country::model()->findAll($criteria);
            $start = false;
            foreach($countries as $country)
            {
                if(!$start)
                {
                    /*if($country->code == 'UA')
                    {
                        $start = true;
                    }else{
                        continue;
                    }/**/
                    if($country->code == 'US')
                    {
                        continue;
                    }
                }
                $hotelCities = $HotelClient->getCities($country->hotelbookId);
                $oneResult = 0;
                $someResult = 0;
                $manyResult = 0;
                $noResult = 0;
                $outfp = fopen($path.'/'.$country->code.'-hotel-log.txt','w');
                if(!$outfp)
                {
                    echo "Cant open file ".$path.'/'.$country->code."-hotel-log.txt for writing\n";
                    die();
                }
                foreach($hotelCities as $hotelCity)
                {

                    if(in_array($country->code,self::$sng))
                    {
                        $hotelCity['nameRu'] = UtilsHelper::fromTranslite($hotelCity['nameEn']);
                    }else
                    {
                        $hotelCity['nameRu'] = UtilsHelper::ruTranscript($hotelCity['nameEn']);
                    }
                    $hotelCity['metaphoneRu'] = UtilsHelper::ruMetaphone($hotelCity['nameRu']);
                    $hotelCity['sondexEn'] = UtilsHelper::soundex($hotelCity['nameEn']);
                    $hotels = $HotelClient->getHotels($hotelCity);
                    if(count($hotels) == 0){
                        //We are don't need empty cities
                        continue;
                    }
                    $cnt = 0;
                    $haveCoordinates = false;
                    foreach ($hotels as $hotelObj)
                    {
                        $cnt++;
                        $query[$hotelObj['id']] = $HotelClient->hotelDetail($hotelObj['id'], true);
                        if($cnt > 10) break;
                    }
                    $HotelClient->processAsyncRequests();
                    foreach ($query as $hotelId => $responseId)
                    {
                        if (isset($HotelClient->requests[$responseId]['result'])){
                            if(isset($HotelClient->requests[$responseId]['result']->latitude,$HotelClient->requests[$responseId]['result']->longitude) && $HotelClient->requests[$responseId]['result']->latitude && $HotelClient->requests[$responseId]['result']->longitude){
                                $haveCoordinates = true;
                                $possibleLatitude = $HotelClient->requests[$responseId]['result']->latitude;
                                $possibleLongitude = $HotelClient->requests[$responseId]['result']->longitude;
                                break;
                            }
                        }

                    }
                    $cityCriteria = new CityFindCriteria();
                    $cityCriteria->paramValues = array('countryCode'=>$country->code,'nameEn'=>$hotelCity['nameEn'],'metaphoneRu'=>$hotelCity['metaphoneRu'],'soundexEn'=>$hotelCity['sondexEn']);
                    //$prevCriteria = null;
                    //$prevCount = 0;
                    $currCriteria = null;
                    $count = 0;
                    $findEnd = false;
                    $needSave = false;
                    while(!$findEnd)
                    {
                        $prevCriteria = $currCriteria;
                        $prevCount = $count;
                        $currCriteria = $cityCriteria->getCriteria();
                        $count =  GeoNames::model()->count($currCriteria);
                        $findGeo = null;
                        echo "Params: ".implode(',',$cityCriteria->paramUsed).' count:'.$count."\n";
                        if($haveCoordinates && $count<15){
                            $geoNames = GeoNames::model()->findAll($currCriteria);
                            foreach($geoNames as $geoName){
                                if($geoName->latitude && $geoName->longitude){
                                    $distance = intval(UtilsHelper::calculateTheDistance($geoName->latitude, $geoName->longitude, $possibleLatitude, $possibleLongitude));
                                    if ($distance < 10000)
                                    {
                                        $findGeo = $geoName;
                                        $count = 1;
                                    }
                                }
                            }
                        }

                        if($count>1){
                            $findEnd = !$cityCriteria->setPlus();
                        }elseif($count<1){
                            $findEnd = !$cityCriteria->setMinus();
                        }else{
                            $findEnd = true;
                        }
                    }

                    if($count<1)
                    {
                        if($count<$prevCount)
                        {
                            $currCriteria = $prevCriteria;
                            $count = $prevCount;
                            $needSave = true;
                        }
                    }else{
                        $needSave = true;
                    }
                    if($needSave){
                        if($count>6){
                            echo "Many results for city {$hotelCity['nameEn']} - $count\n";
                            $manyResult++;
                            fwrite($outfp, "{$hotelCity['nameEn']}|{$hotelCity['nameRu']}|{$hotelCity['id']}|{$count}\n");
                        }elseif($count>1){
                            //echo "Many results for city {$hotelCity['nameEn']} - $count\n";
                            $geoName = GeoNames::model()->find($currCriteria);
                            echo "Possible by city name city {$hotelCity['nameEn']} City: {$geoName->nameEn} Ru: {$geoName->nameRu} IATA: {$geoName->iataCode} coords: {$geoName->longitude} {$geoName->latitude}\n";
                            $someResult++;
                            fwrite($outfp, "{$hotelCity['nameEn']}|{$hotelCity['nameRu']}|{$hotelCity['id']}|{$count}\n");
                        }else{
                            if($findGeo){
                                $geoName = $findGeo;
                            }else{
                                $geoName = GeoNames::model()->find($currCriteria);
                            }
                            echo "Found by city name city {$hotelCity['nameEn']} City: {$geoName->nameEn} Ru: {$geoName->nameRu} IATA: {$geoName->iataCode} coords: {$geoName->longitude} {$geoName->latitude}".($findGeo ? " ByCoords" : "")."\n";
                            if($geoName->iataCode)
                            {
                                $city = City::model()->findByAttributes(array('code'=>$geoName->iataCode,'countryId'=>$country->id));
                                if(!$city){
                                    $city = new City();
                                    $city->localEn = $geoName->nameEn;
                                    $city->localRu = $geoName->nameRu;
                                    $city->countryId = $country->id;
                                    $city->code = $geoName->iataCode;
                                }
                                $city->latitude = $geoName->latitude;
                                $city->longitude = $geoName->longitude;
                                $city->metaphoneRu = $geoName->metaphoneRu;
                                $city->hotelbookId = $hotelCity['id'];
                                if($geoName->stateCode)
                                {
                                    $city->stateCode = $geoName->stateCode;
                                }
                                $city->save();
                            }
                            else
                            {
                                $city = City::model()->findByAttributes(array('localEn'=>$geoName->nameEn,'localRu'=>$geoName->nameRu,'countryId'=>$country->id));
                                if(!$city){
                                    $city = new City();
                                    $city->localEn = $geoName->nameEn;
                                    $city->localRu = $geoName->nameRu;
                                    $city->countryId = $country->id;
                                    $city->code = $geoName->iataCode;
                                }
                                $city->latitude = $geoName->latitude;
                                $city->longitude = $geoName->longitude;
                                $city->metaphoneRu = $geoName->metaphoneRu;
                                $city->hotelbookId = $hotelCity['id'];
                                if($geoName->stateCode)
                                {
                                    $city->stateCode = $geoName->stateCode;
                                }
                                $city->save();
                            }

                            $oneResult++;
                        }
                    }else{
                        echo "Dont found anything for city {$hotelCity['nameEn']}\n";
                        $noResult++;
                        fwrite($outfp, "{$hotelCity['nameEn']}|{$hotelCity['nameRu']}|{$hotelCity['id']}|{$count}\n");
                    }


                    //$criteria = new EMongoCriteria(array('conditions'=>array('countryCode'=>array('equals'=>$country->code)) ));
                    //$criteria->limit(10);
                    //$geoNames = GeoNames::model()->findAll($criteria);

                }
                Echo "NoResults:{$noResult} OneResult:{$oneResult} SomeResults:{$someResult} ManyResults: {$manyResult}\n";
                fclose($outfp);
                //break;
            }
        }
        if($type == 'iataCode')
        {
            $criteria=new CDbCriteria;
            $criteria->condition='hotelbookId IS NOT NULL';
            $path = Yii::getPathOfAlias('application.runtime');
            //$criteria->params=array(':postID'=>10);

            $countries = Country::model()->findAll($criteria);
            $start = false;
            foreach($countries as $country)
            {
                if(!$start)
                {
                    /*
                    if($country->code == 'RU')
                    {
                        $start = true;
                    }else{
                        continue;
                    }/**/
                    /*if($country->code == 'US')
                    {
                        continue;
                    }/**/
                }

                $criteriaCity=new CDbCriteria;
                $criteriaCity->addCondition('code IS NOT NULL');
                $criteriaCity->addCondition('countryId='.$country->id);
                $path = Yii::getPathOfAlias('application.runtime');
                //$criteria->params=array(':postID'=>10);

                $countCities = City::model()->count($criteriaCity);
                echo "Start parsing for country {$country->code} {$countCities} results\n";
                $pageLimit = 20;
                $n = ceil($countCities / $pageLimit);
                $criteriaCity->limit = $pageLimit;

                $oneResult = 0;
                $modifyCount = 0;
                $noResult = 0;
                $lineCount = 0;
                $outfp = fopen($path.'/'.$country->code.'-iata-log.txt','w');
                if(!$outfp)
                {
                    echo "Cant open file ".$path.'/'.$country->code."-hotel-log.txt for writing\n";
                    die();
                }
                for($i=0;$i<$n;$i++)
                {
                    $criteriaCity->offset = $i*$pageLimit;
                    //print_r($criteriaCity);
                    //echo "\n";

                    $cities = City::model()->findAll($criteriaCity);

                    foreach($cities as $city)
                    {
                        $lineCount++;
                        if( UtilsHelper::countRussianCharacters($city->localRu) )
                        {
                            $nameRu = $city->localRu;
                        }
                        else
                        {
                            if(in_array($country->code,self::$sng))
                            {
                                $nameRu = UtilsHelper::fromTranslite($city->localEn);
                            }else
                            {
                                $nameRu = UtilsHelper::ruTranscript($city->localEn);
                            }
                        }
                        $metaphoneRu = UtilsHelper::ruMetaphone($nameRu);
                        $sondexEn = UtilsHelper::soundex($city->localEn);

                        $cityCriteria = new CityFindCriteria();
                        $cityCriteria->states = array('value'=>array('iata'));
                        $cityCriteria->states[0] = array('value'=>array('nameEn'));
                        $cityCriteria->states[1] = array('value'=>array('iata','nameEn'));

                        $cityCriteria->states[1][0] = array('value'=>array('iata','metaphoneRu'));
                        $cityCriteria->states[1][1] = array('value'=>array('iata','nameEn','metaphoneRu'));

                        $cityCriteria->states[0][1] = array('value'=>array('nameEn','metaphoneRu'));
                        $cityCriteria->states[0][0] = array('value'=>array('metaphoneRu'));
                        $cityCriteria->paramUsed = $cityCriteria->states['value'];

                        $cityCriteria->paramValues = array('countryCode'=>$country->code,'nameEn'=>$city->localEn,'metaphoneRu'=>$metaphoneRu,'soundexEn'=>$sondexEn,'iata'=>$city->code);

                        $currCriteria = null;
                        $count = 0;
                        $findEnd = false;
                        while(!$findEnd)
                        {
                            //$prevCriteria = $currCriteria;
                            //$prevCount = $count;
                            $currCriteria = $cityCriteria->getCriteria();
                            $count =  GeoNames::model()->count($currCriteria);
                            //echo "Params: ".implode(',',$cityCriteria->paramUsed).' count:'.$count."\n";

                            if($count>1){
                                $findEnd = !$cityCriteria->setPlus();
                            }elseif($count<1){
                                $findEnd = !$cityCriteria->setMinus();
                            }else{
                                $findEnd = true;
                            }
                        }

                        if($count ==1)
                        {
                            $oneResult++;
                            $geoName = GeoNames::model()->find($currCriteria);
                            $needSave = false;
                            if(!$city->latitude)
                            {
                                $city->latitude = $geoName->latitude;
                                $city->longitude = $geoName->longitude;
                                $needSave = true;
                            }
                            if(!UtilsHelper::countRussianCharacters($city->localRu)){
                                $city->localRu = $geoName->nameRu;
                                $needSave = true;
                            }
                            if(!$city->metaphoneRu)
                            {
                                $city->metaphoneRu = $geoName->metaphoneRu;
                                $needSave = true;
                            }
                            if(!$city->stateCode)
                            {
                                $city->stateCode = $geoName->stateCode;
                                $needSave = true;
                            }
                            if($needSave){
                                $modifyCount++;
                                $city->save();
                            }

                        }
                        else
                        {
                            $noResult++;
                            fwrite($outfp, "{$city->localEn}|{$city->code}|{$city->id}|{$count}\n");
                        }

                        if(($lineCount % 30000) == 0)
                        {
                            Echo "NoResults:{$noResult} OneResult:{$oneResult}\n";
                        }

                    }
                }
                Echo "Total NoResults:{$noResult} OneResult:{$oneResult} ModifyCount: {$modifyCount} line count: {$lineCount}\n";
                fclose($outfp);
                //break;
            }
        }

        if($type == 'updateMetaphone')
        {

            $criteria=new CDbCriteria;
            $criteria->condition='hotelbookId IS NOT NULL';
            //$criteria->params=array(':postID'=>10);

            $countries = Country::model()->findAll($criteria);
            $start = false;
            foreach($countries as $country)
            {
                if(!$start)
                {
                    if($country->code == 'UA')
                    {
                        $start = true;
                    }else{
                        continue;
                    }
                }
                echo 'Update '.$country->code." metaphone\n";
                $criteria = new EMongoCriteria(array('conditions'=>array('countryCode'=>array('equals'=>$country->code)) ));
                //$criteria->limit(10);
                $geoNames = GeoNames::model()->findAll($criteria);
                $k=0;
                foreach($geoNames as $geoName){
                    $geoName->metaphoneRu = UtilsHelper::ruMetaphone($geoName->nameRu);
                    $geoName->save();
                    $k++;
                    if(($k % 30000) == 0)
                    {
                        echo "Updated $k lines\n";
                    }

                }
                echo "{$country->code} complete. Updated $k lines\n";
                //break;
            }
            //$criteria = new EMongoCriteria(array('conditions'=>array('countryCode'=>array('equals'=>$country),'metaphoneRu'=>array('equals'=>$metaphoneRu)) ));
            //VarDumper::dump(GeoNames::model()->find($criteria));
        }


    }
}
class CityFindCriteria
{
    public $paramValues;
    public $paramKeys;
    public $paramUsed;
    public $states;

    public function __construct()
    {
        $this->states = array('value'=>array('iata','nameEn'));
        $this->states[0] = array('value'=>array('nameEn'));
        $this->states[1] = array('value'=>array('iata','nameEn','metaphoneRu'));

        $this->states[0][1] = array('value'=>array('nameEn','metaphoneRu'));
        $this->states[0][0] = array('value'=>array('iata','metaphoneRu'));

        $this->states[0][0][1] = array('value'=>array('iata','metaphoneRu','soundexEn'));
        $this->states[0][0][0] = array('value'=>array('metaphoneRu'));
        $this->states[0][0][0][1] = array('value'=>array('metaphoneRu','soundexEn'));

        $this->states[0][0][1][0] = array('value'=>array('metaphoneRu','soundexEn'));

        $this->states[0][0][1][0][0] = array('value'=>array('metaphoneRu'));
        $this->paramKeys = array();
        $this->paramUsed = $this->states['value'];
    }

    public function setPlus()
    {
        $state = $this->states;
        if($this->paramKeys){
            foreach($this->paramKeys as $key){
                $state = $state[$key];
            }
        }
        if(isset($state[1])){
            $this->paramKeys[] = 1;
            $this->paramUsed = $state[1]['value'];
            return true;
        }else{
            return false;
        }
    }

    public function setMinus()
    {
        $state = $this->states;
        if($this->paramKeys){
            foreach($this->paramKeys as $key){
                $state = $state[$key];
            }
        }
        if(isset($state[0])){
            $this->paramKeys[] = 0;
            $this->paramUsed = $state[0]['value'];
            return true;
        }else{
            return false;
        }
    }

    public function getCriteria()
    {
        $used = $this->paramUsed;
        $used[] = 'countryCode';
        $conditions = array();
        foreach($used as $param){
            if($param == 'iata'){
                if(isset($this->paramValues['iata']))
                {
                    $conditions['iataCode'] = array('equals'=>$this->paramValues['iata']);
                }
                else
                {
                    $conditions['iataCode'] = array('type'=>2);
                }
            }else{
                $conditions[$param] = array('equals'=>$this->paramValues[$param]);
            }
        }
        $conditions['type'] = array('notEq'=>'AIRP');
        //array('conditions'=>array('iataCode'=>array('type'=>2)) );
        //print_r($conditions);
        return new EMongoCriteria(array('conditions'=>$conditions ));
    }
}