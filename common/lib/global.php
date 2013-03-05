<?php
/**
 * This file contains constants and shortcut functions that are commonly used.
 * Please only include functions are most widely used because this file
 * is included for every request. Functions are less often used are better
 * encapsulated as static methods in helper classes that are loaded on demand.
 */

/**
 * This is the shortcut to DIRECTORY_SEPARATOR
 */
defined('DS') or define('DS',DIRECTORY_SEPARATOR);

function yiiCorrectShutdown()
{
    $error = error_get_last();
    if (($error !== null) && (isset($error['type'])) && ($error['type'] & E_ERROR))
    {
        $exception = new CException(CVarDumper::dumpAsString($error));
        Yii::app()->RSentryException->logException($exception);
        Yii::app()->end();
        Yii::app()->log->processLogs(null);
    }
}
register_shutdown_function('yiiCorrectShutdown');

