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

    private $fullPath;

    public function init()
    {
        $this->totalCache = array();
    }

    public function run()
    {
        $timeStart = time();
        $this->syncFlightCache();
        $timeEnd = time();
        echo "Takes time: ".($timeEnd-$timeStart)." s.\n";
    }

    public function syncFlightCache()
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
        //echo "trying to get file from ".$url."\n";
        $result = file_get_contents($url);
        return $result;
    }

    public function batchInsert()
    {
        echo "Executing query start\n";
        /*$query = "ALTER TABLE `".FlightCache::model()->tableName()."` DISABLE KEYS";
        Yii::app()->db->createCommand($query)->execute();*/

        $query = "SELECT COUNT(*) FROM `".FlightCache::model()->tableName()."`";
        $before = Yii::app()->db->createCommand($query)->queryScalar();

        $query = "
            LOAD DATA INFILE '".$this->fullPath."'
            REPLACE
            INTO TABLE `".FlightCache::model()->tableName()."`
            FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n'";
        Yii::app()->db->createCommand($query)->execute();

        $query = "SELECT COUNT(*) FROM `".FlightCache::model()->tableName()."`";
        $after = Yii::app()->db->createCommand($query)->queryScalar();

        /*$query = "ALTER TABLE `".FlightCache::model()->tableName()."` ENABLE KEYS";
        Yii::app()->db->createCommand($query)->execute();*/
        echo "Executing query end\n";
        echo "Before: $before\n";
        echo "End: $after \n";
        echo "Inserted: ".($after-$before)."\n";
        $stat = Yii::app()->db->getStats();
        //echo "Speed: ".($counter/$stat[1])."\n";
        CVarDumper::dump($stat);
        echo "\n";
        unlink($this->fullPath);
        //echo "Total queries: ".$counter."\n";
    }

    public function merge()
    {
        echo "Batch insert incoming items\n";
        $dir = Yii::getPathOfAlias('application.runtime');
        $fileName = "query_".time().".batch";
        $this->fullPath = $dir.DIRECTORY_SEPARATOR.$fileName;
        $counter = 0;
        $file = fopen($this->fullPath, 'w');
        foreach ($this->totalCache as $cache)
        {
            $item = @unserialize($cache);
            if (!$item instanceof FlightCacheDump)
                continue;
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
                    $result[$hash]['time'] = $item->createdAt;
                    $flightCache->setAttributes($attr, false);
                    $part = $flightCache->buildRow();
                    fwrite($file, $part);
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
                $flightCache->setAttributes($attr, false);
                $part = $flightCache->buildRow();
                $counter++;
                fwrite($file, $part);
                unset($flightCache);
            }
        }
        fclose($file);
    }
}
