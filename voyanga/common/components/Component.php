<?php

/**
 * Description of Component
 *
 */
class Component extends CComponent
{
	public function __construct()
	{
		Yii::app()->configManager->configMe($this);
	}	
}

