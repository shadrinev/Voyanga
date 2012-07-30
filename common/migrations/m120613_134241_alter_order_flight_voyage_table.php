<?php

class m120613_134241_alter_order_flight_voyage_table extends CDbMigration
{
	public function up()
	{
        $this->addColumn('order_flight_voyage', 'orderId', 'integer');
	}

	public function down()
	{
		echo "m120613_134241_alter_order_flight_voyage_table does not support migration down.\n";
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