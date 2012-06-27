<?php

function appParams($param)
{
    $inside = explode('.', $param);
    if (sizeof($inside)>1)
    {
        $first = Yii::app()->params[$inside[0]];
        array_shift($inside);
        foreach ($inside as $one)
        {
            $return = $first[$one];
            $first = $return;
        }
    }
    else
        return Yii::app()->params[$param];

    return $return;
}