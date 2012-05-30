<?php

class m120530_093522_create_tables_for_rbac extends CDbMigration
{
	public function up()
	{
        $sql = "
            drop table if exists `AuthAssignment`;
            drop table if exists `AuthItemChild`;
            drop table if exists `AuthItem`;

            create table `AuthItem`
            (
               `name`                 varchar(64) not null,
               `type`                 integer not null,
               `description`          text,
               `bizrule`              text,
               `data`                 text,
               primary key (`name`)
            ) engine InnoDB;

            create table `AuthItemChild`
            (
               `parent`               varchar(64) not null,
               `child`                varchar(64) not null,
               primary key (`parent`,`child`),
               foreign key (`parent`) references `AuthItem` (`name`) on delete cascade on update cascade,
               foreign key (`child`) references `AuthItem` (`name`) on delete cascade on update cascade
            ) engine InnoDB;

            create table `AuthAssignment`
            (
               `itemname`             varchar(64) not null,
               `userid`               varchar(64) not null,
               `bizrule`              text,
               `data`                 text,
               primary key (`itemname`,`userid`),
               foreign key (`itemname`) references `AuthItem` (`name`) on delete cascade on update cascade
            ) engine InnoDB;
            ";
        $this->execute($sql);
	}

	public function down()
	{
		echo "m120530_093522_create_tables_for_rbac does not support migration down.\n";
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