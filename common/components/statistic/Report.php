<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 29.05.12
 * Time: 10:13
 */
abstract class Report extends CComponent
{
    abstract public function getMongoCommand();
}
