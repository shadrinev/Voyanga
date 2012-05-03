<?php

class m120503_123548_inital_db extends CDbMigration
{
	public function up()
	{
        $sql = "CREATE  TABLE IF NOT EXISTS `airline` (
              `id` INT NOT NULL AUTO_INCREMENT ,
              `position` INT NOT NULL DEFAULT 0 ,
              `code` VARCHAR(5) NOT NULL ,
              `localRu` VARCHAR(45) NULL ,
              `sLocalEn` VARCHAR(45) NULL ,
              PRIMARY KEY (`id`) )
            ENGINE = InnoDB";
        $this->execute($sql);
	}

	public function down()
	{
		echo "m120503_123548_inital_db does not support migration down.\n";
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