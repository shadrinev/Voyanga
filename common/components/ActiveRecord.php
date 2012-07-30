<?php

/**
 * ActiveRecord is the base active record class from which all active record model classes should extend.
 *
 * Houses the code needed across all child AR instances
 */
class ActiveRecord extends CActiveRecord {

	public function __construct($scenario = 'insert') {
		parent::__construct($scenario);
		Yii::app()->configManager->configMe($this);
	}

}
