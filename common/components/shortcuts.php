
<?php
function appParams($param)
{
    $inside = explode('.', $param);
    if (sizeof($inside) > 1)
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

/**
 * all in collection?
 *
 * Passes each element of the collection to the given function. The method
 * returns true if the function never returns false or null.
 *
 * If the function is not given, an implicit
 * function ($v) { return ($v !== null && $v !== false) is added
 * (that is array_all() will return true only if none of the collection members are false or null.)
 *
 * @param array $arr input array
 * @param callable|array $lambda takes an element, returns a bool (optional)
 * @return boolean
 */
function array_all($arr, $lambda = null)
{
    // these differ from PHP's "falsy" values
    if (!is_callable($lambda))
    {
        foreach ($arr as $value)
            if ($value === false || $value === null)
                return false;
    }
    else
    {
        foreach ($arr as $value)
            if (!call_user_func($lambda, $value))
                return false;
    }
    return true;
}

/**
 * check is variable acts as an array and suitable for foreach statement
 *
 * @param $var
 * @return bool
 */
function is_iterable($var)
{
    return (is_array($var) || $var instanceof Traversable);
}



