<?php

class m120523_072136_alter_flight_cache_table extends CDbMigration
{
	public function up()
	{
        $this->addColumn('flight_cache', 'createdAt', 'datetime');
        $this->addColumn('flight_cache', 'updatedAt', 'datetime');
	}

	public function down()
	{
		return true;
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