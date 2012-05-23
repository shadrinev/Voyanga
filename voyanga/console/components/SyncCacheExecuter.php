<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 16:46
 */
class SyncCacheExecuter extends Component
{
    public $maxQuerySize = 10240; //10kb

    public $maxFilesPerOnce = 100;

    public $frontends = array();

    private $totalCache;

    public function init()
    {
        $this->totalCache = array();
    }

    public function run()
    {
        $this->syncFlightCache();
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

    public function merge()
    {
        $result = array();
        echo "merging incoming items\n";
        foreach ($this->totalCache as $cache)
        {
            /**
             * @var $item FlightCacheDump
             */
            $item = unserialize($cache);
            if (!$item instanceof FlightCacheDump)
                continue;
            $hash = $item->from.'_'.$item->to.'_'.$item->dateFrom.'_'.$item->dateBack;
            $flag = isset($result[$hash]);
            if ($flag)
            {
                if ($item->createdAt > $result[$hash]['time'])
                {
                    $result[$hash]['time'] = $item->createdAt;
                    $result[$hash]['attr'] = $item->attributes;
                }
            }
            else
            {
                $result[$hash]['time'] = $item->createdAt;
                $result[$hash]['attr'] = $item->attributes;
            }
        }
        $this->totalCache = $result;
        $n = sizeof($this->totalCache);
        echo "Unique $n incoming items\n";
    }

    public function batchInsert()
    {
        echo "Batch insert incoming items\n";
        $query = '';
        $totalSize = 0;
        foreach ($this->totalCache as $hash=>$value)
        {
            $attr = unserialize($value['attr']);
            $flightCache = new FlightCache;
            $flightCache->setAttributes($attr, false);
            $part = $flightCache->buildQuery()."\n";
            $totalSize += strlen($part);
            if ($totalSize>$this->maxQuerySize)
            {
                Yii::app()->db->createCommand($query)->execute();
                $totalSize = 0;
                $query = '';
            }
            else
            {
                $query .= $part;
            }
            unset($flightCache);
        }
        if ($totalSize>0)
            Yii::app()->db->createCommand($query)->execute();
    }
}
