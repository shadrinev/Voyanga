<?php

class PaymentsTestController extends CExtController 
{
    public function actionIndex()
    {
        $this->renderPartial('test');
    }

}