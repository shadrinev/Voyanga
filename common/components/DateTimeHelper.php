<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.08.12
 * Time: 11:28
 */
class DateTimeHelper
{
    static public function formatForJs($dateTime)
    {
        if (is_numeric($dateTime))
            $timestamp = $dateTime;
        else
            $timestamp = strtotime($dateTime);
        //2012-08-22T04:06Z
        return date('Y-m-d', $timestamp).'T'.date('H:i',$timestamp);
    }

    static public function formatForEventForm($dateTime)
    {
        if ($dateTime)
        {
            $timestamp = strtotime($dateTime);
            return date('d.m.Y', $timestamp);
        }
        return null;
    }
}
