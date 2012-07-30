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
 * Event Dispatcher.
 * 
 * The most important features of this notification system are:
 * 
 * - Lazy loading of observers with an option to configure class instantiation
 * - Does not use call_user_* functions on notification, only straight method calls
 * - Does not have a defined event class and lets you pass on any number of arguments
 * - Does not require to implement an interface
 * - Offers a class level (Dispatcher) as well as an application level (StaticDispatcher) 
 * notification system
 * 
 * Compared to its siblings around, this is the fastest and most flexible event
 * dispatcher implementation. This system does not utilize call_user_* functions that
 * show inconsistent and slow performance. Instead, only straigt method calls are used.
 * The configuration option in the attach() method enables to instantiate a class with an
 * array of options, which makes it easy to use with other libraries utilizing arrays
 * in the class contructors. 
 * 
 * Notification 2.0 comes with major simplifications and performance increase compared to
 * v 1.0. Two methods have been removed (notifyAll() and notifyUntil()) along with the
 * Handler class, which was used to store observer class definitions. Also Subject class
 * changed its name to the current Dispatcher class. A chain() method has been added
 * which is useful if you'd like to pass the return value of one handler to the next handler
 * as an argument during notification. Also a simple Message container is included with v 2.0 
 * in case a commun class is preferred for communication.
 * 
 * @author  Omercan Sebboy (www.osebboy.com)
 * @version 2.0
 */
class Dispatcher
{
	/**
	 * Event name to observers.
	 * 
	 * @var array
	 */
	protected $map = array();

	/**
	 * Connect an event to an observer. 
	 * 
	 * @param string $event | event to observe
	 * @param mixed $context | object or string class name
	 * @param string $method | method to call
	 * @param array $config | key, value pairs to use on class initialization
	 * @return void
	 */
	public function attach($event, $context, $method, array $config = array())
	{
		isset($this->map[$event]) ? : $this->map[$event] = array();
		$this->map[$event][] = array($context, $method, $config);
	}

	/**
	 * Remove observer.
	 * 
	 * @param string $event
	 * @param mixed $context | string or object
	 * @param string $method
	 * @return boolean | true if removed, false otherwise
	 */
	public function detach($event, $context, $method)
	{
		foreach ($this->getObservers($event) as $k => $h)
		{
			unset($h[2]);		
			if ($h === array($context, $method))
			{
				unset($this->map[$event][$k]);	
				if (empty($this->map[$event]))
				{
					unset($this->map[$event]);
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * Notify observers, this method will store the return value from each 
	 * observer in a SplDoublyLinkedList. If any one of the observers returns
	 * 'false', then the notification stops with the last value of the List
	 * being 'false'.
	 * 
	 * @param string $event
	 * @param mixed $args
	 * @return SplDoublyLinkedList | return values from the observers
	 */
	public function notify($event, $args = null)
	{
		$args = $args instanceof \stdClass && $args->name === 'static' ? $args->args : 
				array_slice(func_get_args(), 1);		
				
		$list = new SplDoublyLinkedList();

		foreach ($this->getObservers($event) as $observer)
		{
			$list->push($this->invoke($event, $observer, $args));
			
			if ( !$list->isEmpty() && $list->top() === false ) { break; }
		}
		
		return $list;
	}

	/**
	 * Passes the return value of one to the next observer as an argument
	 * creating a chain. $value is the inital value to start the chain. 
	 *  
	 * @param string $event
	 * @param mixed $value
	 * @return mixed $value
	 */
	public function chain($event, $value)
	{
		foreach ($this->getObservers($event) as $observer)
		{
			$value = $this->invoke($event, $observer, array($value));
		}
		return $value;
	}

	/**
	 * Get registered events.
	 * 
	 * @return array
	 */
	public function getEvents()
	{
		return array_keys($this->map);
	}
	
	/**
	 * Get observers for an event.
	 * 
	 * @param string $event
	 * @return array
	 */
	public function getObservers($event)
	{
		return isset($this->map[$event]) ? $this->map[$event] : array();
	}

	/**
	 * Remove an event and its observers.
	 * 
	 * @param string $event
	 * @return boolean | true if removed, false otherwise
	 */
	public function clear($event)
	{
		if (isset($this->map[$event]))
		{
			unset($this->map[$event]);
			return true;
		}
		return false;
	}
	
	/**
	 * Invokes observer's registered callback. Notification does not use
	 * call_user_* functions due to their inconsistent and slow performance.
	 * Straight method calls are used instead.
	 * 
	 * @param array $handler | observer definition array
	 * @param array $args
	 * @return mixed
	 */
	protected function invoke($eventName, array $observer, array $args = array())
	{
		$obj = $observer[0];
		if (is_string($obj))
		{
			$obj = new $obj();
            if (!empty($observer[2]))
                foreach ($observer[2] as $key=>$value)
                    $obj->$key = $value;
		}
		$method = $observer[1];
        $event = new Event();
        $event->name = $eventName;
		switch (count($args))
		{
			case 0:
                $event->args = array();
				return $obj->$method();
			case 1:
                $event->args = array($args[0]);
				return $obj->$method($event);
			case 2:
                $event->args = array($args[0], $args[1]);
				return $obj->$method($event);
			case 3:
                $event->args = array($args[0], $args[1], $args[2]);
				return $obj->$method($event);
			case 4:
                $event->args = array($args[0], $args[1], $args[2], $args[3]);
				return $obj->$method($event);
			case 5:
                $event->args = array($args[0], $args[1], $args[2], $args[3], $args[4]);
				return $obj->$method($event);
			case 6:
                $event->args = array($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
				return $obj->$method($event);
			default:
				return false;
		}
	}
}
?>