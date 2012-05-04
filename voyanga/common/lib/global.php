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
	$errorsMask = E_ERROR; // all the errors on which a special action should be taken to save logs
    if ($error['type'] & $errorsMask)
	{
		Yii::log("A Fatal php error occured: ".print_r($error, true), "error");
		Yii::app()->end();
		//the following line will work as well, and may be better if one wants to only execute logs-flushing on fatal errors
		//Yii::app()->log->processLogs(null);
    }
}
register_shutdown_function('yiiCorrectShutdown');

