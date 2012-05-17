<?php

class EventController extends Controller
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
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Event;
        $eventCategory = new EventCategory;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$this->createOrUpdate($model);

		$this->render('create',array(
			'model'=>$model,
            'attribute'=>'categories'
		));
	}

    protected function createOrUpdate($model)
    {
        if(isset($_POST['Event']))
        {
            $model->attributes=$_POST['Event'];
            $categories = EventCategory::model()->findAllByPk($_POST['Event']['categories']);
            $model->categories = $categories;
            if($model->save())
            {
                if ($pictureSmall=CUploadedFile::getInstance($model, 'pictureSmall'))
                    $model->pictureSmall = $pictureSmall;
                if ($pictureBig=CUploadedFile::getInstance($model, 'pictureBig'))
                    $model->pictureBig = $pictureBig;
                $model->setTags($_POST['Event']['tagsString'])->save();
                $this->redirect(array('view','id'=>$model->id));
            }
        }
    }

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

        $this->createOrUpdate($model);

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Event');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Event('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Event']))
			$model->attributes=$_GET['Event'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Event::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='event-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    public function actionUploadToGallery($id, $qqfile)
    {
        $name = $qqfile;
        $tempName= md5(time());
        $dir = Yii::getPathOfAlias('application.runtime');
        $fullTempName = $dir . DIRECTORY_SEPARATOR . $tempName;
        $file = fopen($fullTempName,'w');
        fwrite($file, $GLOBALS['HTTP_RAW_POST_DATA']);
        fclose($file);
        $type=mime_content_type($fullTempName);
        $size=filesize($fullTempName);
        $error=UPLOAD_ERR_OK;
        $uploadedFile = new CUploadedFile($name, $fullTempName, $type, $size, $error);
        $model = $this->loadModel($id);
        $model->pictures = $uploadedFile;
        if ($model->save()){
            $result = array('success'=>true,'filename'=>$uploadedFile->name);
        } else {
            $result = array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        $result=htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        echo $result;
    }
}
