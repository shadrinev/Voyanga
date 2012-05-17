<?php

class m120517_140432_add_tags_for_event extends CDbMigration
{
	public function up()
	{
        $this->createTable('event_tag',array(
            'id' => 'pk',
            'name' => 'string'
        ));
        $this->createTable('event_has_tag',array(
            'eventId' => 'int',
            'eventTagId' => 'int',
            'PRIMARY KEY (eventId, eventTagId)'
        ));
	}

	public function down()
	{
		$this->dropTable('event_has_tag');
        $this->dropTable('event_tag');
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