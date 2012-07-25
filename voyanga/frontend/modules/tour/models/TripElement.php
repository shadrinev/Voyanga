<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 25.07.12
 * Time: 11:44
 */
abstract class TripElement extends CModel implements IECartPosition
{
    const TYPE_FLIGHT = 1;
    const TYPE_HOTEL = 2;

    public $type;
}
