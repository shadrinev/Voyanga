<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 16.07.12
 * Time: 12:37
 * To change this template use File | Settings | File Templates.
 */
class SWLogActiveRecord extends SWActiveRecord
{
    public function beforeTransition($event)
    {

        return parent::beforeTransition($event);
    }

    public function afterTransition($event)
    {

        return parent::beforeTransition($event);
    }

}
