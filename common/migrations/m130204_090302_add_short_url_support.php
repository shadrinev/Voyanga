<?php

class m130204_090302_add_short_url_support extends CDbMigration
{
	public function up()
	{
        $this->execute("CREATE TABLE `short_url` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `full_url` varchar(2048) NOT NULL,
              `short_url` varchar(128) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
	}

	public function down()
	{
		echo "m130204_090302_add_short_url_support does not support migration down.\n";
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