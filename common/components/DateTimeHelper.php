<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.08.12
 * Time: 11:28
 */
class DateTimeHelper
{
    public static function formatForJs($dateTime)
    {
        if (is_numeric($dateTime))
            $timestamp = $dateTime;
        else
            $timestamp = strtotime($dateTime);
        return date('D, d M y H:i:s', $timestamp)." +0000";
    }
}
