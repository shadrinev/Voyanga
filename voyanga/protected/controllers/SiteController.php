<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
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
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	
	public function actionTest(){
		//$country = new Country();
		
		$aParams = array(
			'adult_count'=>5,
			'child_count'=>2,
			'infant_count'=>0,
			'flight_class'=>'E',
			'flights'=>array(
				array(
					'departure_city_id'=>5185,
					'arrival_city_id'=>4466,
					'departure_date'=>'21.05.2012',
				),
				array(
					'departure_city_id'=>5185,
					'arrival_city_id'=>4466,
					'departure_date'=>'21.05.2012',
				),
			),
		);
		
		$fs = new FlightSearch();
		$fs->status = 1;
		$fs->request_id = '1';
		$fs->data = '{}';
		$fs->sendRequest($aParams);
		echo  get_class($fs->oFlightVoyageStack->aFlightVoyages[0]);
		//echo json_encode($fs->oFlightVoyageStack);
		//$fs->save();
		//$fs = FlightSearch::model()->find('id=:ID', array(':ID'=>2));
		//$timestamp = date('Y-m-d H:i:s', time()-3600*3);
		//$fs = FlightSearch::model()->find('`key`=:KEY AND timestamp>=:TIMESTAMP', array(':KEY'=>'',':TIMESTAMP'=>$timestamp));
		//echo json_encode($fs->aRoutes);
		//echo 1;
		//$fs->test = 'tst';
		Yii::log('Hello people!!!', 'info', 'system.kk');
		$this->render('test',array('sText'=>'hh','flightStack'=>$fs->oFlightVoyageStack));
	}
	
	public function actionDictionary(){
		$sDType = 'airports';
		/*$sFilename = "/srv/www/easytrip.danechka.com/public_html/dictionaries/{$sDType}.json";
		$aJData = json_decode(file_get_contents($sFilename));
		if($aJData){
			foreach ($aJData as $oRecord){
				$object = new Airport();
				$object->id = $oRecord->id;
				$object->code = $oRecord->code;
				$object->local_ru = $oRecord->local_ru;
				$object->local_en = $oRecord->local_en;
				$object->position = $oRecord->position;
				$object->city_id = $oRecord->city_id;
				$object->save();
			}
		}
		$result = count($aJData);
		$this->render('dictionary',array('results'=>$result));*/
	}
}