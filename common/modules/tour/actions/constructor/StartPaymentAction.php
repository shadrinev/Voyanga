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
        $params = Yii::app()->order->getPaymentFormParams();
        // FIXME move to config
        $params['url']= "https://secure.payonlinesystem.com/ru/payment/ivoyanga/";
        header("Content-type: application/json");
        echo json_encode($params);
    }
}
