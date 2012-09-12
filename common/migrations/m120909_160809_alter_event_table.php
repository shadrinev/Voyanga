<?php

class m120909_160809_alter_event_table extends CDbMigration
{
	public function up()
	{
        $this->addColumn('event', 'orderId', 'integer');
	}

	public function down()
	{
		echo "m120909_160809_alter_event_table does not support migration down.\n";
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