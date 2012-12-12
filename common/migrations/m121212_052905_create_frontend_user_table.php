<?php

class m121212_052905_create_frontend_user_table extends CDbMigration
{
    public function up()
    {
        $this->execute('CREATE TABLE `frontend_user` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`username` varchar(45) DEFAULT NULL,
			`password` varchar(255) DEFAULT NULL,
			`salt` varchar(255) DEFAULT NULL,
			`password_strategy` varchar(50) DEFAULT NULL,
			`requires_new_password` tinyint(1) DEFAULT NULL,
			`email` varchar(255) DEFAULT NULL,
			`login_attempts` int(11) DEFAULT NULL,
			`login_time` int(11) DEFAULT NULL,
			`login_ip` varchar(32) DEFAULT NULL,
			`validation_key` varchar(255) DEFAULT NULL,
			`create_id` int(11) DEFAULT NULL,
			`create_time` int(11) DEFAULT NULL,
			`update_id` int(11) DEFAULT NULL,
			`update_time` int(11) DEFAULT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `username` (`username`),
			UNIQUE KEY `email` (`email`)
		) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8');

        $this->addColumn('frontend_user','recover_pwd_key','varchar(32)');
        $this->addColumn('frontend_user','recover_pwd_expiration','timestamp');
    }

    public function down()
    {
        $this->dropTable('frontend_user');
    }
}