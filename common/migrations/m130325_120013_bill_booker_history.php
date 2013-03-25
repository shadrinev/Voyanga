<?php

class m130325_120013_bill_booker_history extends CDbMigration
{
    public function up()
    {
        $this->createTable('bill_hotel_booking_history', array(
            'hotelBookingId' => 'integer',
            'billId' => 'integer',
            'PRIMARY KEY(hotelBookingId, billId)'
        ));
        $this->createTable('bill_flight_booking_history', array(
            'flightBookingId' => 'integer',
            'billId' => 'integer',
            'PRIMARY KEY(flightBookingId, billId)'
        ));

    }

    public function down()
    {
        echo "m130325_120013_bill_booker_history does not support migration down.\n";
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