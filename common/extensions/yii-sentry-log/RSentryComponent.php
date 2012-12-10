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
	 * @var class Sentry stored connection
	 */
	protected $_client;

	/**
	 * Initializes the connection.
	 */
	public function init()
	{
		parent::init();
		
        if(!class_exists('Raven_Autoloader', false)) {
            # Turn off our amazing library autoload
            spl_autoload_unregister(array('YiiBase','autoload'));

            # Include request library
            include(dirname(__FILE__) . '/lib/Raven/Autoloader.php');

            # Run request autoloader
            Raven_Autoloader::register();
            # Give back the power to Yii
            spl_autoload_register(array('YiiBase','autoload'));
        }

        if($this->_client===null)
			$this->_client = new Raven_Client($this->dsn);

        Yii::app()->attachEventHandler('onException',array($this,'handleException'));
	}

    /**
     * logs exception
     * @param	CEvent	$event	Description
     */
    public function handleException($event) {
        $this->_client=$this->_client->captureException($event->exception);
    }

}
