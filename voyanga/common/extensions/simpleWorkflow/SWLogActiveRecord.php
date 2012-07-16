<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 16.07.12
 * Time: 12:37
 * To change this template use File | Settings | File Templates.
 */
class SWLogActiveRecord extends SWActiveRecord
{
    public function beforeTransition($event)
    {
        //VarDumper::dump($event);
        $transition = array();
        $transition['type'] = 'before';
        $transition['modelName'] = get_class($event->sender);
        $transition['modelId'] = $event->sender->primaryKey;
        $transition['stateFrom'] = $event->source->getId();
        $transition['stateTo'] = $event->destination->getId();
        $transition['time'] = date('Y-m-d H:i:s');
        VarDumper::dump($transition);
        WorkflowStates::setTransition($transition);
        return parent::beforeTransition($event);
    }

    public function afterTransition($event)
    {
        $transition = array();
        $transition['type'] = 'after';
        $transition['modelName'] = get_class($event->sender);
        $transition['modelId'] = $event->sender->primaryKey;
        $transition['stateFrom'] = $event->source->getId();
        $transition['stateTo'] = $event->destination->getId();
        $transition['time'] = date('Y-m-d H:i:s');
        VarDumper::dump($transition);
        WorkflowStates::setTransition($transition);
        return parent::beforeTransition($event);
    }

}
