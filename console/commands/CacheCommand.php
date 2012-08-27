<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 16:42
 */
class CacheCommand extends CConsoleCommand
{
    public function actionSync()
    {
        $executor = Yii::app()->syncCacheExecuter;
        $executor->run();
    }

    public function actionClean()
    {
        $query = "DELETE FROM ".FlightCache::model()->tableName()." WHERE `dateFrom` < NOW();";
        $deleted = Yii::app()->db->createCommand($query)->execute();
        echo "Deleted flight rows: ".$deleted."\n";

        $query = "DELETE FROM ".HotelCache::model()->tableName()." WHERE `dateFrom` < NOW();";
        $deleted = Yii::app()->db->createCommand($query)->execute();
        echo "Deleted hotel rows: ".$deleted."\n";
    }
}
