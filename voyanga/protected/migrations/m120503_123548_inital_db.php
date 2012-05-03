<?php

class m120503_123548_inital_db extends CDbMigration
{
	public function safeUp()
	{
        $this->renameColumn('airport', 'localRU', 'localRu');
        $this->dropForeignKey('fk_airport_city', 'airport');
        $this->renameColumn('airport', 'city_id', 'cityId');
        $this->addForeignKey('fk_airport_city', 'airport','cityId','city','id');
        
        $this->renameColumn('city', 'local_ru', 'localRu');
        $this->renameColumn('city', 'local_en', 'localEn');
        $this->dropForeignKey('fk_city_country', 'city');
        $this->renameColumn('city', 'country_id', 'countryId');
        $this->addForeignKey('fk_city_country', 'city','countryId','country','id');
        
        $this->renameColumn('country', 'local_ru', 'localRu');
        $this->renameColumn('country', 'local_en', 'localEn');
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