<?php

class m120723_081250_add_order_column extends CDbMigration
{
	public function up()
	{
        $this->createTable('order_hotel', array(
            'id' => 'pk',
            'key' => 'string',
            'cityId' => 'integer',
            'checkIn' => 'date',
            'duration' => 'integer',
            'object' => 'text'
        ));
        $this->createIndex('search_by_key', 'order_hotel', 'key', true);
	}

	public function down()
	{
		echo "m120723_081250_add_order_column does not support migration down.\n";
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