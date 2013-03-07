<?php

class m130307_115311_add_marker extends CDbMigration
{
	public function up()
	{
        $this->addColumn('order_booking', 'marker', 'string');
	}

	public function down()
	{
		echo "m130307_115311_add_marker does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}