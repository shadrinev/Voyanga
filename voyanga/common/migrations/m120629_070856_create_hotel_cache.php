<?php

class m120629_070856_create_hotel_cache extends CDbMigration
{
	public function up()
	{
        $this->createTable('hotel_cache', array(
            'cityId'=>'integer',
            'dateFrom'=>'date',
            'dateTo'=>'date',
            'stars'=>'integer',
            'price'=>'float',
            'hotelId'=>'integer',
            'hotelName'=>'string',
            'createdAt'=>'datetime',
            'updatedAt'=>'datetime',
            'PRIMARY KEY (`cityId`, `dateFrom`, `dateTo`, `stars`)'
        ));
	}

	public function down()
	{
		echo "m120629_070856_create_hotel_cache does not support migration down.\n";
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