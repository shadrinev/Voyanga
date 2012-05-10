<?php

class m120510_153607_add_email_table extends UserDbMigration
{
	public function up()
	{
        $this->createTable('emails', array(
        	'id'=>'pk',
        	'sender'=>'string NOT NULL',
        	'recipient'=>'string NOT NULL',
        	'cc'=>'string',
        	'bcc'=>'string',
        	'subject'=>'string',
        	'headers'=>'text',
        	'content'=>'text',
        	'isHtml'=>'boolean',
        	'timeAdded'=>'timestamp'
        ));
	}

	public function down()
	{
		$this->dropTable('emails');
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