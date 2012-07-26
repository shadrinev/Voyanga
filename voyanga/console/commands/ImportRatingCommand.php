<?php
/**
 * Command to insert/update hotels rating in database
 */
class ImportRatingCommand extends CConsoleCommand
{
    public function getHelp()
    {
        return <<<EOD
USAGE ImportRating path/to/csv/from/spider.csv

EOD;
    }

    public function run($args)
    {
        if (count($args)==0)
        {
            echo $this->getHelp();
            exit; //mihan007: better to use Yii::app()->end() to end execution. It'll write all logs, executes any afterRequest events handlers and other useful stuff
        }

        if (!is_readable($args[0]))
        {
            echo $this->getHelp(); //mihan007: maybe should show error or warning here?
            exit;
        }

        Yii::import("common.modules.hotel.models.HotelRating");

        // Precache city name => city id mapping
        $cityname_to_city = Array(); //mihan007: our code conventions says that we are using camelCaseVariables mainly because of framework internal code style
        $cities = City::model()->findAll(); //mihan007: do you really need all columns here? maybe City::model()->findAll(array('select'=>'id, localRu, localEn')) only?
        foreach ($cities as $city)
        {
            $cityname_to_city[$city->localRu] = $city;
        }

        $fh = fopen($args[0], 'r');

        if($fh===FALSE)
        {
            echo $this->getHelp(); //mihan007: correct error message should be here
            exit;
        }
        // skip und check header
        $line = trim(fgets($fh));
        if($line!="rating,name,locality,url,country,name_alt,address,lat,lng,postal,address_ext")
        {
            echo "Wrong fingerprint \n"; //mihan007: ok. but why not executing $this->logError?
            echo $this->getHelp();
            exit;
        }

        while(!feof($fh))
        {
            $data = fgetcsv($fh);
            if($data!==false)
            {
                $rating = $this->parseRating($data[0]);
                //! Use english for canonical names
                $name = ($data[5]=="")?$data[1]:$data[5]; //mihan007: just minor thing.. what is 5 and 1? don't like magic numbers.
                $city = $cityname_to_city[$data[2]];
                if(!$city) {
                    $this->logError("Problem mapping city " . $data[2]);
                }
                $canonical_name = UtilsHelper::canonizeHotelName($name, $city->localEn); //mihan007: can't find such function. Did you commit that file?
                $this->saveRow($city->id, $canonical_name, $rating, $name);
            }
        }
    }

    /**
     * Parse rating from crawler`s raw data
     *
     * @param string $raw_rating_string "3.5 из 5"
     * @return float float representation of rating 0..5.0
     */
    public function parseRating($raw_rating_string)
    {
        $matches = Array();
        if(!preg_match("~^\d+(\.\d+)?~", $raw_rating_string, $matches)){
            $this->logError("Cant parse rating string: " .
                            $raw_rating_string);
            return false;
        };
        return floatval($matches[0]);
    }



    public function saveRow($city_id, $canonical_name, $rating, $name)
    {
        $hr = new HotelRating();
        $hr->city_id = $city_id;
        $hr->canonical_name = $canonical_name;
        $hr->rating = $rating;
        try {
            $hr->save();
        } catch (CDbException $exc) {
            $this->logError("duplicated canonical_name '"
                            . $canonical_name . " | " . $name .
                            "'");

        }
    }

    public function logError($msg)
    {
        //! FIXME there should be built in stuff for that
        // if not we should implement it as behavior(?)

        //mihan007: usually I use Yii::log($msg, 'error', $category) for this things.
        //and of course it'll be useful to implement something like class StdOutRoute for such things
        //(to have ability to print errors to stdout)
        //more details = http://www.yiiframework.com/forum/index.php/topic/30484-yii-log-to-console-stdout/
        print "ERROR: " . $msg . "\n";
    }
}
