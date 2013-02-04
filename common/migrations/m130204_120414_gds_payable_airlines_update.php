<?php
/**
 Обновление признака возможности оплаты через ГДС для некоторых АК
 */
class m130204_120414_gds_payable_airlines_update extends CDbMigration
{
	public function up()
	{
        $this->execute("UPDATE airline SET payableViaSabre=0 , payableViaGalileo=0 WHERE code IN ('UT','QU','UR','S7','HR')");
	}

	public function down()
	{
		echo "m130204_120414_gds_payable_airlines_update does not support migration down.\n";
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