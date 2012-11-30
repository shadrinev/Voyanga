<?php


class PayonlineController extends CController {

    public function actions()
    {
        return array(
            'success' => array('class'=>'common.extensions.payments.actions.SuccessAction'),
            'failure' => array('class'=>'common.extensions.payments.actions.FailureAction'),
        );
    }

}