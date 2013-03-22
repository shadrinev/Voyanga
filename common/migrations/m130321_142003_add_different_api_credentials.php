<?php

class m130321_142003_add_different_api_credentials extends CDbMigration
{
	public function up()
	{
        $this->addColumn('partner', 'clientId', 'string');
        $this->addColumn('partner', 'apiKey', 'string');
	}

	public function down()
	{
		echo "m130321_142003_add_different_api_credentials does not support migration down.\n";
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