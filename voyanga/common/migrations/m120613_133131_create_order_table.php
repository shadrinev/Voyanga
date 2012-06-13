<?php

class m120613_133131_create_order_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('order',array(
            'id' => 'pk',
            'name' => 'string',
            'userId' => 'integer',
            'createdAt' => 'datetime'
        ));
	}

	public function down()
	{
		echo "m120613_133131_create_order_table does not support migration down.\n";
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