<?php

namespace Fuel\Migrations;

class Create_users
{
	const TABLE_NAME = "users";
	public function up()
	{
		\DBUtil::create_table(self::TABLE_NAME, array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'name' => array('type' => 'varchar', 'constraint' => 100, 'null' => false),
			'email' => array('type' => 'varchar', 'constraint' => 100, 'null' => false),
			'description' => array('type' => 'text', 'null' => false),
			'status' => array('type' => 'int', 'unsigned' => false, 'null' => false, 'constraint' => 3, 'default' => 0),
			'hashed_password' => array('type' => 'varchar', 'constraint' => 300),
			'password' => array('type' => 'varchar', 'constraint' => 100),
			'cookie_value' => array('type' => 'varchar', 'null' => false, 'constraint' => 300, 'default' => 'aaa'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table(self::TABLE_NAME);
	}
}