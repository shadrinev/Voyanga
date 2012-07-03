<?php
class Passenger
{
    const TYPE_ADULT = 1;
    const TYPE_CHILD = 2;
    const TYPE_INFANT = 3;

    public $type;
    /** @var Passport */
    public $passport;

    public function checkValid()
    {
        return true;
    }
}