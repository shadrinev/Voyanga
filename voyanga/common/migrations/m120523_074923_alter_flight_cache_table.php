<?php

class m120523_074923_alter_flight_cache_table extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('flight_cache','validatorBestTime','string');
        $this->alterColumn('flight_cache','validatorBestPrice','string');
        $this->alterColumn('flight_cache','validatorBestPriceTime','string');

        $this->alterColumn('flight_cache','transportBestPriceTime','string');
        $this->alterColumn('flight_cache','transportBestPrice','string');
        $this->alterColumn('flight_cache','transportBestTime','string');
	}

	public function down()
	{
		echo "m120523_074923_alter_flight_cache_table does not support migration down.\n";
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