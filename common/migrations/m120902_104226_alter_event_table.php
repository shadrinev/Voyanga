<?php

class m120902_104226_alter_event_table extends CDbMigration
{
	public function up()
	{
        $sql = "ALTER TABLE `event` DROP COLUMN `cityId`, DROP INDEX `fk_event_city`, DROP FOREIGN KEY `fk_event_city`";
        $this->execute($sql);
	}

	public function down()
	{
		echo "m120902_104226_alter_event_table does not support migration down.\n";
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