<?php

class m120521_132749_alter_flight_cache_table extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('flight_cache','cacheType');
        $this->addColumn('flight_cache','isBestTime','boolean DEFAULT 0');
        $this->addColumn('flight_cache','isBestPrice','boolean DEFAULT 0');
        $this->addColumn('flight_cache','isOptimal','boolean DEFAULT 0');

        $this->createIndex('time_from', 'flight_cache', 'departureDate');
        $this->createIndex('time_return', 'flight_cache', 'returnDate');

        $this->createIndex('is_fastest', 'flight_cache', 'isBestTime');
        $this->createIndex('is_cheapest', 'flight_cache', 'isBestPrice');
        $this->createIndex('is_optimal', 'flight_cache', 'isOptimal');
	}

	public function down()
	{
		echo "m120521_132749_alter_flight_cache_table does not support migration down.\n";
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