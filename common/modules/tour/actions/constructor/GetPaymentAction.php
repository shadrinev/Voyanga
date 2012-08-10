<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 09.08.12
 * Time: 14:00
 * To change this template use File | Settings | File Templates.
 */
class GetPaymentAction extends CAction
{
    public function run()
    {
        VarDumper::dump(Yii::app()->order->getPayment());
    }

}
