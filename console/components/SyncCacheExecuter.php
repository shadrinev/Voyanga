<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 16:46
 */
class SyncCacheExecuter extends Component
{
    public $maxQuerySize = 4000000; //10kb

    public $maxFilesPerOnce = 200;

    public $frontends = array();

    private $totalCache;

    private $fullHotelPath;
    private $fullFlightPath;

    public function init()
    {
        $this->totalCache = array();
    }

    public function run()
    {
        $timeStart = time();
        $this->syncCache();
        $timeEnd = time();
        echo "Takes time: ".($timeEnd-$timeStart)." s.\n";
    }

    public function syncCache()
    {
        foreach ($this->frontends as $frontend)
        {
            $url = $this->buildUrl($frontend);
            $counter = 0;
            //todo: change to while
            while($cache = $this->getCacheFile($url) and ($counter<$this->maxFilesPerOnce))
            {
                $counter++;
                if ($counter%50==0)
                    echo "End getting $counter chunk of incoming items\n";
                $cache = explode('##',$cache);
                $this->totalCache = array_merge($this->totalCache, $cache);
                unset($cache);
            }
        }
        $n=sizeof($this->totalCache);
        if ($n>0)
        {
            echo "Syncing with $n incoming items\n";
            $this->merge();
            unset($this->totalCache);
            $this->batchInsert();
        }
        else
        {
            echo "Nothing to sync\n";
        }
    }

    public function buildUrl($frontend)
    {
        $url = $frontend['url'];
        $key = $frontend['key'];
        $fullUrl = $url . "?" . http_build_query(array('key'=>$key));
        return $fullUrl;
    }

    public function getCacheFile($url)
    {
        echo "trying to get file from ".$url."\n";
        $result = file_get_contents_curl($url);
        return $result;
    }

    public function batchInsert()
    {
        echo "Executing query start\n";

        $query = "SELECT COUNT(*) FROM `".FlightCache::model()->tableName()."`";
        $beforeFlights = Yii::app()->db->createCommand($query)->queryScalar();

        $query = "SELECT COUNT(*) FROM `".HotelCache::model()->tableName()."`";
        $beforeHotels = Yii::app()->db->createCommand($query)->queryScalar();

        $queryFlight = "
            LOAD DATA INFILE '".$this->fullFlightPath."'
            REPLACE
            INTO TABLE `".FlightCache::model()->tableName()."`
            FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n'";
        Yii::app()->db->createCommand($queryFlight)->execute();

        $queryHotel = "
            LOAD DATA INFILE '".$this->fullHotelPath."'
            REPLACE
            INTO TABLE `".HotelCache::model()->tableName()."`
            FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n'";
        Yii::app()->db->createCommand($queryHotel)->execute();

        $query = "SELECT COUNT(*) FROM `".FlightCache::model()->tableName()."`";
        $afterFlights = Yii::app()->db->createCommand($query)->queryScalar();
        $query = "SELECT COUNT(*) FROM `".HotelCache::model()->tableName()."`";
        $afterHotels = Yii::app()->db->createCommand($query)->queryScalar();

        echo "Executing queries end\n\n";
        echo "Before flights: $beforeFlights\n";
        echo "After flights: $afterFlights \n";
        echo "Inserted flights: ".($afterFlights-$beforeFlights)."\n\n";

        echo "Before hotels: $beforeHotels\n";
        echo "After hotels: $afterHotels \n";
        echo "Inserted hotels: ".($afterHotels-$beforeHotels)."\n\n";

        $stat = Yii::app()->db->getStats();
        unlink($this->fullFlightPath);
        unlink($this->fullHotelPath);
    }

    public function merge()
    {
        echo "Batch insert incoming items\n";
        $dir = Yii::getPathOfAlias('application.runtime');
        $fileFlightName = "query_flight_".time().".batch";
        $fileHotelName = "query_hotel_".time().".batch";
        $this->fullFlightPath = $dir.DIRECTORY_SEPARATOR.$fileFlightName;
        $this->fullHotelPath = $dir.DIRECTORY_SEPARATOR.$fileHotelName;
        $counter = 0;
        $fileFlight = fopen($this->fullFlightPath, 'w');
        $fileHotel = fopen($this->fullHotelPath, 'w');
        foreach ($this->totalCache as $cache)
        {
            $item = @unserialize($cache);
            if ((!$item instanceof FlightCacheDump) and (!$item instanceof HotelCacheDump))
                continue;
            if ($item instanceof FlightCacheDump)
            {
                $hash = $item->from.'_'.$item->to.'_'.$item->dateFrom.'_'.$item->dateBack;
                $flag = isset($result[$hash]);
                if ($flag)
                {
                    if ($item->createdAt > $result[$hash]['time'])
                    {
                        $attr = @unserialize($item->attributes);
                        if (!is_array($attr))
                            continue;
                        $flightCache = new FlightCache;
                        $flightCache->scenario = 'restore';
                        $result[$hash]['time'] = $item->createdAt;
                        $attr['updatedAt'] = date('Y-m-d H:i:s', $item->createdAt);
                        $flightCache->setAttributes($attr, false);
                        $part = $flightCache->buildRow();
                        fwrite($fileFlight, $part);
                        unset($flightCache);
                    }
                }
                else
                {
                    $attr = @unserialize($item->attributes);
                    if (!is_array($attr))
                        continue;
                    $result[$hash]['time'] = $item->createdAt;
                    $flightCache = new FlightCache;
                    $attr['updatedAt'] = date('Y-m-d H:i:s', $item->createdAt);
                    $flightCache->setAttributes($attr, false);
                    $part = $flightCache->buildRow();
                    $counter++;
                    fwrite($fileFlight, $part);
                    unset($flightCache);
                }
            }
            elseif ($item instanceof HotelCacheDump)
            {
                $hash = $item->cityId.'_'.$item->dateFrom.'_'.$item->dateTo.'_'.$item->stars;
                $flag = isset($result[$hash]);
                if ($flag)
                {
                    if ($item->createdAt > $result[$hash]['time'])
                    {
                        $attr = @unserialize($item->attributes);
                        if (!is_array($attr))
                            continue;
                        $hotelCache = new HotelCache;
                        $result[$hash]['time'] = $item->createdAt;
                        $attr['updatedAt'] = date('Y-m-d H:i:s', $item->createdAt);
                        $hotelCache->setAttributes($attr, false);
                        $part = $hotelCache->buildRow();
                        fwrite($fileHotel, $part);
                        unset($hotelCache);
                    }
                }
                else
                {
                    $attr = @unserialize($item->attributes);
                    if (!is_array($attr))
                        continue;
                    $result[$hash]['time'] = $item->createdAt;
                    $attr['updatedAt'] = date('Y-m-d H:i:s', $item->createdAt);
                    $hotelCache = new HotelCache;
                    $hotelCache->setAttributes($attr, false);
                    $part = $hotelCache->buildRow();
                    $counter++;
                    fwrite($fileHotel, $part);
                    unset($hotelCache);
                }
            }
        }
        fclose($fileFlight);
        fclose($fileHotel);
    }
}
