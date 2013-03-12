<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 03.09.12
 * Time: 12:11
 * To change this template use File | Settings | File Templates.
 */
class PartnerManageController extends ABaseAdminController
{
    /**
     * Lists all models.
     */
    public function actionIndex()
    {

        /*$nPartner = new Partner();
        $nPartner->name = 'MegaPartner';


        $nPartner->requiresNewPassword = 0;
        $nPartner->passwordStrategy = 'ABcryptPassword';
        //$nPartner->generateSalt();

        $nPartner->password = '123456';*/

        //if(!$nPartner->save()){
        //    VarDumper::dump($nPartner->getErrors());
        //}
        $selectCriteria = new CDbCriteria();
        $dataProvider=new CActiveDataProvider(
            'Partner',
            array(
                'criteria'=>$selectCriteria,
                'pagination'=>array(
                    'pageSize'=>40,
                )
            )
        );
        $this->render('index',array(
            'dataProvider'=>$dataProvider,

        ));
    }

    public function actionDelete($id){
        $partner = Partner::model()->findByPk($id);
        if($partner){
            //$criteria = new CDbCriteria();
            //$criteria->addCondition('roomNameRusId = :rnri');
            //$criteria->params = array(':rnri'=>$id);
            //RoomNamesNemo::model()->updateAll(array('roomNameRusId'=>null),$criteria);
            //Delete all relations
            $partner->delete();
            $this->redirect('/admin/partners/partnerManage/');
        }else{
            echo "Элемента не найдено!";
        }
    }

    public function actionEdit($id = ''){
        $modelClass = 'Partner';
        if(!$id){
            $model = new Partner();
            $isCreated = true;
        }else{
            $model = $this->loadModel($id);
            $isCreated = false;
        }
        if($model){
            $model->requiresNewPassword = 0;
            $model->passwordStrategy = 'ABcryptPassword';
        }
        $this->performAjaxValidation($model);

        if (isset($_POST[$modelClass]))
        {
            $newPasswordIs = '';
            if(isset($_POST['genPass']) && $_POST['genPass'] == '1'){
                $_POST[$modelClass]['password'] = $model->generatePassword();
            }
            if(isset($_POST[$modelClass]['password'])){
                if($_POST[$modelClass]['password'] == ''){
                    unset($_POST[$modelClass]['password']);
                }else{
                    $newPasswordIs = ' Новый пароль: '.$_POST[$modelClass]['password'];
                }
            }
            $model->attributes = $_POST[$modelClass];
            if ($model->save())
            {
                Yii::app()->user->setFlash('success', 'Данные о партнере '.($isCreated ? 'созданы' : 'обновлены').$newPasswordIs);
                $this->redirect(array('edit','id'=>$model->id));
            }
        }
        $model->password = ""; // we don't pass the password to the view
        $this->render('_edit', array(
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
        $model=Partner::model()->findByPk($id);

        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-form')
        {
            echo CActiveForm::validate($model);
            echo 'ppp'.$model->password;
            Yii::app()->end();
        }
    }
}
