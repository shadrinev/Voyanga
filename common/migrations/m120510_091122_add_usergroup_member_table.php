<?php

class m120510_091122_add_usergroup_member_table extends UserDbMigration
{
	public function up()
	{
        $this->createTable('usergroup_members', array(
        	'groupId' => 'int',
        	'userId' => 'int',
        	'timeAdded' => 'timestamp',
        	'isAdmin' => 'boolean',
        	'PRIMARY KEY (`groupId`,`userid`)'
        ));
	}

	public function down()
	{
		$this->dropTable('usergroup_members');
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