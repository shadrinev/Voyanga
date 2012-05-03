<?php

class m120503_123548_inital_db extends CDbMigration
{
	public function up()
	{
        $this->renameColumn('airline', 'local_ru', 'localRu');
        $this->renameColumn('airline', 'local_en', 'localEn');
        
        $this->renameColumn('airport', 'local_ru', 'localRU');
        $this->renameColumn('airport', 'local_en', 'localEn');
        $this->renameColumn('airport', 'city_id', 'cityId');
        
        $this->renameColumn('city', 'local_ru', 'localRU');
        $this->renameColumn('city', 'local_en', 'localEn');
        $this->renameColumn('city', 'country_id', 'countryId');
        
        $this->renameColumn('country', 'local_ru', 'localRU');
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