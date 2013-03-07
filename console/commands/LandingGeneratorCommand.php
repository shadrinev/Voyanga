<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 06.03.13
 * Time: 11:37
 * To change this template use File | Settings | File Templates.
 */


class LandingGeneratorCommand extends CConsoleCommand
{

    public function getHelp()
    {
        return <<<EOD
USAGE UpdateGeoip [OPTIONS]
   ...
Options:
--type=(value) - Default value airports
   ...
EOD;
    }

    /**
     * Execute the action.
     * @param array command line parameters specific for this command
     */
    public function actionIndex()
    {
        $connection=Yii::app()->db;
        $sql = 'SELECT city.id, city.code, city.countryId, ctr.code AS countryCode,caseGen,caseAcc
FROM  `city`
INNER JOIN  `country` AS ctr ON ctr.`id` =  `city`.`countryId`
WHERE  `countAirports` >0 AND city.hotelbookId >0';
        $outFilename = 'landingFromTo.xml';
        $fp = fopen($outFilename,'w');
        if(!$fp){
            echo 'Cant open file '.$outFilename;
            return '';
        }

        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        fwrite($fp,"id;codeFrom;codeTo;ctrCodeTo;url;desc;price\r\n");

        $citiesInfo = array();
        $ruCityIds = array();
        while(($row=$dataReader->read())!==false) {
            $citiesInfo[$row['id']] = array('cityCode'=>$row['code'],'countryCode'=>$row['countryCode'],'caseGen'=>$row['caseGen'],'caseAcc'=>$row['caseAcc']);
            if($row['countryCode'] == 'RU'){
                $ruCityIds[$row['id']] = $row['id'];
            }
        }

        $sql = 'SELECT `from`,`to`,`dateFrom`,`dateBack`,`priceBestPrice` from (SELECT * FROM `flight_cache` WHERE `dateFrom` > \'' . date('Y-m-d') . '\' AND `dateBack` > \'' . date('Y-m-d') . '\' ORDER BY priceBestPrice) as tbl1 GROUP BY `from`,`to` ORDER BY priceBestPrice';
        $bestPricesRt = array();
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        while(($row=$dataReader->read())!==false) {
            if(!isset($bestPricesRt[$row['to']])){
                $bestPricesRt[$row['to']] = array();
            }
            $bestPricesRt[$row['to']][$row['from']] = $row['priceBestPrice'];
        }

        $bestPricesOw = array();
        $sql = 'SELECT `from`,`to`,`dateFrom`,`dateBack`,`priceBestPrice` from (SELECT * FROM `flight_cache` WHERE `from` NOT IN ('.implode(',',$ruCityIds).') AND `to` IN ('.implode(',',$ruCityIds).') AND `dateFrom` > \'' . date('Y-m-d') . '\' AND `dateBack` = \'0000-00-00\' ORDER BY priceBestPrice) as tbl1 GROUP BY `from`,`to` ORDER BY priceBestPrice';
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        while(($row=$dataReader->read())!==false) {
            if(!isset($bestPricesOw[$row['to']])){
                $bestPricesOw[$row['to']] = array();
            }
            $bestPricesOw[$row['to']][$row['from']] = $row['priceBestPrice'];
        }

        $j = 0;
        foreach($citiesInfo as $cityFromKey=>$cityFromInfo){
            foreach($citiesInfo as $cityToKey=>$cityToInfo){
                if($cityFromKey != $cityToKey){
                    if(isset($ruCityIds[$cityFromKey]) || isset($ruCityIds[$cityToKey])){
                        $j++;
                        $minPrice = '';
                        $url = 'https://voyanga.com/land/'.$cityToInfo['countryCode'].'/'.$cityFromInfo['cityCode'].'/'.$cityToInfo['cityCode'].'/';
                        if(isset($ruCityIds[$cityToKey]) && !isset($ruCityIds[$cityFromKey])){
                            $url.='trip/OW';
                            if(isset($bestPricesOw[$cityToKey],$bestPricesOw[$cityToKey][$cityFromKey])){
                                $minPrice = $bestPricesOw[$cityToKey][$cityFromKey];
                            }
                        }else{
                            if(isset($bestPricesRt[$cityToKey],$bestPricesRt[$cityToKey][$cityFromKey])){
                                $minPrice = $bestPricesRt[$cityToKey][$cityFromKey];
                            }
                        }
                        $desc = "из {$cityFromInfo['caseGen']} в {$cityToInfo['caseAcc']}";
                        $str = "$j;{$cityFromInfo['cityCode']};{$cityToInfo['cityCode']};{$cityToInfo['countryCode']};{$url};{$desc};{$minPrice}\r\n";
                        fwrite($fp,$str);
                    }
                }
                //echo "xml:".$sxe->asXML();
                //break;
            }
            //break;
        }


        echo "RU:".implode(',',$ruCityIds)."\n";

        echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
        echo "count:".count($citiesInfo);
        fclose($fp);
        die();
        // -------------------------- Only to one City
        $outFilename = 'landingTo.xml';
        $fp = fopen($outFilename,'w');
        if(!$fp){
            echo 'Cant open file '.$outFilename;
            return '';
        }
        fwrite($fp,'<?xml version="1.0" encoding="utf-8"?>
<LandingUrls>');

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<LandingUrls>
<url country="RU" to="MOW" minPrice="" desc=""></url>
</LandingUrls>';
        $mainSxe = simplexml_load_string($xml);
        $sxe = &$mainSxe->url;
        foreach($citiesInfo as $cityToKey=>$cityToInfo){

            $minPrice = '';
            $url = 'https://voyanga.com/land/'.$cityToInfo['countryCode'].'/'.$cityToInfo['cityCode'].'/';
            if(isset($ruCityIds[$cityToKey])){

                if(isset($bestPricesOw[$cityToKey])){
                    foreach($bestPricesOw[$cityToKey] as $minPrice)
                        break;
                    //$minPrice = $bestPricesOw[$cityFromKey][$cityToKey];
                }
            }else{
                if(isset($bestPricesRt[$cityToKey])){
                    foreach($bestPricesRt[$cityToKey] as $minPrice)
                        break;
                    //$minPrice = $bestPricesRt[$cityFromKey][$cityToKey];
                }
            }
            $desc = "в {$cityToInfo['caseAcc']}";
            //$sxe['url'] = $url;
            $sxe['country'] = $cityToInfo['countryCode'];
            $sxe['to'] = $cityToInfo['cityCode'];
            $sxe['minPrice'] = $minPrice;
            $sxe['desc'] = $desc;
            $mainSxe->url = $url;
            fwrite($fp,$sxe->asXML()."\n");

        }

        fwrite($fp,'</LandingUrls>');
        fclose($fp);
        // -------------------------- Hotels in City
        $outFilename = 'landingHotelsInCity.xml';
        $fp = fopen($outFilename,'w');
        if(!$fp){
            echo 'Cant open file '.$outFilename;
            return '';
        }
        fwrite($fp,'<?xml version="1.0" encoding="utf-8"?>
<LandingUrls>');

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<LandingUrls>
<url country="RU" to="MOW" minPrice="" desc=""></url>
</LandingUrls>';
        $mainSxe = simplexml_load_string($xml);
        $sxe = &$mainSxe->url;
        foreach($citiesInfo as $cityToKey=>$cityToInfo){

            $minPrice = '';
            $url = 'https://voyanga.com/land/'.$cityToInfo['countryCode'].'/'.$cityToInfo['cityCode'].'/';
            if(isset($ruCityIds[$cityToKey])){

                if(isset($bestPricesOw[$cityToKey])){
                    foreach($bestPricesOw[$cityToKey] as $minPrice)
                        break;
                    //$minPrice = $bestPricesOw[$cityFromKey][$cityToKey];
                }
            }else{
                if(isset($bestPricesRt[$cityToKey])){
                    foreach($bestPricesRt[$cityToKey] as $minPrice)
                        break;
                    //$minPrice = $bestPricesRt[$cityFromKey][$cityToKey];
                }
            }
            $desc = "в {$cityToInfo['caseAcc']}";
            //$sxe['url'] = $url;
            $sxe['country'] = $cityToInfo['countryCode'];
            $sxe['to'] = $cityToInfo['cityCode'];
            $sxe['minPrice'] = $minPrice;
            $sxe['desc'] = $desc;
            $mainSxe->url = $url;
            fwrite($fp,$sxe->asXML()."\n");

        }

        fwrite($fp,'</LandingUrls>');
        fclose($fp);





    }
}
