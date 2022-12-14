<?php

use \Model\User;
use \Model\Tweet;

class Controller_Tweets extends Controller_Template
{

	public function action_show()
	{
		$data = array();
		$params = Request::active()->params();
		$id = $params['id'];
		$tweet = Tweet::fetchById($id);
		$data["tweet"] = $tweet[0]; // If tweet is not exists tweet[0] always return false.
		if ( $data["tweet"] != false ) {
			$user = User::fetchByIdSafe($data["tweet"]["user_id"]);
			$data["user"] = $user[0];
		}
		$data["subnav"] = array('show'=> 'active' );
		$this->template->title = 'Tweets &raquo; Show';
		$this->template->content = View::forge('tweets/show', $data);
	}

	public function action_index()
	{
		$data = array();
		$tweets = Tweet::fetchAll();
		$tweets_with_user = array();
		foreach ( $tweets as $tweet ) {
			$user = User::fetchByIdSafe($tweet["id"]);
			array_push($tweets_with_user, [$tweet, $user[0]]);
		}
		$data["tweets"] = $tweets_with_user;
		$data["subnav"] = array('index'=> 'active' );
		$this->template->title = 'Tweets &raquo; Index';
		$this->template->content = View::forge('tweets/index', $data);
	}

	public function action_new()
	{
		$data["subnav"] = array('new'=> 'active' );
		$this->template->title = 'Tweets &raquo; New';
		$this->template->content = View::forge('tweets/new', $data);
	}

	public function action_create()
	{
		$data["subnav"] = array('create'=> 'active' );
		$this->template->title = 'Tweets &raquo; Create';
		$this->template->content = View::forge('tweets/create', $data);
	}

}
