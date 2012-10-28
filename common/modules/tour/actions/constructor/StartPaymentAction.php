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
        echo '<pre>';
        CVarDumper::dump(Yii::app()->user->getState('bookerIds'));
        echo '</pre>';
        //VarDumper::dump(Yii::app()->order->startPayment());
    }

}
