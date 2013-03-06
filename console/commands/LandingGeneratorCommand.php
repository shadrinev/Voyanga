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
        $sql = 'SELECT city.id, city.code, city.countryId, ctr.code AS countryCode
FROM  `city`
INNER JOIN  `country` AS ctr ON ctr.`id` =  `city`.`countryId`
WHERE  `countAirports` >0 AND city.hotelbookId >0';
        $outFilename = 'landing.xml';
        $fp = fopen($outFilename,'w');
        if(!$fp){
            echo 'Cant open file '.$outFilename;
            return '';
        }

        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        fwrite($fp,'<?xml version="1.0" encoding="utf-8"?>
<LandingUrls>');

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<LandingUrls>
<cityUrl countryCode="RU" cityCodeFrom="LED" cityCodeTo="MOW"><url>kgam</url></cityUrl>
</LandingUrls>';
        $mainSxe = simplexml_load_string($xml);
        $sxe = &$mainSxe->cityUrl;
        $citiesInfo = array();
        while(($row=$dataReader->read())!==false) {
            $citiesInfo[$row['id']] = array('cityCode'=>$row['code'],'countryCode'=>$row['countryCode'],);
        }
        foreach($citiesInfo as $cityFromKey=>$cityFromInfo){
            foreach($citiesInfo as $cityToKey=>$cityToInfo){
                if($cityFromKey != $cityToKey){
                    $url = 'https://voyanga.com/land/'.$cityToInfo['countryCode'].'/'.$cityFromInfo['cityCode'].'/'.$cityToInfo['cityCode'].'/';
                    $sxe->url = $url;
                    $sxe['countryCode'] = $cityToInfo['countryCode'];
                    $sxe['cityCodeFrom'] = $cityFromInfo['cityCode'];
                    $sxe['cityCodeTo'] = $cityToInfo['cityCode'];
                    fwrite($fp,$sxe->asXML()."\n");
                }
                //echo "xml:".$sxe->asXML();
                //break;
            }
            //break;
        }


        echo "xml:".$sxe->asXML();

        echo "Memory usageB: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
        echo "count:".count($citiesInfo);
        fwrite($fp,'</LandingUrls>');
        fclose($fp);

    }
}
