<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 16.07.12
 * Time: 15:19
 * To change this template use File | Settings | File Templates.
 */
class WorkflowStates extends EMongoDocument // Notice: We extend EMongoDocument class instead of CActiveRecord
{
    public $className;
    public $objectId;
    public $lastState;
    public $updated;
    public $transitions;

    public function getCollectionName()
    {
        return 'workflow_states';
    }

    // We can define rules for fields, just like in normal CModel/CActiveRecord classes
    public function rules()
    {
        return array(
            array('className,objectId', 'required'),
            array('lastState,updated,transitions', 'safe'),
            //array('requestNum', 'numeric', 'integerOnly' => true),
        );
    }


    /*// the same with attribute names
    public function attributeNames()
    {
        return array(
            'className' => 'Class Name',
            'objectId'=>'Object id',
            'lastState'=>'Last State',
            'updated'=> 'Updated Time',
            'transitions'=>'History States'
        );
    }*/

    public static function setTransition($transitionInfo)
    {
        $criteria = new EMongoCriteria(array('conditions'=>array('className'=>array('equals'=>$transitionInfo['modelName']),'objectId'=>array('equals'=>$transitionInfo['modelId']) ) ));
        /** @var WorkflowStates $wfStates  */
        //echo WorkflowStates::model()->count($criteria);
        $wfStates = WorkflowStates::model()->find($criteria);

        if($wfStates)
        {
            //VarDumper::dump($wfStates);die();
            if($transitionInfo['type'] == 'after')
            {
                $wfStates->lastState = $transitionInfo['stateTo'];
            }
        }
        else
        {
            $wfStates = new self();
            $wfStates->className = $transitionInfo['modelName'];
            $wfStates->objectId = $transitionInfo['modelId'];
            $wfStates->transitions = array();
        }

        $wfStates->transitions[] = $transitionInfo;
        $wfStates->updated = time();
        $wfStates->save();
    }

    public function getDescription()
    {
        return 'my desc';
    }

    /**
     * This method have to be defined in every model, like with normal CActiveRecord
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
