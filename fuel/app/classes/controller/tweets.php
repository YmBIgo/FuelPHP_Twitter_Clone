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
		$this->template->title = 'Tweets - Show';
		$this->template->content = View::forge('tweets/show', $data);
	}

	public function action_index()
	{
		$data = array();
		$tweets = Tweet::fetchAll();
		$tweets_with_user = array();
		foreach ( $tweets as $tweet ) {
			$user = User::fetchByIdSafe($tweet["user_id"]);
			array_push($tweets_with_user, [$tweet, $user[0]]);
		}
		$data["tweets"] = $tweets_with_user;
		$data["subnav"] = array('index'=> 'active' );
		$this->template->title = 'Tweets - Index';
		$this->template->content = View::forge('tweets/index', $data);
	}

	public function action_new()
	{
		// get cookie user
		$data = array();
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user_id = Cookie::get("user_id");
		$cookie_user = User::fetchByCookieAndId($cookie_value, $cookie_user_id);
		$data["cookie_user"] = $cookie_user[0];
		if ($data["cookie_user"] != false) {
			// insert csrf data
			$token = array();
			$token['token_key'] = Config::get('security.csrf_token_key');
			$token['token'] = Security::fetch_token();
			$data["token"] = $token;
		}
		$data["subnav"] = array('new'=> 'active' );
		$this->template->title = 'Tweets - New';
		$this->template->content = View::forge('tweets/new', $data);
	}

	public function action_create()
	{
		$data = array();
		$data["subnav"] = array('create'=> 'active' );
		$this->template->title = 'Tweets - Create';
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user_id = Cookie::get("user_id");
		$post = Input::post();
		$cookie_user = User::fetchByCookieAndId($cookie_value, $cookie_user_id);
		$data["cookie_user"] = $cookie_user[0];
		if ($data["cookie_user"] == false) {
			$this->template->content = View::forge('tweets/create', $data);
			return;
		}
		// check csrf || \Fuel::$env == "test"
		if (Security::check_token() || \Fuel::$env == "test") {
			$content = $post["content"];
			$tweet_id = Tweet::insertTweet($content, $cookie_user_id, $cookie_value);
			$data["tweet_id"] = $tweet_id;
		} else {
			$data["tweet_id"] = false;
		}
		$this->template->content = View::forge('tweets/create', $data);
	}

}
