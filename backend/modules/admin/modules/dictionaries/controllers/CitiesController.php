<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 18.03.13
 * Time: 18:34
 * To change this template use File | Settings | File Templates.
 */
class CitiesController extends ABaseAdminController
{
    public $defaultAction = 'admin';
    /**
     * Lists all models.
     */
    public function actionIndex()
    {

    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $this->render('index',array(


        ));
    }
}
