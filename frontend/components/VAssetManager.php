<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 05.10.12
 * Time: 11:40
 */
Yii::import('frontend.extensions.EScriptBoost.*');

class VAssetManager extends EAssetManagerBoost
{
    /**
     * Creating assets folder with human readable names
     * @param string $path
     * @return string
     */
    protected function hash($path)
    {
        $path2 = str_replace('\\', '/', $path) . '/';
        $parts = explode('/assets', $path2);
        $ptr = strrpos($parts[0], '/');
        echo '!!-';
        CVarDumper::dump($path);
        CVarDumper::dump($parts);
        echo '-!!';
        if (isset($parts[1]) and ($parts[1]!=''))
        {
            $path2 = str_replace('/', '_',
                substr($parts[0], $ptr + 1) . "_" . $parts[1]);
        }
        else
        {
            $path2 = str_replace('/', '_', substr($parts[0], $ptr + 1));
        }
        //we are adding hash to get unque pathname
        $path2 .= '_' . parent::hash($path);
        return $path2;
    }

    /**
     * Forces copy while debugging mode on
     * @param string $path
     * @param bool $hashByName
     * @param int $level
     * @param bool $forceCopy
     * @return string
     */
    public function publish($path, $hashByName = false, $level = -1, $forceCopy = false)
    {
        if (YII_DEBUG)
        {
            $forceCopy = true;
            $hashByName = true;
        }
        return parent::publish($path, $hashByName, $level, $forceCopy);
    }
}