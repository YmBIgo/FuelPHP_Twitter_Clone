<?php

namespace Model;
use \DB;
use \Model\User;

class Tweet extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"content" => array(
			"label" => "Content",
			"data_type" => "varchar",
		),
		"user_id" => array(
			"label" => "User id",
			"data_type" => "int",
		),
		"is_reply" => array(
			"label" => "Is reply",
			"data_type" => "int",
		),
		"is_retweet" => array(
			"label" => "Is retweet",
			"data_type" => "int",
		),
		"created_at" => array(
			"label" => "Created at",
			"data_type" => "int",
		),
		"updated_at" => array(
			"label" => "Updated at",
			"data_type" => "int",
		),
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'property' => 'created_at',
			'mysql_timestamp' => true,
			'overwrite' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'property' => 'updated_at',
			'mysql_timestamp' => true,
			'overwrite' => true,
		),
	);

	protected static $_table_name = 'tweets';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
	);

	// fetch
	public static function fetchAll() {
		$query  = DB::query("SELECT * FROM tweets ORDER BY id DESC;", DB::SELECT);
		$result = $query->as_assoc()->execute();
		return $result;
	}
	public static function fetchTimeline() {
		$query = DB::select("id", "content", "user_id", "is_reply", "is_retweet", "created_at", "updated_at")->from("tweets");
		$query->where_open()->where("is_reply", 0)->where_close();
		$query->order_by("id", "desc");
		$result = $query->as_assoc()->execute();
		return $result;
	}
	public static function fetchById($id) {
		$query = DB::select("id", "content", "user_id", "is_reply", "is_retweet", "created_at", "updated_at")->from("tweets");
		$query->where("id", $id);
		$result = $query->as_assoc()->execute();
		return $result;
	}
	public static function fetchByUserId($user_id) {
		$query = DB::select("id", "content", "user_id", "is_reply", "is_retweet", "created_at", "updated_at")->from("tweets");
		$query->where("user_id", $user_id);
		$query->order_by("id", "desc");
		$result = $query->as_assoc()->execute();
		return $result;
	}
	// get retweet by retweet tweet id and user id
	public static function fetchRetweetByTweetIdAndUserId($tweet_id, $user_id) {
		$query = DB::select("id", "content", "user_id", "is_reply", "is_retweet", "created_at", "updated_at")->from("tweets");
		$query->where_open()->where("is_retweet", $tweet_id)->and_where("user_id", $user_id)->where_close();
		$result = $query->as_assoc()->execute();
		return $result;
	}
	// get retweet by retweet tweet id
	public static function fetchRetweetByTweetId($tweet_id) {
		$query = DB::select("id", "content", "user_id", "is_reply", "is_retweet", "created_at", "updated_at")->from("tweets");
		$query->where("is_retweet", $tweet_id);
		$query->order_by("id", "desc");
		$result = $query->as_assoc()->execute();
		return $result;
	}
	// get reply by tweet id
	public static function fetchReplyByTweetId($tweet_id) {
		$query = DB::select("id", "content", "user_id", "is_reply", "is_retweet", "created_at", "updated_at")->from("tweets");
		$query->where("is_reply", $tweet_id);
		$query->order_by("id", "asc");
		$result = $query->as_assoc()->execute();
		return $result;
	}

	// insert
	public static function insertTweet($content, $user_id, $cookie) {
		if (strlen($content) > 140 || strlen($content) == 0) {
			return false;
		}
		$is_user_valid = User::fetchByCookieAndId($cookie, $user_id)[0];
		if ($is_user_valid == false) {
			return false;
		}
		list($insert_id, $rows_affected) = DB::insert("tweets")->columns(array("user_id", "content", "is_reply", "is_retweet"))->values(array($user_id, $content, 0, 0))->execute();
		if ($rows_affected > 0) {
			return $insert_id;
		} else {
			return false;
		}
	}
	// retweet
	public static function retweetTweet($tweet_id, $user_id, $cookie) {
		$is_user_valid = User::fetchByCookieAndId($cookie, $user_id)[0];
		if ($is_user_valid == false) {
			return false;
		}
		$original_tweet = Tweet::fetchById($tweet_id)[0];
		if ($original_tweet == false) {
			return false;
		}
		$is_retweet_exist = Tweet::fetchRetweetByTweetIdAndUserId($tweet_id, $is_user_valid["id"])[0];
		if ($is_retweet_exist != false) {
			return false;
		}
		$user_id = $is_user_valid["id"];
		list($insert_id, $rows_affected) = DB::insert("tweets")->columns(array("user_id", "content", "is_reply", "is_retweet"))->values(array($user_id, $original_tweet["content"], 0, $tweet_id))->execute();
		if ($rows_affected > 0) {
			return $insert_id;
		} else {
			return false;
		}
	}
	// reply
	public static function replyTweet($tweet_id, $content, $user_id, $cookie) {
		if ( strlen($content) > 140 || strlen($content) == 0 ) {
			return false;
		}
		$is_user_valid = User::fetchByCookieAndId($cookie, $user_id)[0];
		if ($is_user_valid == false) {
			return false;
		}
		$original_tweet = Tweet::fetchById($tweet_id)[0];
		if ($original_tweet == false) {
			return false;
		}
		// not only root tweet is replyable.
		/*
		if ($original_tweet["is_reply"] != 0) {
			return false;
		}
		*/
		$user_id = $is_user_valid["id"];
		list ($insert_id, $rows_affected) = DB::insert("tweets")->columns(array("user_id", "content", "is_reply", "is_retweet"))->values(array($user_id, $content, $tweet_id, 0))->execute();
		if ($rows_affected > 0) {
			return $insert_id;
		} else {
			return false;
		}
	}
	// unretweet
	//   -> implement using retweet id is valid ? or just using original tweet id.
	public static function unretweetTweet($retweet_id, $user_id, $cookie) {
		$is_user_valid = User::fetchByCookieAndId($cookie, $user_id)[0];
		if ($is_user_valid == false) {
			return false;
		}
		$original_tweet = Tweet::fetchById($retweet_id)[0];
		if ($original_tweet == false) {
			return false;
		}
		if ($original_tweet["is_retweet"] == 0) {
			return false;
		}
		$query = DB::delete("tweets");
		$query->where_open()->where("id", $retweet_id)->and_where("user_id", $user_id)->where_close();
		$query->execute();
		return true;
	}

	// delete
	public static function deleteAllTweets() {
		$query = DB::delete("tweets");
		$query->execute();
	}

}
