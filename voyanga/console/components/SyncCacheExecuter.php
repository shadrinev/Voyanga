<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 16:46
 */
class SyncCacheExecuter extends Component
{
    public $frontends;

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
            //todo: change to while
            if($cache = $this->getCacheFile($url))
            {
                $cache = explode('##',$cache);
                $this->totalCache = array_merge($this->totalCache, $cache);
                unset($cache);
            }
        }
        if ($n=sizeof($this->totalCache)>0)
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
        echo "trying to get file from ".$url."\n";
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
    }

    public function batchInsert()
    {
        echo "batch insert incoming items\n";
        foreach ($this->totalCache as $hash=>$value)
        {
            $attr = $value['attr'];
            CVarDumper::dump($hash);
            echo ":";
            CVarDumper::dump($value['time']);
            echo "\n";
        }
    }
}
