<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 14.06.12
 * Time: 12:32
 * To change this template use File | Settings | File Templates.
 */
class HotelSearchResponse extends ResponseStatus
{
    public static $errorSatuses = array(0=>'No errors',1=>'Part error',2=>'Full Error');
    public $errorStatus;
    /** @var Hotel[] */
    public $hotels;
    public $timestamp;
    public $errorsDescriptions;
    public $searchId;
    public function attributeNames()
    {
        return CMap::mergeArray(parent::attributeNames(),
            array(
                'hotels',
                'errorsDescriptions'
            )
        );
    }
}
