<?php

class m130410_131949_add_direct_to_order_booking extends CDbMigration
{
	public function up()
	{
        $this->addColumn('order_booking', 'direct', 'integer');
	}

	public function down()
	{
		echo "m130410_131949_add_direct_to_order_booking does not support migration down.\n";
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