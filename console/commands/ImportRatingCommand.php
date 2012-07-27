<?php
/**
 * Command to insert/update hotels rating in database
 */
class ImportRatingCommand extends FileProcessingCommand
{
    public function getHelp()
    {
        return <<<EOD
USAGE ImportRating path/to/csv/from/spider.csv

EOD;
    }

    public function process($fh)
    {
       // skip und check header
        $line = trim(fgets($fh));
        if($line!="rating,name,locality,url,country,name_alt,address,lat,lng,postal,address_ext")
        {
            $this->logError("Wrong fingerprint \n");
            echo $this->getHelp();
            Yii::app()->end();
        }

        while(!feof($fh))
        {
            $data = fgetcsv($fh);
            if($data!==false)
            {
                $rating = $this->parseRating($data[0]);
                //! Use english for canonical names
                $name = ($data[5]=="")?$data[1]:$data[5]; //mihan007: just minor thing.. what is 5 and 1? don't like magic numbers.
                $cityName=$data[2];
                $city = City::model()->guess($cityName);
                if(count($city)==0) {
                    $this->logError("Problem mapping city " . $data[2]);
                    continue;
                }
                $city = $city[0];
                $canonicalName = UtilsHelper::canonizeHotelName($name, $city->localEn);
                $this->saveRow($city->id, $canonicalName, $rating, $name);
            }
        }
    }

    /**
     * Parse rating from crawler`s raw data
     *
     * @param string $rawRatingString "3.5 из 5"
     * @return float float representation of rating 0..5.0
     */
    public function parseRating($rawRatingString)
    {
        $matches = Array();
        if(!preg_match("~^\d+(\.\d+)?~", $rawRatingString, $matches)){
            $this->logError("Cant parse rating string: " .
                            $rawRatingString);
            return false;
        };
        return floatval($matches[0]);
    }

    public function saveRow($cityId, $canonicalName, $rating, $name)
    {
        $hr = new HotelRating();
        $hr->city_id = $cityId;
        $hr->canonical_name = $canonicalName;
        $hr->rating = $rating;
        try {
            $hr->save();
        } catch (CDbException $exc) {
            $this->logError("duplicated canonical_name '"
                            . $canonicalName . " | " . $name .
                            "'");
        }
    }


}
