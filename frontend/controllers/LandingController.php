<?php

class LandingController extends Controller {

    public function actionIndex()
    {
        $this->layout = 'static';
        $this->render('landing');
    }

    public function actionHotels()
    {
        $this->layout = 'static';
        $this->render('hotels');
    }

    public function actionCity()
    {
        $this->layout = 'static';
        $this->render('city');
    }

    public function actionCountry()
    {
        $this->layout = 'static';
        $this->render('country');
    }

    public function actionOWFlight()
    {
        $this->layout = 'static';
        $this->render('owflight');
    }

}