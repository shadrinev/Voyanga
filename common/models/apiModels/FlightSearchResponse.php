<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 30.07.12
 * Time: 19:48
 * To change this template use File | Settings | File Templates.
 */
class FlightSearchResponse
{
    public $flights = array();
    public $searchId;
    /** @var ResponseStatus */
    public $responseStatus;
}
