<?php

use \Model\User;
use \Model\Tweet;

/**
 * Test For User Model
 *
 * @group ModelTweet
 *
 * run with below command
 *
 * < php oil test --group=ModelTweet >
 */

class Test_Model_Tweet extends TestCase {

    // < Insertion check >
    // 
    // check whether tweet insertion works
    public function test_tweet_insertion1() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        //
        $tweet_id = Tweet::insertTweet("test tweet", $user1_id, $user1_cookie);
        $this->assertSame($tweet_id == false, false);
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 1);
        $this->assertSame($all_tweets[0]["content"], "test tweet");
    }
    // check whether tweet insertio not work for cookie invalid cookie user
    public function test_tweet_insertion2() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        //
        $invalid_cookie = $user1_cookie."_test_fail";
        $tweet_id = Tweet::insertTweet("test tweet", $user1_id, $invalid_cookie);
        $this->assertSame($tweet_id, false);
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 0);
    }
    // check whether tweet insertio not work for userid invalid cookie user
    public function test_tweet_insertion3() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        //
        $invalid_userid = $user1_cookie."_test_fail";
        $tweet_id = Tweet::insertTweet("test tweet", $invalid_userid, $user1_cookie);
        $this->assertSame($tweet_id, false);
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 0);
    }
    // check whether tweet insertion not work for longer than 140
    public function test_tweet_insertion4() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        //
        $tweet_id = Tweet::insertTweet("test tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweett", $user1_id, $user1_cookie);
        $this->assertSame($tweet_id, false);
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 0);
    }
    // check whether tweet insertion not work for duplicate email user
    public function test_tweet_insertion5() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        //
        $tweet_id = Tweet::insertTweet("test tweet", $user2_id, $user2_cookie);
        $this->assertSame($tweet_id, false);
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 0);
    }

    // < Fetch All check >
    //
    // check whether fetch all works for single user
    public function test_tweet_fetch_all1() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet", $user1_id, $user1_cookie);
        $this->assertSame($tweet_id == false, false);
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 1);
    }
    // check whether fetch all works across 2 users
    public function test_tweet_fetch_all2() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $tweet3_id = Tweet::insertTweet("test tweet", $user2_id, $user2_cookie);
        $this->assertSame($tweet1_id == false, false);
        $this->assertSame($tweet2_id == false, false);
        $this->assertSame($tweet3_id == false, false);
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 3);
    }
    // check whether fetch all works across 3 users
    public function test_tweet_fetch_all3() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        [$user3_id, $user3_cookie] = User::insertUser("hoge3@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $tweet3_id = Tweet::insertTweet("test tweet3", $user2_id, $user2_cookie);
        $tweet4_id = Tweet::insertTweet("test tweet4", $user2_id, $user2_cookie);
        $tweet5_id = Tweet::insertTweet("test tweet5", $user3_id, $user3_cookie);
        $this->assertSame($tweet1_id == false, false);
        $this->assertSame($tweet2_id == false, false);
        $this->assertSame($tweet3_id == false, false);
        $this->assertSame($tweet4_id == false, false);
        $this->assertSame($tweet5_id == false, false);
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 5);
    }

    // < Fetch check >
    //
    // check whether fetchById works
    public function test_tweet_fetch_by_id1() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet", $user1_id, $user1_cookie);
        $this->assertSame($tweet_id == false, false);
        $tweet = Tweet::fetchById($tweet_id);
        $this->assertSame($tweet[0]["content"], "test tweet");
        $this->assertSame($tweet[0]["user_id"], $user1_id);
    }
    // check whether fetchById not works for invalid id
    public function test_tweet_fetch_by_id_fail() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet", $user1_id, $user1_cookie);
        $this->assertSame($tweet_id == false, false);
        $new_tweet_id = $tweet_id + 1;
        $tweet = Tweet::fetchById($new_tweet_id);
        $this->assertSame($tweet[0], false);
    }
    // check whether fetchByUserId works
    public function test_tweet_fetch_by_userid1() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet", $user1_id, $user1_cookie);
        $this->assertSame($tweet_id == false, false);
        $tweets = Tweet::fetchByUserId($user1_id);
        $this->assertSame($tweets[0]["content"], "test tweet");
        $this->assertSame($tweets[0]["user_id"], $user1_id);
    }
    // check whether fetchByUserId not works for no tweet user id
    public function test_tweet_fetch_by_userid_fail1() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet", $user1_id, $user1_cookie);
        $this->assertSame($tweet_id == false, false);
        $tweets = Tweet::fetchByUserId($user2_id);
        $this->assertSame($tweets[0], false);
    }
    // check whether fetchByUserId not works for invalud user id
    public function test_tweet_fetch_by_userid_fail2() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet", $user1_id, $user1_cookie);
        $this->assertSame($tweet_id == false, false);
        $tweets = Tweet::fetchByUserId($user1_id + 1);
        $this->assertSame($tweets[0], false);
    }

    // < Delet check >
    // check whether fetch all works across 3 users
    public function test_tweet_delete_all() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        [$user3_id, $user3_cookie] = User::insertUser("hoge3@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $tweet3_id = Tweet::insertTweet("test tweet3", $user2_id, $user2_cookie);
        $tweet4_id = Tweet::insertTweet("test tweet4", $user2_id, $user2_cookie);
        $tweet5_id = Tweet::insertTweet("test tweet5", $user3_id, $user3_cookie);
        $this->assertSame($tweet1_id == false, false);
        $this->assertSame($tweet2_id == false, false);
        $this->assertSame($tweet3_id == false, false);
        $this->assertSame($tweet4_id == false, false);
        $this->assertSame($tweet5_id == false, false);
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 5);
        Tweet::deleteAllTweets();
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 0);
    }
}