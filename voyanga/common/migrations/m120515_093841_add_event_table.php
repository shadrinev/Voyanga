<?php

class m120515_093841_add_event_table extends CDbMigration
{
	public function safeUp()
	{
        $this->createTable('event', array(
            'id'=>'pk',
            'startDate'=>'datetime',
            'endDate'=>'datetime',
            'cityId'=>'int',
            'address'=>'string',
            'contact'=>'string',
            'status'=>'bool',
            'preview'=>'text',
            'description'=>'text'
            ),
            "ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
        );

        $this->createTable('event_link',array(
            'id'=>'pk',
            'eventId'=>'int',
            'url'=>'string',
            'title'=>'string',
            ),
            "ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
        );

        $this->createTable('event_has_category',array(
            'eventId'=>'int',
            'eventCategoryId'=>'int',
            'PRIMARY KEY (`eventId`,`eventCategoryId`)'
            ),
            "ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
        );

        $this->addForeignKey('fk_event_city','event','cityId','city','id');
        $this->addForeignKey('fk_link_event','event_link','eventId','event','id');
        $this->addForeignKey('fk_category_has_event','event_has_category','eventId','event','id');
        $this->addForeignKey('fk_event_has_category','event_has_category','eventCategoryId','event_category','id');
    }

	public function safeDown()
	{
		$this->dropTable('event_has_category');
        $this->dropTable('event_link');
        $this->dropTable('event');
		return true;
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