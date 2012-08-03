<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 30.05.12
 * Time: 14:55
 * To change this template use File | Settings | File Templates.
 */
class WorkflowStatesController extends Controller
{
    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view',array(
            'model'=>$this->loadModel($id),
        ));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider=new EMongoDocumentDataProvider('WorkflowStates',array('sort'=>array('defaultOrder'=>'updated desc')));
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
    }

    public function actionGetInfo($id)
    {
        $wfStates = WorkflowStates::model()->findByPk(new MongoID($id));
        Yii::import('site.common.components.hotelBooker.*');
        //Yii::import('hotelBooker.models.*');
        //Yii::import('hotelBooker.*');
        Yii::import('site.common.modules.hotel.models.*');

        $modelObject = SWLogActiveRecord::model($wfStates->className)->findByPk($wfStates->objectId);

        //VarDumper::dump($modelObject);
        $desc = '';
        $stages = array();
        $lastStageName = '';
        $lastStageId = 0;
        foreach($wfStates->transitions as $i=>$transition)
        {
            if($transition['type'] == 'before')
            {
                if(count($stages) == 0)
                {
                    $stages[] = array('stageName'=>$transition['stateFrom'],'requestIds'=>array());
                }
                $n = count($stages);

                if(isset($transition['requestIds'])){
                    if(count($transition['requestIds']) > 0){
                        $stages[$n - 1]['requestIds'] = $transition['requestIds'];
                        foreach($stages[$n - 1]['requestIds'] as $j=>$reqId){
                            $requestModel = EMongoDocument::model($reqId['class'])->findByAttributes(array($reqId['keyName']=>$reqId['key']));
                            $stages[$n - 1]['requestIds'][$j]['methodName'] = $requestModel->methodName;
                            $stages[$n - 1]['requestIds'][$j]['description'] = $requestModel->requestDescription;
                        }
                    }
                }
                $stages[] = array('stageName'=>$transition['stateTo'],'requestIds'=>array(),'time'=>$transition['time']);
            }
            elseif($transition['type'] == 'afterSave')
            {
                if(count($stages) == 0)
                {
                    $stages[] = array('stageName'=>$transition['state'],'requestIds'=>$transition['requestIds']);
                }
                $n = count($stages);

                if(isset($transition['requestIds'])){
                    if(count($transition['requestIds']) > 0){
                        $stages[$n - 1]['requestIds'] = $transition['requestIds'];
                        foreach($stages[$n - 1]['requestIds'] as $j=>$reqId){
                            $requestModel = EMongoDocument::model($reqId['class'])->findByAttributes(array($reqId['keyName']=>$reqId['key']));
                            $stages[$n - 1]['requestIds'][$j]['methodName'] = $requestModel->methodName;
                            $stages[$n - 1]['requestIds'][$j]['description'] = $requestModel->requestDescription;
                        }
                    }
                }
                //$stages[] = array('stageName'=>$transition['stateTo'],'requestIds'=>array(),'time'=>$transition['time']);
            }
        }

        //VarDumper::dump($stages);
        $retArr = array('stages'=>$stages,'class'=>$wfStates->className,'objectId'=>$wfStates->objectId);

        /*$widget = new CTextHighlighter();
        $widget->language = 'xml';
        $retArr['methodName'] = $model->methodName;
        $retArr['requestXml'] = $widget->highlight($model->requestXml);
        $retArr['responseXml'] = $widget->highlight($model->responseXml);
        $retArr['timestamp'] = date("Y-m-d H:i:s",$model->timestamp);
        $retArr['executionTime'] = Yii::app()->format->formatNumber($model->executionTime);
        $retArr['errorDescription'] = $model->errorDescription;*/

        //$retArr['responseXml'] = $model->responseXml;


        //echo $model->requestXml);
        echo json_encode($retArr);die();
    }

    public function actionGetRequestInfo($className,$keyName,$key)
    {
        $requestModel = EMongoDocument::model($className)->findByAttributes(array($keyName=>$key));
        $retArr = array();
        $widget = new CTextHighlighter();
        $widget->language = 'xml';
        $retArr['methodName'] = $requestModel->methodName;
        $retArr['requestXml'] = $widget->highlight($requestModel->requestXml);
        $retArr['responseXml'] = $widget->highlight($requestModel->responseXml);
        //$retArr['requestUrl'] = $requestModel->requestUrl;
        $retArr['timestamp'] = date("Y-m-d H:i:s",$requestModel->timestamp);
        $retArr['executionTime'] = Yii::app()->format->formatNumber($requestModel->executionTime);
        $retArr['errorDescription'] = $requestModel->errorDescription;

        echo json_encode($retArr);die();
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=WorkflowStates::model()->findByPk(new MongoID($id));

        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }



}
