<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 19.07.12
 * Time: 12:18
 * To change this template use File | Settings | File Templates.
 */
class OrderBookingController extends Controller
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
        //$dataProvider=new EMongoDocumentDataProvider('GeoNames',array('criteria'=>array('conditions'=>array('iataCode'=>array('type'=>2)) )));
        //$dataProvider=new EMongoDocumentDataProvider('GeoNames',array('criteria'=>array('conditions'=>array('iataCode'=>array('type'=>2)) )));
        $dataProvider=new EMongoDocumentDataProvider('GeoNames',array('criteria'=>array('conditions'=>array('countryCode'=>array('equals'=>'PR'),'iataCode'=>array('type'=>2)) )));
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
    }

    public function actionGetInfo($id)
    {
        $model = GdsRequest::model()->findByPk(new MongoID($id));
        $retArr = array();
        $widget = new CTextHighlighter();
        $widget->language = 'xml';
        $retArr['methodName'] = $model->methodName;
        $retArr['requestXml'] = $widget->highlight($model->requestXml);
        $retArr['responseXml'] = $widget->highlight($model->responseXml);
        $retArr['timestamp'] = date("Y-m-d H:i:s",$model->timestamp);
        $retArr['executionTime'] = Yii::app()->format->formatNumber($model->executionTime);
        $retArr['errorDescription'] = $model->errorDescription;

        //$retArr['responseXml'] = $model->responseXml;


        //echo $model->requestXml);
        echo json_encode($retArr);die();
    }



    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=GeoNames::model()->findByPk(new MongoID($id));

        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

}
