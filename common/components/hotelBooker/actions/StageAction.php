<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 02.07.12
 * Time: 11:26
 * To change this template use File | Settings | File Templates.
 */
abstract class StageAction extends CAction
{
    public function run()
    {
        throw new CHttpException(500,'You can`n execute this action directly');
    }
    abstract public function execute();
}
