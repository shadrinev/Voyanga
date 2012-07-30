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
    public function saveTaskInfo($taskName,$id)
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
        $cronTask->taskId = $id;
        $res = $cronTask->save();
        if(!$res){
            VarDumper::dump($cronTask->getErrors());
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

    public function getTaskByName($taskName)
    {
        Yii::import('site.common.components.cron.*');
        /** @var CActiveRecord $owner  */
        $owner = $this->getOwner();
        $modelName = get_class($owner);
        return CronTask::model()->findByAttributes(array('ownerModel'=>$modelName,'ownerId'=>$owner->primaryKey,'taskName'=>$taskName));
    }
}
