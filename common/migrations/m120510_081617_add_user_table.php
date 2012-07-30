<?php

class m120510_081617_add_user_table extends UserDbMigration
{
	public function up()
	{
        $this->createTable('user', array(
            'id'   => 'pk',
            'name' => 'string NOT NULL',
            'salt' => 'string NOT NULL',
            'password' => 'string NOT NULL',
            'email' => 'string NOT NULL',
            'requireNewPassword' => 'boolean'
        ));
	}

	public function down()
	{
		echo $this->dropTable('user');
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