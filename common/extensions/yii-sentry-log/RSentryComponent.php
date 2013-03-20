<?php

/**
 * RSentryComponent records exceptions to sentry server.
 *
 * RSentryComponent can be used with RSentryLog but only tracts exceptions
 * as Yii logger does not pass the exception to the logger but rather a string traceback
 * RSentryLog "error" logging is not that usefull as the traceback
 * does not contain veriables but only a string where this component allows you to use
 * the power of sentry for exceptions.
 *
 * @author Pieter Venter <boontjiesa@gmail.com>
 */
class RSentryComponent extends CApplicationComponent
{
    /**
     * @var string Sentry DSN value
     */
    public $dsn;

    /**
     * @var Raven_Client Sentry stored connection
     */
    protected $_client;

    /**
     * Initializes the connection.
     */
    public function init()
    {
        parent::init();

        if (!class_exists('Raven_Autoloader', false)) {
            spl_autoload_unregister(array('YiiBase', 'autoload'));
            include(dirname(__FILE__) . '/raven-php/lib/Raven/Autoloader.php');
            Raven_Autoloader::register();
            spl_autoload_register(array('YiiBase', 'autoload'));
        }

        if ($this->_client === null)
            $this->_client = new Raven_Client($this->dsn);

        Yii::app()->attachEventHandler('onException', array($this, 'handleException'));
    }

    /**
     * logs exception
     * @param CEvent $event Description
     */
    public function handleException($event)
    {
        if (!($event->exception instanceof CHttpException))
            $this->_client->captureException($event->exception);
    }

    public function logException($exception, $param=null)
    {
        // param => array('culprit' => 'test') => to get unique
        if (!($exception instanceof CHttpException))
            $this->_client->captureException($exception, $param);
    }
}
