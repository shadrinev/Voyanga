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
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF
            ),
            'cityAutocomplete'=>array(
                'class'=>'application.actions.AAutoCompleteAction',
                'modelClass'=>'City',
                'cache'=>true,
                'cacheExpire'=>1800,
                'attributes'=>array('localRu','localEn','code:='),
                'labelTemplate'=>'{localRu}, {country.localRu}, {code}',
                'valueTemplate'=>'{localRu}',
                'criteria'=>array('with'=>'country'),


            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction'
            )
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
        if ($error = Yii::app()->errorHandler->error)
        {
            if (Yii::app()->request->isAjaxRequest) echo $error['message'];
            else $this->render('error', $error);
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if (isset($_POST['ContactForm']))
        {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate())
            {
                $headers = "From: {$model->email}\r\nReply-To: {$model->email}";
                mail(Yii::app()->params['adminEmail'], $model->subject, $model->body, $headers);
                Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact', array(
            'model' => $model
        ));
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $model = new LoginForm();

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm']))
        {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array(
            'model' => $model
        ));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionPassport()
    {
        $model = new PassportForm();
        $form = new CForm('application.views.site.passportForm', $model);
        if ($form->submitted('smb') && $form->validate())
        {
            echo '#####jjjjjj####';
            $ar_model = new Passport();

            $ar_model->attributes = $model->attributes;
            $ar_model->save();
            print_r($ar_model->attributes);
            $this->render('passport', array(
                'form' => $form,
                'message' => 'all right'
            ));
        } else
        {

            echo '#####kkkjjj####';
            print_r($model->errors);
            $this->render('passport', array(
                'form' => $form
            ));
        }

    }

    public function actionGeoip()
    {
        $geoIpService = new GeoIpService();
        $GeoIP = new GetGeoIP();
        $GeoIP->IPAddress = '93.187.189.205';

        $response = $geoIpService->GetGeoIP($GeoIP);
        print_r($response);
        $geoIp = $response->GetGeoIPResult;
        echo sprintf('Your IP: %s (%s, %s)', $geoIp->IP, $geoIp->CountryName, $geoIp->CountryCode);
    }

    public function actionTest()
    {
        //$country = new Country();
        $oFlightSearchParams = new FlightSearchParams();
        $oFlightSearchParams->addRoute(array(
            'adult_count' => 5,
            'child_count' => 2,
            'infant_count' => 0,
            'departure_city_id' => 5185,
            'arrival_city_id' => 4466,
            'departure_date' => '21.05.2012'
        ));
        $oFlightSearchParams->addRoute(array(
            'adult_count' => 5,
            'child_count' => 2,
            'infant_count' => 0,
            'departure_city_id' => 4466,
            'arrival_city_id' => 5185,
            'departure_date' => '30.05.2012'
        ));
        $oFlightSearchParams->flight_class = 'E';
        $fs = new FlightSearch();
        $fs->status = 1;
        $fs->requestId = '1';
        $fs->data = '{}';
        $fs->sendRequest($oFlightSearchParams);
        echo get_class($fs->oFlightVoyageStack->aFlightVoyages[0]);
        //echo json_encode($fs->oFlightVoyageStack);
        //$fs->save();
        //$fs = FlightSearch::model()->find('id=:ID', array(':ID'=>2));
        //$timestamp = date('Y-m-d H:i:s', time()-3600*3);
        //$fs = FlightSearch::model()->find('`key`=:KEY AND timestamp>=:TIMESTAMP', array(':KEY'=>'',':TIMESTAMP'=>$timestamp));
        //echo json_encode($fs->aRoutes);
        //echo 1;
        //$fs->test = 'tst';
        $logRouter = Yii::app()->log;
        $logRouter->routes[1]->setLogFile('info2.log'); //test

        //$logRouter->routes[2]->enabled = false;
        Yii::log('Hello people!!!', 'info', 'application');

        $this->render('test', array(
            'sText' => 'hh' . print_r(Yii::app()->gdsAdapter),
            'flightStack' => $fs->oFlightVoyageStack
        ));
    }



    public function actionFlightSearch()
    {
        $model = new FlightSearchForm();
        $form = new CForm('application.views.site.flightSearchForm', $model);
        if ($form->submitted('smb') && $form->validate())
        {
            Yii::import('application.modules.gds.models.*');
            $nemo = new GDSNemo();
            $flightSearchParams = new FlightSearchParams();
            $flightSearchParams->addRoute(array(
                'adult_count' => $model->adultCount,
                'child_count' => $model->childCount,
                'infant_count' => $model->infantCount,
                'departure_city_id' => $model->departureCityId,
                //'arrival_city_id' => 3654,
                'arrival_city_id' => $model->arrivalCityId,
                'departure_date' => $model->departureDate
            ));

            $form->attributes = $model->attributes;
            /*$flightSearchParams->addRoute(array(
                'adult_count' => 1,
                'child_count' => 0,
                'infant_count' => 0,
                'departure_city_id' => 4381,
                'arrival_city_id' => 4931,
                'departure_date' => '30.05.2012'
            ));*/
            $flightSearchParams->flight_class = 'E';
            //$nemo->FlightTariffRules();
            $fs = new FlightSearch();
            $fs->status = 1;
            $fs->requestId = '1';
            $fs->data = '{}';
            //echo $flightSearchParams->key;
            $fs->sendRequest($flightSearchParams);

            //$aFlights = $nemo->FlightSearch($flightSearchParams);
            //$aParamsFS['aFlights'] = $aFlights;
            //$oFlightVoyageStack = new FlightVoyageStack($aParamsFS);
            //print_r($oFlightVoyageStack);

            //echo '#####jjjjjj####';

            $this->render('flightResult', array(
                'form' => $form,
                'message' => 'all right',
                'flightSearchKey' => $fs->key,
                'flightStack' => $fs->oFlightVoyageStack
            ));
        } else
        {

            //echo '#####kkkjjj####';
            print_r($model->errors);
            $this->render('flightResult', array(
                'form' => $form,
                'flightStack' => null
            ));
        }
    }

    public function actionFlightBooking($fskey,$id)
    {
        $fs = Yii::app()->cache->get('flightSearch'.$fskey);
        echo "FlightBooking key:{$fskey}";

        $flightVoyage = $fs->oFlightVoyageStack->getFlightById($id);

        $route = $fs->aRoutes[0];
        $countPassengers = $route->adultCount + $route->childCount + $route->infantCount;
        $passports = array();

        $bookForm = new BookingForm();
        $configArray = require(Yii::getPathOfAlias('application.views.site.bookingForm').'.php');

        $configArray['elements']['passports'] = array('type'=>'form','elements'=>array());
        $mainForm = new EForm($configArray,$bookForm);

        for($i=0;$i<$countPassengers;$i++)
        {
            $modelPassport = new PassportForm();
            $modelPassport->id = $i;
            $passports['passport'.$i] = $modelPassport;

        }
        $bookForm->passports = $passports;

        if ($mainForm->submitted('smb') && $mainForm->validate())
        {

        }
        else
        {
            $this->render('flightBooking', array(
                'form' => $bookForm->getForm(),
                'flightStack' => null
            ));
        }


    }

    public function actionDictionary()
    {
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