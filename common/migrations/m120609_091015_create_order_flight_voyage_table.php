<?php

class m120609_091015_create_order_flight_voyage_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('order_flight_voyage', array(
            'id' => 'pk',
            'key' => 'string',
            'departureDate' => 'date',
            'departureCity' => 'integer',
            'arrivalCity' => 'integer',
            'object' => 'text'
        ));
        $this->createIndex('search_by_key', 'order_flight_voyage', 'key', true);
	}

	public function down()
	{
		echo "m120609_091015_create_order_flight_voyage_table does not support migration down.\n";
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