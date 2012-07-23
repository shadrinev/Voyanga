<?php

class m120723_081534_user_many_to_many_link_order_and_order_hotel extends CDbMigration
{
	public function up()
	{
        $this->createTable('order_has_hotel', array(
            'orderId' => 'integer',
            'orderHotel' => 'integer',
            'PRIMARY KEY(orderId, orderHotel)'
        ));
	}

	public function down()
	{
		echo "m120723_081534_user_many_to_many_link_order_and_order_hotel does not support migration down.\n";
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