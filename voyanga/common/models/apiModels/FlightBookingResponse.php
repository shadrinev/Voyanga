<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 03.07.12
 * Time: 11:27
 * To change this template use File | Settings | File Templates.
 */
class FlightBookingResponse
{
    public $pnr;
    public $expiration;
    public $nemoBookId;
    /** @var integer 1 - ok, 2 error */
    public $status;
}
