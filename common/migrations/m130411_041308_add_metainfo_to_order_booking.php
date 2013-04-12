<?php

class m130411_041308_add_metainfo_to_order_booking extends CDbMigration
{
	public function up()
	{
        $this->addColumn('order_booking', 'meta', 'text');
	}

	public function down()
	{
		echo "m130411_041308_add_metainfo_to_order_booking does not support migration down.\n";
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