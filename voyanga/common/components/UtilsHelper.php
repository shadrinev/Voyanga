<?php
class UtilsHelper
{
    /**
     * Function return first element of array, if array have only 1 element, else return all array.
     * @param Array $aArr
     */
    public static function normalizeArray($aArr)
    {
        if (count($aArr) == 1)
        {
            $aEach = each($aArr);
            return $aArr[$aEach['key']];
        } else
        {
            return $aArr;
        }
    }

    public static function formatXML($xml)
    {
        $oDM = new DOMDocument();
        $oDM->loadXML ($xml);
        if($oDM){
            $oDM->formatOutput = true;
            $oDM->normalize();
            $xml = $oDM->saveXML();
        }
        return $xml;
    }

    /**
     * Modify incoming parameter to array(wrap to array), incoming parameter is object
     * @param unknown_type $oSoapArray
     */
    public static function soapObjectsArray(&$oSoapArray)
    {
        if (!is_array($oSoapArray))
        {
            $oObj = $oSoapArray;
            $oSoapArray = array(
                $oObj
            );
        }
    }

    /**
     * Return real value of soap element.
     * @static
     * @param $element
     * @return mixed
     */
    public static function soapElementValue($element){
        if(is_object($element))
        {
            return $element->_;
        }else{
            return $element;
        }
    }

    public static function dateToPointDate($sDate)
    {
        return date('d.m.Y',strtotime($sDate));
    }

    /**
     * Function for render duration time interval
     * @static
     * @param $sec - number of seconds
     * @param string $local - Locale for print (ru|en)
     * @return string - 5 ч 54 мин
     */
    public static function durationToTime($sec, $local = 'ru')
    {
        $min = (int)($sec / 60);
        $hour = floor($min / 60);
        $min = $min % 60;
        $hourWords = array('ru'=>'ч','en'=>'h');
        $minuteWords = array('ru'=>'мин','en'=>'min');
        return "{$hour} {$hourWords[$local]} {$min} {$minuteWords[$local]}";
    }

    /**
     * Функция которая добавляет правильное окончание к русскому слову использованному после числа.
     * @param $aWords - Массив из трех форм слова: первый элемент для 1, второй для 4, третий для 7, array('год','года','лет')
     * @param $iNum - число
     * @return string
     */
    public static function WordAfterNum($aWords, $iNum)
    {
        $iNum = $iNum % 100;
        if (count($aWords) > 2)
        {
            if ($iNum > 4 && $iNum < 21)
            {
                return $aWords[2];
            } else
            {
                $iOst = $iNum % 10;
                if ($iOst == 1)
                {
                    return $aWords[0];
                } elseif ($iOst > 1 && $iOst < 5)
                {
                    return $aWords[1];
                } else
                {
                    return $aWords[2];
                }
            }
        } else
        {
            if ($iNum > 1 && $iNum < 21)
            {
                return $aWords[1];
            } else
            {
                $iOst = $iNum % 10;
                if ($iOst == 1)
                {
                    return $aWords[0];
                } else
                {
                    return $aWords[1];
                }
            }
        }
    }
}