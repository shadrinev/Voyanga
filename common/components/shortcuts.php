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
    {
        return Yii::app()->params[$param];
    }

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
        {
            if ($value === false || $value === null)
            {
                return false;
            }
        }
    }
    else
    {
        foreach ($arr as $value)
        {
            if (!call_user_func($lambda, $value))
            {
                return false;
            }
        }
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


function sluggable($string, $separator = '-' )
{
    $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
    $special_cases = array( '&' => 'and');
    $string = mb_strtolower( trim( $string ), 'UTF-8' );
    $string = str_replace( array_keys($special_cases), array_values( $special_cases), $string );
    $string = preg_replace( $accents_regex, '$1', htmlentities( $string, ENT_QUOTES, 'UTF-8' ) );
    $string = preg_replace("/[^a-z0-9]/u", "$separator", $string);
    $string = preg_replace("/[$separator]+/u", "$separator", $string);
    return $string;
}


