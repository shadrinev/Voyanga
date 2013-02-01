<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 05.10.12
 * Time: 11:40
 */

class VAssetManager extends CAssetManager
{
    /**
     * Creating assets folder with human readable names
     * @param string $path
     * @return string
     */
    protected function hash($path)
    {
        $path2 = str_replace('\\', '/', $path);
        $parts = explode('/assets', $path2);
        if (sizeof($parts)==1)
        {
            $parts = explode('/', $path2);
            $path2 = end($parts);
        }
        else
        {
            $end = end($parts);
            if (is_numeric($end))
                $end = $parts[0];
            $parts = explode('/', $end);
            $path2 = end($parts);
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
            $hashByName = false;
        }
        return parent::publish($path, $hashByName, $level, $forceCopy);
    }
}