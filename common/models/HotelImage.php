<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 29.06.12
 * Time: 13:20
 * To change this template use File | Settings | File Templates.
 */
class HotelImage
{
    public $description;
    public $smallUrl;
    public $largeUrl;

    function __construct($params)
    {
        $attrs = get_object_vars($this);
        foreach($attrs as $attrName=>$attrVal)
        {
            if(isset($params[$attrName])){
                $this->{$attrName} = $params[$attrName];
            }
        }
    }
}
