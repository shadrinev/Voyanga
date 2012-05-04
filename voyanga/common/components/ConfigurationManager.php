<?php

/**
 * Description of ConfigurationManager
 *
 * @author Dani
 */
class ConfigurationManager extends CApplicationComponent
{
	protected $_classesConfig = array ();
	
	// Accepts the obj object, generates and returns a configuration array for the object
	// The returned array will reflect the settings for the whole class hierarchy of $obj,
	// children classes overide parrent settings in case of name collisions
	public function getClassConfig ($objectOrClassName)
	{
		if (is_string ($objectOrClassName)) $class = $objectOrClassName;
		else if (is_object ($objectOrClassName)) $class = get_class($objectOrClassName);
		else throw new CException ('objectOrClassName should be an object or a string with a name of a class');
		

		if (isset ($this->_classesConfig[$class]) && is_array ($this->_classesConfig[$class])) return $this->_classesConfig[$class];

		$originalClass = $class;
		$config = array();
		do
		{
			if (isset (Yii::app()->params[$class]) && is_array (Yii::app()->params[$class]))
				$config = array_merge (Yii::app()->params[$class], $config);
		} 
		while (($class = get_parent_class($class)) !== false);

		$this->_classesConfig[$originalClass] = $config;
		return $config;
	}

	// Applies a configuration to the passed object, according to its class
	public function configMe ($obj)
	{
		foreach ($this->getClassConfig ($obj) as $k => $v)
				$obj->$k = $v;
	}
}
