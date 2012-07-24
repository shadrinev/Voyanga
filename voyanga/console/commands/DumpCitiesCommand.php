<?php
/**
 * Command dumps city names with present hotelbookid to stdout
 */
class DumpCitiesCommand extends CConsoleCommand
{
    public function run($args)
    {
        $cities = City::model()->findAll("hotelbookid IS NOT NULL");
        foreach($cities as $city)
        {
            echo $city->localRu;
            echo "\n";
         }
    }
}
