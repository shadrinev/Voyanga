<?php

class SiteController extends Controller
{
    /**
     * Declares class-based actions.
     */
    public function actions()
    {

        return array(
            'cityAutocomplete'=>array(
                'class'=>'site.frontend.actions.AAutoCompleteAction',
                'modelClass'=>'City',
                'cache'=>true,
                'cacheExpire'=>1800,
                'attributes'=>array('localRu','localEn','code:='),
                'labelTemplate'=>'{localRu}, {country.localRu}, {code}',
                'valueTemplate'=>'{localRu}',
                'criteria'=>array('with'=>'country'),
                'paramName' => 'query'
            ));
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $this->render('index');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error)
        {
            if (Yii::app()->request->isAjaxRequest) echo $error['message'];
            else $this->render('error', $error);
        }
    }

    public function actionTest()
    {
        Yii::app()->observer->notify('onBeforeFlightSearch', $user);
        Yii::app()->observer->notify('onAfterFlightSearch', $this);
    }
}