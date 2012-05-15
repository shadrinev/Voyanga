<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 15.05.12
 * Time: 12:22
 */

class EventCategoryController extends ABaseAdminController
{
    public $defaultAction = 'admin';

    public $CQtreeGreedView = array(
        'modelClassName' => 'EventCategory', //название класса
        'adminAction' => 'admin' //action, где выводится QTreeGridView. Сюда будет идти редирект с других действий.
    );

    public function actions()
    {
        return array(
            'create' => 'ext.QTreeGridView.actions.Create',
            'update' => 'ext.QTreeGridView.actions.Update',
            'delete' => 'ext.QTreeGridView.actions.Delete',
            'moveNode' => 'ext.QTreeGridView.actions.MoveNode',
            'makeRoot' => 'ext.QTreeGridView.actions.MakeRoot',
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

     /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('EventCategory');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new EventCategory('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['EventCategory']))
            $model->attributes = $_GET['EventCategory'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = EventCategory::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'event-category-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}