<?php

class m120514_111357_add_benchmark_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('benchmark', array(
        	'id'=>'pk',
        	'url'=>'text',
        	'route'=>'text',
        	'params'=>'text',
        	'timeAdded'=>'datetime'
        ));
	}

	public function down()
	{
        $this->delete('benchmark');
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