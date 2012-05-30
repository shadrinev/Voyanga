<?php

class m120530_112010_alter_tables_for_rbac extends CDbMigration
{
	public function up()
	{
        $this->addColumn('AuthItem', 'slug', 'string');
	}

	public function down()
	{
		echo "m120530_112010_alter_tables_for_rbac does not support migration down.\n";
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