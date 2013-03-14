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
        $percentageUp = Yii::app()->params['hotel.markupPercentage'];
        $cost = ceil($originalPrice + $originalPrice * $percentageUp/100);
        return $cost;
    }

    public static function calculateDiscountHotelPrice($originalPrice)
    {
        $percentageUp = Yii::app()->params['hotel.markupPercentage'];
        $percentageDown = Yii::app()->params['hotel.markdownPercentage'];
        $precost = ceil($originalPrice * (1 + $percentageUp/100));
        $cost = ceil($precost * (1 - $percentageDown/100));
        return $cost;
    }
}
