<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 31.05.12
 * Time: 13:22
 */
class Notification extends CApplicationComponent
{
    public function handle($event)
    {
        echo $event->name;
//        echo '123';
    }

    public function handleBest($event)
    {
        VarDumper::dump($event);
    }
}