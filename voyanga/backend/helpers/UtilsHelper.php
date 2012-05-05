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

    public static function dateToPointDate($sDate)
    {
        return $sDate;
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