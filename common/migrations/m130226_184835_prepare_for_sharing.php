<?php

class m130226_184835_prepare_for_sharing extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('order', 'name', 'text');
        $this->addColumn('order', 'hash', 'varchar(32)');
	}

	public function down()
	{
		echo "m130226_184835_prepare_for_sharing does not support migration down.\n";
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