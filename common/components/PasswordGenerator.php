<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 12.12.12
 * Time: 10:15
 */
class PasswordGenerator
{
    static public function createSimple()
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz";
        $minchars = 8;
        $maxchars = 10;

        $escapecharplus = 0;
        $repeat = mt_rand($minchars, $maxchars);
        $randomword = '';

        while ($escapecharplus < $repeat)
        {
            $randomword .= $chars[mt_rand(1, strlen($chars) - 1)];
            $escapecharplus += 1;
        }
        return $randomword;
    }
}
