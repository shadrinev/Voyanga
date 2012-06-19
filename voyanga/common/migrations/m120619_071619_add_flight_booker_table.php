<?php

class m120619_071619_add_flight_booker_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('flight_booker',array(
            'id'=>'pk',
            'status'=>'integer',
            'pnr'=>'string',
            'timeout'=>'datetime',
            'flight_voyage'=>'text'
        ));
	}

	public function down()
	{
		echo "m120619_071619_add_flight_booker_table does not support migration down.\n";
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