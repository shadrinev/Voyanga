<?php

class m130410_141457_add_use_transport_to_partner extends CDbMigration
{
	public function up()
	{
        $this->addColumn('partner', 'use_transport', 'integer(1) default 0');
	}

	public function down()
	{
		echo "m130410_141457_add_use_transport_to_partner does not support migration down.\n";
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