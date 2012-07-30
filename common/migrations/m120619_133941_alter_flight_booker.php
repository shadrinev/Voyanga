<?php

class m120619_133941_alter_flight_booker extends CDbMigration
{
	public function up()
	{
        $this->addColumn('flight_booker', 'created_at', 'datetime');
        $this->addColumn('flight_booker', 'updated_at', 'datetime');
	}

	public function down()
	{
		echo "m120619_133941_alter_flight_booker does not support migration down.\n";
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