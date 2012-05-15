<?php

class m120515_075518_add_event_category_table extends CDbMigration
{
	public function up()
	{
        $sql = "CREATE TABLE `event_category` (
          `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          `root` INT(10) UNSIGNED DEFAULT NULL,
          `title` VARCHAR(255),
          `lft` INT(10) UNSIGNED NOT NULL,
          `rgt` INT(10) UNSIGNED NOT NULL,
          `level` SMALLINT(5) UNSIGNED NOT NULL,
          PRIMARY KEY (`id`),
          KEY `root` (`root`),
          KEY `lft` (`lft`),
          KEY `rgt` (`rgt`),
          KEY `level` (`level`)
        );";
        $this->execute($sql);
	}

	public function down()
	{
		$this->dropTable('event_category');
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