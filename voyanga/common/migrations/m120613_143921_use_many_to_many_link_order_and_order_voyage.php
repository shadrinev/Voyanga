<?php

class m120613_143921_use_many_to_many_link_order_and_order_voyage extends CDbMigration
{
	public function up()
	{
        $this->createTable('order_has_flight_voyage', array(
            'orderId' => 'integer',
            'orderFlightVoyage' => 'integer',
            'PRIMARY KEY(orderId, orderFlightVoyage)'
        ));
        $this->dropColumn('order_flight_voyage', 'orderId');
	}

	public function down()
	{
		echo "m120613_143921_use_many_to_many_link_order_and_order_voyage does not support migration down.\n";
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