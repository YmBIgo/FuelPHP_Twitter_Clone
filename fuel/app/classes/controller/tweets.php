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
			// current user
			$cookie_value = Cookie::get('cookie_value');
			$cookie_user_id = Cookie::get("user_id");
			$cookie_user = User::fetchByCookieAndId($cookie_value, $cookie_user_id);
			$data["cookie_user"] = $cookie_user[0];
			if ( $cookie_user[0] != false ) {
				// retweet
				$is_retweet_exist = Tweet::fetchRetweetByTweetIdAndUserId($id, $cookie_user[0]["id"]);
				$data["is_retweet_exist"] = $is_retweet_exist[0];
				// insert csrf data
				$token = array();
				$token['token_key'] = Config::get('security.csrf_token_key');
				$token['token'] = Security::fetch_token();
				$data["token"] = $token;
			}
			$replies = Tweet::fetchReplyByTweetId($id);
			$user_append_replies = array();
			if ($replies[0] != false) {
				foreach($replies as $reply) {
					$reply_user = User::fetchByIdSafe($reply["user_id"])[0];
					$is_retweet_exist_for_reply = Tweet::fetchRetweetByTweetIdAndUserId($reply["id"], $cookie_user[0]["id"])[0];
					array_push($user_append_replies, [$reply, $reply_user, $is_retweet_exist_for_reply]);
				}
			}
			$data["replies"] = $user_append_replies;
		}
		$data["subnav"] = array('show'=> 'active' );
		$this->template->title = 'Tweets - Show';
		$this->template->content = View::forge('tweets/show', $data);
	}

	public function action_index()
	{
		$data = array();
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user_id = Cookie::get("user_id");
		$cookie_user = User::fetchByCookieAndId($cookie_value, $cookie_user_id);
		$data["cookie_user"] = $cookie_user[0];
		$data["subnav"] = array('index'=> 'active' );
		$this->template->title = 'Tweets - Index';
		if ( $cookie_user[0] == false ) {
			$this->template->content = View::forge('tweets/index', $data);
			return;
		}
		// csrf
		$token = array();
		$token['token_key'] = Config::get('security.csrf_token_key');
		$token['token'] = Security::fetch_token();
		$data["token"] = $token;
		// tweet
		// $tweets = Tweet::fetchAll();
		$tweets = Tweet::fetchTimeline();
		$tweets_with_user = array();
		foreach ( $tweets as $tweet ) {
			$user = User::fetchByIdSafe($tweet["user_id"]);
			$is_retweet_exist = Tweet::fetchRetweetByTweetIdAndUserId($tweet["id"], $cookie_user[0]["id"])[0];
			array_push($tweets_with_user, [$tweet, $user[0], $is_retweet_exist]);
		}
		$data["tweets"] = $tweets_with_user;
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

	public function action_retweet() {
		$data = array();
		$data["subnav"] = array();
		$this->template->title = 'Tweets - Retweet';
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user_id = Cookie::get("user_id");
		$cookie_user = User::fetchByCookieAndId($cookie_value, $cookie_user_id);
		$data["cookie_user"] = $cookie_user;
		if ( $cookie_user == false ) {
			$this->template->content = View::forge('tweets/retweet', $data);
			return;
		}
		$params = Request::active()->params();
		$tweet_id = $params['id'];
		// check csrf || \Fuel::$env == "test"
		if (Security::check_token() || \Fuel::$env == "test") {
			$retweet_id = Tweet::retweetTweet($tweet_id, $cookie_user_id, $cookie_value);
			$data["tweet_id"] = $retweet_id;
		} else {
			$data["tweet_id"] = false;
		}
		$this->template->content = View::forge('tweets/retweet', $data);
	}

	public function action_unretweet() {
		$data = array();
		$data["subnav"] = array();
		$this->template->title = 'Tweets - Unretweet';
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user_id = Cookie::get("user_id");
		$cookie_user = User::fetchByCookieAndId($cookie_value, $cookie_user_id);
		$data["cookie_user"] = $cookie_user;
		if ( $cookie_user == false ) {
			$this->template->content = View::forge('tweets/unretweet', $data);
			return;
		}
		$params = Request::active()->params();
		$tweet_id = $params['id'];
		if (Security::check_token() || \Fuel::$env == "test") {
			$retweet_id = Tweet::fetchRetweetByTweetIdAndUserId($tweet_id, $cookie_user_id)[0];
			if ( $retweet_id == false ) {
				$data["unretweer_result"] = false;
			} else {
				$unretweet_result = Tweet::unretweetTweet($retweet_id["id"], $cookie_user_id, $cookie_value);
				$data["unretweer_result"] = $unretweet_result;
			}
		} else {
			$data["unretweer_result"] = false;
		}
		$this->template->content = View::forge('tweets/unretweet', $data);
	}

	public function action_reply() {
		$data = array();
		$data["subnav"] = array();
		$this->template->title = 'Tweets - Reply';
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user_id = Cookie::get("user_id");
		$cookie_user = User::fetchByCookieAndId($cookie_value, $cookie_user_id);
		$data["cookie_user"] = $cookie_user;
		$data["tweet_id"] = false;
		if ( $cookie_user == false ) {
			$this->template->content = View::forge('tweets/reply', $data);
			return;
		}
		$params = Request::active()->params();
		$tweet_id = $params['id'];
		if (Security::check_token() || \Fuel::$env == "test") {
			$post = Input::post();
			$reply_tweet_id = Tweet::replyTweet($tweet_id, $post["content"], $cookie_user_id, $cookie_value);
			if ($reply_tweet_id != false) {
				$data["tweet_id"] = $reply_tweet_id;
			}
		}
		$this->template->content = View::forge('tweets/reply', $data);
	}

}
