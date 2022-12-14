<?php

namespace Fuel\Migrations;

class Create_tweets
{
	const TABLE_NAME = "tweets";
	public function up()
	{
		\DBUtil::create_table(self::TABLE_NAME, array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'user_id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'default' => 0, 'constraint' => 11),
			'content' => array('type' => 'varchar', 'constraint' => 256, 'null' => false),
			'is_reply' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'default' => 0, 'constraint' => 11),
			'is_retweet' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'default' => 0, 'constraint' => 11),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table(self::TABLE_NAME);
	}
}