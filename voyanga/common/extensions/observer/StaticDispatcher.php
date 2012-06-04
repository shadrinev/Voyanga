<?php
/**
 * Notification: Event based notification system for PHP
 * 
 * Copyright (c) 2010 - 2011, Omercan Sebboy <osebboy@gmail.com>. 
 * All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE file 
 * that was distributed with this source code.
 *
 * @author     Omercan Sebboy (www.osebboy.com)
 * @package    Notification
 * @copyright  Copyright(c) 2010 - 2011, Omercan Sebboy (osebboy@gmail.com)
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    2.0
 */

/**
 * Static Dispatcher.
 * 
 * Simply works on an instance of Dispatcher class. The Dispatcher instance can be
 * changed with StaticDispatcher::setInstance() method. This gives flexibility
 * to create a dispatcher class and make it available to the application on demand.
 * 
 * @author  Omercan Sebboy (www.osebboy.com)
 * @version 2.0
 */
class StaticDispatcher
{
	/**
	 * Dispatcher instance.
	 * 
	 * @var Subject
	 */
	protected static $instance = null;
	
	/**
	 * Get a Dispatcher instance.
	 * 
	 * @return Notification\Dispatcher 
	 */
	public static function getInstance()
    {
        if (null === self::$instance) 
        {
            self::setInstance(new Dispatcher());
        }
        return self::$instance;
    }
    
    /**
     * Set the subject instance.
     * 
     * @param Notification\Dispatcher $dispatcher
     * @return void
     */
	public static function setInstance(Dispatcher $dispatcher)
    {
        self::$instance = $dispatcher;
    }
    
	/**
	 * Connect an event to an observer.
	 * 
	 * @param string   $event   | event to observe
	 * @param mixed    $context | object or string class name
	 * @param string   $method  | method to call
	 * @param array    $config  | key, value pairs to use on class initialization
	 * @return void
	 */
    public static function attach($event, $context, $method, $config = array())
    {
    	return self::getInstance()->attach($event, $context, $method, $config);
    }
    
	/**
	 * Remove observer.
	 * 
	 * @param string $event
	 * @param mixed $context | string or object
	 * @param string $method
	 * @return boolean | true if removed, false otherwise
	 */
    public static function detach($event, $context, $method)
    {
    	return self::getInstance()->detach($event, $context, $method);
    }
    
    /**
	 * Dispatch an event with arguments to the connected observers.
	 * 
	 * Notify observers, this method will store the return value from each 
	 * observer in a SplDoublyLinkedList. If any one of the observers returns
	 * 'false', then the notification stops with the last value of the List
	 * being 'false'.
	 * 
	 * @param  string $event
	 * @param  mixed  $args | arguments
	 * @return SplDoublyLinkedList
	 */
    public static function notify($event, $args = null)
    {
    	$std = new \stdClass();
    	$std->name = 'static';
    	$std->args = array_slice(func_get_args(), 1);
    	return self::getInstance()->notify($event, $std);
    }
    
	/**
	 * Passes the return value of one to the next listener as an argument
	 * creating a chain. $value is the inital value to start the chain. 
	 *  
	 * @param string $event
	 * @param mixed $value
	 * @return mixed $value | the value returned from the last observer
	 */
	public static function chain($event, $value)
	{
		return self::getInstance()->chain($event, $value);
	}

    /**
     * Get events.
     * 
     * @return array of event names
     */
    public static function getEvents()
    {
    	return self::getInstance()->getEvents();
    }
    
	/**
	 * Get observers for an event.
	 * 
	 * @param string $event
	 * @return array
	 */
	public static function getObservers($event)
	{
		return self::getInstance()->getObservers($event);
	}

    /**
     * Reset an event.
     * 
     * @param  string $event
     * @return bool | true if successful
     */
    public static function clear($event)
    {
    	return self::getInstance()->clear($event);
    }
}
?>