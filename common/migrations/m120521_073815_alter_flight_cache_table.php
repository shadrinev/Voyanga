<?php

class m120521_073815_alter_flight_cache_table extends CDbMigration
{
	public function up()
	{
        $this->addColumn('flight_cache', 'dateReturn', 'datetime');
        $this->alterColumn('flight_cache','departureDate','datetime');
	}

	public function down()
	{
		$this->dropColumn('flight_cache','date');
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