<?php

class m120521_085052_alter_flight_cache_table extends CDbMigration
{
	public function up()
	{
        $this->renameColumn('flight_cache','dateReturn','returnDate');
	}

	public function down()
	{
		echo "m120521_085052_alter_flight_cache_table does not support migration down.\n";
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