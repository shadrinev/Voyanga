<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 16:42
 */
class SyncCacheCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        $executor = Yii::app()->syncCacheExecuter;
        $executor->run();
    }
}
