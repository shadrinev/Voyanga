<?php

class m120522_154537_alter_flight_cache_table extends CDbMigration
{
	public function up()
	{
        //$this->dropTable('flight_cache');
        $this->createTable('flight_cache', array(
            'from'=>'integer',
            'to'=>'integer',
            'dateFrom'=>'date',
            'dateBack'=>'date',
            'priceBestPrice'=>'integer',
            'durationBestPrice'=>'integer',
            'validatorBestPrice'=>'integer',
            'transportBestPrice'=>'integer',
            'priceBestTime'=>'integer',
            'durationBestTime'=>'integer',
            'validatorBestTime'=>'integer',
            'transportBestTime'=>'integer',
            'priceBestPriceTime'=>'integer',
            'durationBestPriceTime'=>'integer',
            'validatorBestPriceTime'=>'integer',
            'transportBestPriceTime'=>'integer',
            'PRIMARY KEY (`from`, `to`, `dateFrom`, `dateBack`)'
        ));
        $this->createIndex('dates', 'flight_cache', 'dateFrom, dateBack');
	}

	public function down()
	{
		echo "m120522_154537_alter_flight_cache_table does not support migration down.\n";
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