<?php

class m130226_185840_prepare_for_sharing extends CDbMigration
{
	public function up()
	{
        $this->addColumn('order', 'ttl', 'datetime');
	}

	public function down()
	{
		echo "m130226_185840_prepare_for_sharing does not support migration down.\n";
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