<?php

class m120822_124123_add_cases_to_city extends CDbMigration
{
	public function up()
	{
        $this->addColumn('city','caseNom','varchar(100)');
        $this->addColumn('city','caseGen','varchar(100)');
        $this->addColumn('city','caseDat','varchar(100)');
        $this->addColumn('city','caseAcc','varchar(100)');
        $this->addColumn('city','caseIns','varchar(100)');
        $this->addColumn('city','casePre','varchar(100)');
	}

	public function down()
	{
		echo "m120822_124123_add_cases_to_city does not support migration down.\n";
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