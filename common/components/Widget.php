<?php

/**
 * Description of Widget
 *
 */
class Widget extends CWidget
{
	public function __construct ($owner = null)
	{
		parent::__construct ($owner);
		Yii::app()->configManager->configMe($this);		
	}
}

