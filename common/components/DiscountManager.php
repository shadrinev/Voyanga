<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.11.12
 * Time: 13:14
 */
class DiscountManager
{
    public static function calculateHotelPrice($originalPrice)
    {
        $percentage = appParams('hotel.markupPercentage');
        $cost = ceil($originalPrice * (1 + $percentage/100));
        return $cost;
    }
}
