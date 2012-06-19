<?php

class m120619_074816_alter_flight_booker_table extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('flight_booker', 'status', 'VARCHAR(50)');
	}

	public function down()
	{
		echo "m120619_074816_alter_flight_booker_table does not support migration down.\n";
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