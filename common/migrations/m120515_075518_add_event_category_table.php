<?php

class m120515_075518_add_event_category_table extends CDbMigration
{
	public function up()
	{
        $sql = "CREATE TABLE `event_category` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `root` INT(11) UNSIGNED DEFAULT NULL,
          `title` VARCHAR(255),
          `lft` INT(11) UNSIGNED NOT NULL,
          `rgt` INT(11) UNSIGNED NOT NULL,
          `level` SMALLINT(5) UNSIGNED NOT NULL,
          PRIMARY KEY (`id`),
          KEY `root` (`root`),
          KEY `lft` (`lft`),
          KEY `rgt` (`rgt`),
          KEY `level` (`level`)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ;";
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