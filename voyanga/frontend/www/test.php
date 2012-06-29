<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 28.06.12
 * Time: 13:47
 */

class Singleton
{
    public static function instance()
    {
        static $inst;
        if(!isset($inst)) $inst = new static();
        return $inst;
    }
}

class S1 extends Singleton {
}

class S2 extends Singleton {
}

$s1 = S1::instance();
print_r($s1);

$s2 = S2::instance();
print_r($s2);

$s1->obj = true;

print_r(S1::instance());

?>