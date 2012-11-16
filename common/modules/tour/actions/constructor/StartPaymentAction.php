<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 09.08.12
 * Time: 13:59
 * To change this template use File | Settings | File Templates.
 */
class StartPaymentAction extends CAction
{
    public function run()
    {
        try
        {
            $params = Yii::app()->order->getPaymentFormParams();
        } catch(Exception $e) {
            header("Content-type: application/json");
            $params = Array();
            $params['error'] = $e->getMessage();
            echo json_encode($params);
            exit;
        }
        // FIXME move to config
        $params['url']= "https://secure.payonlinesystem.com/ru/payment/ivoyanga/";
        $params['FailUrl'] = "https://api.voyanga.com/fail"
        header("Content-type: application/json");
        echo json_encode($params);
    }
}
