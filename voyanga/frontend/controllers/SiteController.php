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
                'criteria'=>array('with'=>'country','condition'=>'countAirports!=0'),


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
        echo get_class($fs->flightVoyageStack->aFlightVoyages[0]);
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
            'flightStack' => $fs->flightVoyageStack
        ));
    }



    public function actionFlightSearch()
    {
        $model = new FlightSearchForm();
        $form = new CForm('application.views.site.flightSearchForm', $model);
        if ($form->submitted('smb') && $form->validate())
        {
            Yii::import('application.modules.gds.models.*');
            //$nemo = new GDSNemo();
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
            if($model->returnDate)
            {
                $flightSearchParams->addRoute(array(
                    'adult_count' => $model->adultCount,
                    'child_count' => $model->childCount,
                    'infant_count' => $model->infantCount,
                    'departure_city_id' => $model->arrivalCityId,
                    //'arrival_city_id' => 3654,
                    'arrival_city_id' => $model->departureCityId,
                    'departure_date' => $model->returnDate
                ));
            }

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
                'flightStack' => $fs->flightVoyageStack
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

    public function actionFillSearch($from = 'LED',$to = 'MOW')
    {
        $model = new FlightSearchForm();
        $departureCity = City::getCityByCode($from);
        $arrivalCity = City::getCityByCode($to);
        $model->departureCityId = $departureCity->id;
        $model->arrivalCityId = $arrivalCity->id;
        $model->departureCity = $departureCity->localRu;
        $model->arrivalCity = $arrivalCity->localRu;

        try
        {
            $criteria = new CDbCriteria();
            //todo: group by datefrom
            $criteria->addColumnCondition(array('`from`'=>$model->departureCityId, '`to`'=>$model->arrivalCityId));
            $criteria->addCondition('`dateFrom` > "'.date('Y-m-d').'"');
            $criteria->addCondition('`dateBack` > "0000-00-00"');
            $criteria->limit = 20;
            $criteria->order = 'updatedAt desc';

            //$criteria->addCondition('`dateBack` = "0000-00-00"');

            $flightCaches = FlightCache::model()->findAll($criteria);
            $bestValues = array();

            if ($flightCaches)
            {
                foreach($flightCaches as $flightCache)
                {
                    if(!isset($bestValues[$flightCache->dateFrom]))
                    {
                        $bestCaches[$flightCache->dateFrom] = $flightCache;
                    }
                    //print_r($flightCache->attributes);
                }

            }
            else
                throw new CException('Can\'t get best pricetime');
            //$price = MFlightSearch::getOptimalPrice($model->departureCityId, $model->arrivalCityId, $model->departureDate);
            //echo $price;
        }
        catch (Exception $e)
        {
            print $e->getMessage();
        }

        $form = new CForm('application.views.site.flightSearchForm', $model);
        if ($form->submitted('smb') && $form->validate())
        {
            Yii::import('application.modules.gds.models.*');
            //$nemo = new GDSNemo();
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
            if($model->returnDate)
            {
                $flightSearchParams->addRoute(array(
                    'adult_count' => $model->adultCount,
                    'child_count' => $model->childCount,
                    'infant_count' => $model->infantCount,
                    'departure_city_id' => $model->arrivalCityId,
                    //'arrival_city_id' => 3654,
                    'arrival_city_id' => $model->departureCityId,
                    'departure_date' => $model->returnDate
                ));
            }

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
                'flightStack' => $fs->flightVoyageStack
            ));
        } else
        {

            //echo '#####kkkjjj####';
            print_r($model->errors);
            $this->render('flightResult', array(
                'form' => $form,
                'bestCaches'=>$bestCaches,
                'flightStack' => null
            ));
        }
    }

    public function actionFlightBooking($fskey,$id,$bookingId = 0)
    {
        $fs = Yii::app()->cache->get('flightSearch'.$fskey);
        echo "FlightBooking key:{$fskey}";

        $flightVoyage = $fs->flightVoyageStack->getFlightById($id);

        $route = $fs->routes[0];
        $countPassengers = $route->adultCount + $route->childCount + $route->infantCount;
        $passports = array();
        $valid = TRUE;
        if(!$bookingId)
        {
            $booking = new BookingForm();

            $countries = Country::model()->findAll(array('order'=>'position desc'));
            $countriesList = array();
            foreach($countries as $country)
            {
                $countriesList[$country->id] = $country->localRu;
            }

                if(isset($_POST['BookingForm']))
                {
                    $booking->attributes=$_POST['BookingForm'];
                    $valid=$booking->validate() && $valid;
                }

                for($i=0;$i<$countPassengers;$i++)
                {
                    $modelPassport = new PassportForm();
                    $passports[$i] = $modelPassport;

                    if(isset($_POST['PassportForm'][$i]))
                    {
                        $passports[$i]->attributes=$_POST['PassportForm'][$i];
                        if(!$passports[$i]->validate()){
                            echo 'PassportForm id:'.$i.' dont valid';
                        }
                        $valid=$passports[$i]->validate() && $valid;
                    }
                }
            if(!isset($_POST['BookingForm']))
            {
                $valid = false;
            }

            if($valid)
            {
                //saving data to objects
                $bookingAr = new Booking();

                $bookingAr->email = $booking->contactEmail;
                $bookingAr->phone = $booking->contactPhone;
                $bookingPassports = array();
                foreach($passports as $passport)
                {
                    $bookingPassport = new BookingPassport();
                    $bookingPassport->birthday = $passport->birthday;
                    $bookingPassport->firstName = $passport->firstName;
                    $bookingPassport->lastName = $passport->lastName;
                    $bookingPassport->countryId = $passport->countryId;
                    $bookingPassport->number = $passport->number;
                    $bookingPassport->series = $passport->series;
                    $bookingPassport->genderId = $passport->genderId;
                    $bookingPassport->documentTypeId = $passport->documentTypeId;

                    $bookingPassports[] = $bookingPassport;
                }
                $bookingAr->bookingPassports = $bookingPassports;
                $bookingAr->flightId = $flightVoyage->flightKey;

                //print_r($bookingAr);
                if($bookingAr->validate())
                {

                    $bookingAr->save();//die();
                    $bookingAr->id = $bookingAr->getPrimaryKey();
                    $this->render('flightTicketing', array('booking'=>$bookingAr,'voyage'=>$flightVoyage));

                }
                else
                {
                    print_r($bookingAr->errors);
                    $this->render('flightBooking', array('passports'=>$passports,'booking'=>$booking,'countriesList'=>$countriesList));
                }
            }
            else
            {
                echo 'all left';
                $this->render('flightBooking', array('passports'=>$passports,'booking'=>$booking,'countriesList'=>$countriesList));
            }
        }
        else
        {
            $bookingAr = Booking::model()->findByPk($bookingId);
            $this->render('flightTicketing', array('booking'=>$bookingAr,'voyage'=>$flightVoyage));
        }




        /*$bookForm = new BookingForm();
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
            echo 'dsfsdfdsf';
        }
        else
        {
            $this->render('flightBooking', array(
                'form' => $bookForm->getForm(),
                'flightStack' => null
            ));
        }*/


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

    function randomAlphaNum($length)
    {

        $rangeMin = pow(36, $length-1); //smallest number to give length digits in base 36
        $rangeMax = pow(36, $length-1); //largest number to give length digits in base 36
        $base10Rand = mt_rand($rangeMin, $rangeMax); //get the random number
        $newRand = base_convert($base10Rand, 10, 36); //convert it

        return $newRand; //spit it out

    }

    public function actionTestWrite()
    {
        $writer = Yii::app()->sharedMemory;
        $writer->erase();
        for ($i=0; $i<1000000; $i++)
            $writer->write($i);
    }

    public function actionTestRead()
    {
        $reader = Yii::app()->sharedMemory;

    }

    public function actionTestHotel()
    {
        Yii::import('site.common.modules.hotel.models.*');

        $HotelClient = new HotelBookClient();
        //$HotelClient->synchronize();
        //$russia = Country::getCountryByCode('US');
        $city = City::getCityByCode('PAR');
        $city = City::getCityByCode('LON');
        $hotelSearchParams = new HotelSearchParams();
        $hotelSearchParams->checkIn = '2012-09-17';
        $hotelSearchParams->duration = '7';
        $hotelSearchParams->cityId = $city->hotelbookId;
        $hotelSearchParams->addRoom(2);
        $hotelSearchParams->addRoom(2);
        //$hotelSearchParams->addRoom(2,1);
        //$hotelSearchParams->addRoom(1);
        //$hotelSearchParams->addRoom(3);
        $resultSearch = $HotelClient->fullHotelSearch($hotelSearchParams);
        $hotelStack = new HotelStack($resultSearch);
        echo '<br>'.count($hotelStack->hotels);

        //print_r($HotelClient->getCities($russia->hotelbookId));
        /*$params = array('cityId'=>$city->hotelbookId,'checkIn'=>'2012-09-17','duration'=>'7');
        $params['rooms'] = array();
        $params['rooms'][] = array('roomSizeId'=>2,'child'=>0,'roomNumber'=>1);
        //$params['rooms'][] = array('roomSizeId'=>1,'child'=>0,'roomNumber'=>1);
        $resultSearch = $HotelClient->hotelSearch($params, true);
        $params['rooms'][0]['roomSizeId'] = 3;
        $resultSearch = $HotelClient->hotelSearch($params, true);
        $params['rooms'][0]['roomSizeId'] = 5;
        //$resultSearch = $HotelClient->hotelSearch($params, true);

        $HotelClient->processAsyncRequests();


        VarDumper::dump($HotelClient->requests);*/
        //$HotelClient->hotelSearchDetails($resultSearch->hotels[0]);
        //VarDumper::dump($resultSearch->hotels[0]);


    }

    public function actionTestmb()
    {

        //echo
        $criteria=new CDbCriteria;
        $criteria->condition='hotelbookId IS NOT NULL';
        //$criteria->params=array(':postID'=>10);

        $countries = Country::model()->findAll($criteria);
        foreach($countries as $country)
        {
            echo "unzip -n {$country->code}.zip<br>";
        }
        foreach($countries as $country)
        {
            echo "<a href='http://download.geonames.org/export/dump/{$country->code}.zip'>{$country->code} - {$country->localRu}</a><br>";
        }

    }

    public function actionTestMongo($cityname = 'Saint',$country = 'US')
    {
        //$mdb = Yii::app()->mongodb->
        /*$gdsReq = new GdsRequest();
        $gdsReq->_id = 'dsfhkjhwiur32423';
        $gdsReq->requestNum = '124';
        $gdsReq->requestXml = '<xml></xml>';
        $gdsReq->validate();
        print_r($gdsReq->getErrors());
        $gdsReq->save();

        $loadReq = GdsRequest::model()->findAll();
        foreach($loadReq as $req){
            //echo $req->_id;
            print_r($req);
        }*/

        if(UtilsHelper::countRussianCharacters($cityname) > 0)
        {
            $nameRu = $cityname;
            $soundexRu = UtilsHelper::soundex($nameRu,'RU');
            $nameEn = UtilsHelper::str_to_translit($nameRu);
            $soundexEn = UtilsHelper::soundex($nameEn,'EN');

            //$nameEn = UtilsHelper::cityNameToRus(
        }else{
            $nameEn = $cityname;
            $soundexEn = UtilsHelper::soundex($nameEn,'EN');
            $nameRu = UtilsHelper::cityNameToRus($cityname,$country);
            $soundexRu = UtilsHelper::ruSoundex($nameRu);
        }
        $metaphoneRu = UtilsHelper::ruMetaphone($nameRu);
        echo "nameEn: {$nameEn} nameRu: {$nameRu} <br>soundexEn: {$soundexEn} soundexRu: {$soundexRu} meta: {$metaphoneRu}<br>";
        $criteria = new EMongoCriteria(array('conditions'=>array('countryCode'=>array('equals'=>$country),'soundexEn'=>array('equals'=>$soundexEn)) ));
        $count = GeoNames::model()->count($criteria);
        echo 'Found by EN:'.$count.'<br>';
        if($count){
            //$criteria = new EMongoCriteria(array('conditions'=>array('countryCode'=>array('equals'=>$country),'soundexEn'=>array('equals'=>$soundexEn)) ));
            //$criteria->limit = 1;
            $find = GeoNames::model()->find($criteria);
            VarDumper::dump($find);
        }
        $criteria = new EMongoCriteria(array('conditions'=>array('countryCode'=>array('equals'=>$country),'metaphoneRu'=>array('equals'=>$metaphoneRu)) ));
        $count = GeoNames::model()->count($criteria);
        echo 'Found by Ru:'.$count.'<br>';
        if($count){
            //$criteria->limit = 1;
            VarDumper::dump(GeoNames::model()->find($criteria));
        }
        $criteria = new EMongoCriteria(array('conditions'=>array('countryCode'=>array('equals'=>$country),'metaphoneRu'=>array('equals'=>$metaphoneRu),'soundexEn'=>array('equals'=>$soundexEn)) ));
        $count = GeoNames::model()->count($criteria);
        echo 'Found by Both:'.$count.'<br>';
        if($count){
            //$criteria->limit = 1;
            VarDumper::dump(GeoNames::model()->findAll($criteria));
        }


    }
}
