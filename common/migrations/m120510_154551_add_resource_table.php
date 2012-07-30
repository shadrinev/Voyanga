<?php

class m120510_154551_add_resource_table extends UserDbMigration
{
	public function up()
	{
        $this->createTable('resources', array(
        	'id'=>'pk',
        	'ownerModel'=>'string',
        	'ownerId'=>'integer',
        	'ownerAttribute'=>'string',
        	'name'=>'string',
        	'description'=>'text',
        	'path'=>'text',
        	'type'=>'string',
        	'size'=>'integer',
        	'userId'=>'integer',
        	'timeAdded'=>'timestamp'
        ));
	}

	public function down()
	{
		$this->dropTable('resources');
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