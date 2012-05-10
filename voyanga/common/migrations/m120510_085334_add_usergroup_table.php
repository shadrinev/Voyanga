<?php

class m120510_085334_add_usergroup_table extends UserDbMigration
{
	public function up()
	{
		$this->createTable('usergroups', array(
			'id' => 'pk',
			'name' => 'string NOT NULL'
		));
	}

	public function down()
	{
		$this->dropTable('usergroups');
		return true;
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