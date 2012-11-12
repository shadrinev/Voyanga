<?php

class LandingController extends Controller {

    public function actionIndex()
    {
        $this->layout = 'static';
        $this->render('landing');
    }

}