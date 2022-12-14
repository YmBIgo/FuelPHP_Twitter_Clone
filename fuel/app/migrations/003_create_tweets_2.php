<?php

namespace Fuel\Migrations;

class Create_tweets_2
{
	public function up()
	{
		/*
		\DBUtil::create_table('tweets', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'content' => array('constraint' => 256, 'null' => false, 'type' => 'varchar'),
			'user_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'is_reply' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'is_retweet' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'updated_at' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
		), array('id'));
		*/
	}

	public function down()
	{
		/*
		\DBUtil::drop_table('tweets');
		*/
	}
}