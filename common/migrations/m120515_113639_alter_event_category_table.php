<?php

class m120515_113639_alter_event_category_table extends CDbMigration
{
	public function up()
	{
        $this->addColumn('event','title','string');
	}

	public function down()
	{
		$this->dropColumn('event','title');
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