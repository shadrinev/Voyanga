<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 09.07.12
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */
class CronTaskBehavior extends CBehavior
{
    public function saveTaskInfo($taskName,$addInfo)
    {
        Yii::import('site.common.components.cron.*');
        /** @var CActiveRecord $owner  */
        $owner = $this->getOwner();
        $modelName = get_class($owner);
        $cronTask = CronTask::model()->findByAttributes(array('ownerModel'=>$modelName,'ownerId'=>$owner->primaryKey,'taskName'=>$taskName));
        if(!$cronTask)
        {
            $cronTask = new CronTask();
            $cronTask->ownerModel = $modelName;
            $cronTask->ownerId = $owner->primaryKey;
            $cronTask->taskName = $taskName;
        }
        else
        {
            Yii::app()->cron->delete($cronTask->taskId);
        }
        $cronTask->taskId = $addInfo['atId'];
        $cronTask->uniqKey = $addInfo['uniqKey'];
        $res = $cronTask->save();
        if(!$res)
        {
            Yii::log($cronTask->getErrors(), CLogger::LEVEL_ERROR);
        }
    }

    public function getAllTasks()
    {
        Yii::import('site.common.components.cron.*');
        /** @var CActiveRecord $owner  */
        $owner = $this->getOwner();
        $modelName = get_class($owner);
        return CronTask::model()->findAllByAttributes(array('ownerModel'=>$modelName,'ownerId'=>$owner->primaryKey));
    }

    public function getTaskInfo($taskName) {
        Yii::import('site.common.components.cron.*');
        /** @var CActiveRecord $owner  */
        $owner = $this->getOwner();
        $modelName = get_class($owner);
        return CronTask::model()->findByAttributes(array('ownerModel'=>$modelName,'ownerId'=>$owner->primaryKey,'taskName'=>$taskName));
    }

    public function getTaskByName($taskName)
    {
        trigger_error("CronTaskBehavior getTaskByName is deprecated.", E_USER_NOTICE);
        return $this->getTaskInfo($taskName);
    }
}
