<?php

use \Model\User;
use \Model\Tweet;

/**
 * Test For Tweet Model
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
    // check whether tweet insetion not work for text 0
    public function test_tweet_insertion6() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        //
        $tweet_id = Tweet::insertTweet("", $user1_id, $user1_cookie);
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
        $this->assertSame($all_tweets[0]["content"], "test tweet");
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
        $tweet3_id = Tweet::insertTweet("test tweet3", $user2_id, $user2_cookie);
        $this->assertSame($tweet1_id == false, false);
        $this->assertSame($tweet2_id == false, false);
        $this->assertSame($tweet3_id == false, false);
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 3);
        $this->assertSame($all_tweets[0]["content"], "test tweet3");
        $this->assertSame($all_tweets[1]["content"], "test tweet2");
        $this->assertSame($all_tweets[2]["content"], "test tweet");
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
        $this->assertSame($all_tweets[0]["content"], "test tweet5");
        $this->assertSame($all_tweets[2]["content"], "test tweet3");
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

    // < Delete check >
    //
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

    // <Retweet check>
    //
    // check whether retweet success
    public function test_retweet_success() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $retweet_id = Tweet::retweetTweet($tweet1_id, $user2_id, $user2_cookie);
        $original_tweet = Tweet::fetchById($tweet1_id)[0];
        $retweet = Tweet::fetchById($retweet_id)[0];
        $this->assertSame($retweet_id == false, false);
        $this->assertSame($retweet["user_id"], $user2_id);
        $this->assertSame($retweet["is_retweet"], $tweet1_id);
        $this->assertSame($original_tweet["content"], $retweet["content"]);
        $user1_retweet = Tweet::fetchRetweetByTweetIdAndUserId($tweet1_id, $user2_id);
        $this->assertSame(count($user1_retweet), 1);
        $this->assertSame($user1_retweet[0]["content"], $original_tweet["content"]);
        $user1_retweets = Tweet::fetchRetweetByTweetId($tweet1_id);
        $this->assertSame(count($user1_retweets), 1);
        $this->assertSame($user1_retweets[0]["content"], $original_tweet["content"]);
    }
    // check whether invalid cookie user fail to retweet
    public function test_invalid_cookie_user_fail_to_retweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $wrong_cookie_user2 = $user2_cookie."_test_fail";
        $retweet_id = Tweet::retweetTweet($tweet1_id, $user2_id, $wrong_cookie_user2);
        $this->assertSame($retweet_id, false);
    }
    // check whether invalid userid user fail to retweet
    public function test_invalid_userid_user_fail_to_retweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $wrong_user2_id = $user2_id + 10;
        $retweet_id = Tweet::retweetTweet($tweet1_id, $wrong_user2_id, $user2_cookie);
        $this->assertSame($retweet_id, false);
    }
    // check whether non cookie user fail to retweet
    public function test_non_cookie_user_fail_to_retweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $retweet_id = Tweet::retweetTweet($tweet1_id, null, null);
        $this->assertSame($retweet_id, false);
    }
    // check whether already retweeted tweet cannot retweet
    public function test_already_retweeted_tweet_fail_to_retweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $retweet_id = Tweet::retweetTweet($tweet1_id, $user2_id, $user2_cookie);
        $retweet = Tweet::fetchById($retweet_id)[0];
        $this->assertSame($retweet_id == false, false);
        $this->assertSame($retweet["user_id"], $user2_id);
        $this->assertSame($retweet["is_retweet"], $tweet1_id);
        $retweet2_id = Tweet::retweetTweet($tweet1_id, $user2_id, $user2_cookie);
        $this->assertSame($retweet2_id, false);
    }
    // check whether non exist tweet cannot retweet
    public function test_non_exist_tweet_cannot_retweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        //
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $new_tweet_id = $tweet1_id + 10;
        $retweet_id = Tweet::retweetTweet($new_tweet_id, $user2_id, $user2_cookie);
        $this->assertSame($retweet_id, false);
    }
    // check whether 2 people retweet return 2 results for fetchRetweetByTweetId
    public function test_2people_retweet_return_2results() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // user
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        // tweet
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $original_tweet = Tweet::fetchById($tweet1_id)[0];
        // retweet
        $retweet1_id = Tweet::retweetTweet($tweet1_id, $user1_id, $user1_cookie);
        $retweet1 = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_id == false, false);
        $this->assertSame($retweet1["content"], $original_tweet["content"]);
        $retweet2_id = Tweet::retweetTweet($tweet1_id, $user2_id, $user2_cookie);
        $retweet2 = Tweet::fetchById($retweet2_id)[0];
        $this->assertSame($retweet2_id == false, false);
        $this->assertSame($retweet2["content"], $original_tweet["content"]);
        $tweet1_retweets = Tweet::fetchRetweetByTweetId($tweet1_id);
        $this->assertSame(count($tweet1_retweets), 2);
        $this->assertSame($tweet1_retweets[0]["content"], $original_tweet["content"]);
        $this->assertSame($tweet1_retweets[1]["content"], $original_tweet["content"]);
    }
    // check whether 3 people retweet return 3 results for fetchRetweetByTweetId
    public function test_3people_retweet_return_3results() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        [$user3_id, $user3_cookie] = User::insertUser("hoge3@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $original_tweet = Tweet::fetchById($tweet1_id)[0];
        // retweet
        $retweet1_id = Tweet::retweetTweet($tweet1_id, $user1_id, $user1_cookie);
        $this->assertSame($retweet1_id == false, false);
        $retweet2_id = Tweet::retweetTweet($tweet1_id, $user2_id, $user2_cookie);
        $this->assertSame($retweet2_id == false, false);
        $retweet3_id = Tweet::retweetTweet($tweet1_id, $user3_id, $user3_cookie);
        $this->assertSame($retweet3_id == false, false);
        $tweet1_retweets = Tweet::fetchRetweetByTweetId($tweet1_id);
        $this->assertSame(count($tweet1_retweets), 3);
        $this->assertSame($tweet1_retweets[0]["content"], $original_tweet["content"]);
        $this->assertSame($tweet1_retweets[1]["content"], $original_tweet["content"]);
        $this->assertSame($tweet1_retweets[2]["content"], $original_tweet["content"]);
        $this->assertSame($tweet1_retweets[0]["user_id"], $user3_id);
        $this->assertSame($tweet1_retweets[1]["user_id"], $user2_id);
        $this->assertSame($tweet1_retweets[2]["user_id"], $user1_id);
    }

    // <Unretweet check>
    //
    // check whether unretweet success
    public function test_unretweet_success() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $original_tweet = Tweet::fetchById($tweet1_id)[0];
        // retweet
        $retweet1_id = Tweet::retweetTweet($tweet1_id, $user1_id, $user1_cookie);
        $retweet1 = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_id == false, false);
        $this->assertSame($retweet1["content"], $original_tweet["content"]);
        // unretweet
        $unretweet_result = Tweet::unretweetTweet($retweet1_id, $user1_id, $user1_cookie);
        $this->assertSame($unretweet_result, true);
        $retweet1_after_unretweet = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_after_unretweet, false);
    }
    // check whether invalid cookie user fail unretweet
    public function test_invalid_cookie_user_fail_unretweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $original_tweet = Tweet::fetchById($tweet1_id)[0];
        // retweet
        $retweet1_id = Tweet::retweetTweet($tweet1_id, $user1_id, $user1_cookie);
        $retweet1 = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_id == false, false);
        $this->assertSame($retweet1["content"], $original_tweet["content"]);
        // unretweet
        $invalid_cookie = $user1_cookie."_test_fail";
        $unretweet_result = Tweet::unretweetTweet($retweet1_id, $user1_id, $invalid_cookie);
        $this->assertSame($unretweet_result, false);
        $retweet1_after_unretweet = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_after_unretweet == false, false);
        $this->assertSame($retweet1_after_unretweet["content"], $original_tweet["content"]);
    }
    // check whether invalid userid user fail unretweet
    public function test_invalid_userid_user_fail_unretweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $original_tweet = Tweet::fetchById($tweet1_id)[0];
        // retweet
        $retweet1_id = Tweet::retweetTweet($tweet1_id, $user1_id, $user1_cookie);
        $retweet1 = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_id == false, false);
        $this->assertSame($retweet1["content"], $original_tweet["content"]);
        // unretweet
        $invalid_user = $user1_id + 10;
        $unretweet_result = Tweet::unretweetTweet($retweet1_id, $invalid_user, $user1_cookie);
        $this->assertSame($unretweet_result, false);
        $retweet1_after_unretweet = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_after_unretweet == false, false);
        $this->assertSame($retweet1_after_unretweet["content"], $original_tweet["content"]);
    }
    // check whether non cookie user fail retweet
    public function test_non_cookie_user_fail_unretweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $original_tweet = Tweet::fetchById($tweet1_id)[0];
        // retweet
        $retweet1_id = Tweet::retweetTweet($tweet1_id, $user1_id, $user1_cookie);
        $retweet1 = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_id == false, false);
        $this->assertSame($retweet1["content"], $original_tweet["content"]);
        // unretweet
        $invalid_user = $user1_id + 10;
        $unretweet_result = Tweet::unretweetTweet($retweet1_id, null, null);
        $this->assertSame($unretweet_result, false);
        $retweet1_after_unretweet = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_after_unretweet == false, false);
        $this->assertSame($retweet1_after_unretweet["content"], $original_tweet["content"]);
    }
    // check whether non exist retweet cannot unretweet
    public function test_non_exist_tweet_cannot_unretweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $original_tweet = Tweet::fetchById($tweet1_id)[0];
        // retweet
        $retweet1_id = Tweet::retweetTweet($tweet1_id, $user1_id, $user1_cookie);
        $retweet1 = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_id == false, false);
        $this->assertSame($retweet1["content"], $original_tweet["content"]);
        // unretweet
        $invalid_retweet1_id = $retweet1_id + 1;
        $unretweet_result = Tweet::unretweetTweet($invalid_retweet1_id, $user1_id, $user1_cookie);
        $this->assertSame($unretweet_result, false);
        $retweet1_after_unretweet = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_after_unretweet == false, false);
        $this->assertSame($retweet1_after_unretweet["content"], $original_tweet["content"]);
    }
    // check whether normal tweet cannot unretweet
    public function test_normal_tweet_cannot_unretweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $original_tweet = Tweet::fetchById($tweet1_id)[0];
        // retweet
        $retweet1_id = Tweet::retweetTweet($tweet1_id, $user1_id, $user1_cookie);
        $retweet1 = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_id == false, false);
        $this->assertSame($retweet1["content"], $original_tweet["content"]);
        // unretweet
        $invalid_retweet1_id = $retweet1_id + 1;
        $unretweet_result = Tweet::unretweetTweet($tweet2_id, $user1_id, $user1_cookie);
        $this->assertSame($unretweet_result, false);
        $retweet1_after_unretweet = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_after_unretweet == false, false);
        $this->assertSame($retweet1_after_unretweet["content"], $original_tweet["content"]);
    }
    // check whether not your retweet cannot unretweet
    public function test_not_your_retweet_cannot_unretweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $original_tweet = Tweet::fetchById($tweet1_id)[0];
        $original_tweet2 = Tweet::fetchById($tweet2_id)[0];
        // retweet
        $retweet1_id = Tweet::retweetTweet($tweet1_id, $user1_id, $user1_cookie);
        $retweet1 = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_id == false, false);
        $this->assertSame($retweet1["content"], $original_tweet["content"]);
        $retweet2_id = Tweet::retweetTweet($tweet2_id, $user2_id, $user2_cookie);
        $retweet2 = Tweet::fetchById($retweet2_id)[0];
        $this->assertSame($retweet2_id == false, false);
        $this->assertSame($retweet2["content"], $original_tweet2["content"]);
        // unretweet
        $invalid_retweet1_id = $retweet1_id + 1;
        $unretweet_result = Tweet::unretweetTweet($tweet1_id, $user2_id, $user2_cookie);
        $this->assertSame($unretweet_result, false);
        $retweet1_after_unretweet = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_after_unretweet == false, false);
        $this->assertSame($retweet1_after_unretweet["content"], $original_tweet["content"]);
    }
    // check whether already unretweet tweet cannot unretweet
    public function test_already_unretweet_tweet_cannot_unretweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $original_tweet = Tweet::fetchById($tweet1_id)[0];
        // retweet
        $retweet1_id = Tweet::retweetTweet($tweet1_id, $user1_id, $user1_cookie);
        $retweet1 = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_id == false, false);
        $this->assertSame($retweet1["content"], $original_tweet["content"]);
        // unretweet
        $invalid_retweet1_id = $retweet1_id + 1;
        $unretweet_result = Tweet::unretweetTweet($retweet1_id, $user1_id, $user1_cookie);
        $this->assertSame($unretweet_result, true);
        $retweet1_after_unretweet = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_after_unretweet, false);
        $unretweet_result2 = Tweet::unretweetTweet($retweet1_id, $user1_id, $user1_cookie);
        $this->assertSame($unretweet_result2, false);
    }
    // check for untweet after 3 people retweet 
    public function test_for_unretweet_after_3people_retweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        [$user3_id, $user3_cookie] = User::insertUser("hoge3@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $original_tweet = Tweet::fetchById($tweet1_id)[0];
        // retweet
        $retweet1_id = Tweet::retweetTweet($tweet1_id, $user1_id, $user1_cookie);
        $retweet1 = Tweet::fetchById($retweet1_id)[0];
        $this->assertSame($retweet1_id == false, false);
        $this->assertSame($retweet1["content"], $original_tweet["content"]);
        $retweet2_id = Tweet::retweetTweet($tweet1_id, $user2_id, $user2_cookie);
        $retweet2 = Tweet::fetchById($retweet2_id)[0];
        $this->assertSame($retweet2_id == false, false);
        $this->assertSame($retweet2["content"], $original_tweet["content"]);
        $retweet3_id = Tweet::retweetTweet($tweet1_id, $user3_id, $user3_cookie);
        $retweet3 = Tweet::fetchById($retweet3_id)[0];
        $this->assertSame($retweet3_id == false, false);
        $this->assertSame($retweet3["content"], $original_tweet["content"]);
        // unretweet
        $unretweet1_result = Tweet::unretweetTweet($retweet1_id, $user1_id, $user1_cookie);
        $this->assertSame($unretweet1_result == false, false);
        $retweets = Tweet::fetchRetweetByTweetId($tweet1_id);
        $this->assertSame(count($retweets), 2);
        $this->assertSame($retweets[0]["user_id"], $user3_id);
        $this->assertSame($retweets[1]["user_id"], $user2_id);
    }

    // < Reply check > 
    //
    // check whether success reply
    public function test_reply_success1() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // reply
        $reply_tweet_id = Tweet::replyTweet($tweet1_id, "test reply1", $user1_id, $user1_cookie);
        $this->assertSame($reply_tweet_id == false, false);
        // fetch tweet data
        $reply_tweet = Tweet::fetchById($reply_tweet_id)[0];
        $this->assertSame($reply_tweet["content"], "test reply1");
        $this->assertSame($reply_tweet["user_id"], $user1_id);
        // fetch from fetchReplyByTweetId
        $reply_tweets = Tweet::fetchReplyByTweetId($tweet1_id);
        $this->assertSame($reply_tweets[0]["content"], "test reply1");
        $this->assertSame($reply_tweets[0]["user_id"], $user1_id);
        $this->assertSame($reply_tweet["id"], $reply_tweets[0]["id"]);
    }
    // check whether invalid cookie user fail reply
    public function test_reply_fail1_for_invalid_cookie_user() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $invalid_cookie = $user1_cookie."_test_fail";
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // reply
        $reply_tweet_id = Tweet::replyTweet($tweet1_id, "test reply1", $user1_id, $invalid_cookie);
        $this->assertSame($reply_tweet_id, false);
        // fetch from fetchReplyByTweetId
        $reply_tweets = Tweet::fetchReplyByTweetId($tweet1_id);
        $this->assertSame(count($reply_tweets), 0);
        // fetch all
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 1);
    }
    // check whether invalid userid user fail reply
    public function test_reply_fail2_for_invalid_userid_user() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $invalid_userid = $user1_id + 10;
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // reply
        $reply_tweet_id = Tweet::replyTweet($tweet1_id, "test reply1", $invalid_userid, $user1_cookie);
        $this->assertSame($reply_tweet_id, false);
        // fetch from fetchReplyByTweetId
        $reply_tweets = Tweet::fetchReplyByTweetId($tweet1_id);
        $this->assertSame(count($reply_tweets), 0);
        // fetch all
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 1);
    }
    // check whether non cookie user fail reply
    public function test_reply_fail3_for_non_cookie_user() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // reply
        $reply_tweet_id = Tweet::replyTweet($tweet1_id, "test reply1", null, null);
        $this->assertSame($reply_tweet_id, false);
        // fetch from fetchReplyByTweetId
        $reply_tweets = Tweet::fetchReplyByTweetId($tweet1_id);
        $this->assertSame(count($reply_tweets), 0);
        // fetch all
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 1);
    }
    // check whether non exist tweet cannot reply
    public function test_reply_fail4_for_non_exists_tweet() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $invalid_tweet_id = $tweet1_id + 10;
        // reply
        $reply_tweet_id = Tweet::replyTweet($invalid_tweet_id, "test reply1", $user1_id, $user1_cookie);
        $this->assertSame($reply_tweet_id, false);
        // fetch from fetchReplyByTweetId
        $reply_tweets = Tweet::fetchReplyByTweetId($tweet1_id);
        $this->assertSame(count($reply_tweets), 0);
        // fetch all
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 1);
    }
    // check whether longer than 140 reply fail
    public function test_reply_longer_than140_fail() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // reply
        $reply_tweet_id = Tweet::replyTweet($tweet1_id, "test replytest replytest replytest replytest replytest replytest replytest replytest replytest replytest replytest replytest replytest reply1", $user1_id, $user1_cookie);
        $this->assertSame($reply_tweet_id, false);
        // fetch from fetchReplyByTweetId
        $reply_tweets = Tweet::fetchReplyByTweetId($tweet1_id);
        $this->assertSame(count($reply_tweets), 0);
        // fetch all
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 1);
    }
    // check whether blank reply fail
    public function test_reply_blank_fail() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // reply
        $reply_tweet_id = Tweet::replyTweet($tweet1_id, "", $user1_id, $user1_cookie);
        $this->assertSame($reply_tweet_id, false);
        // fetch from fetchReplyByTweetId
        $reply_tweets = Tweet::fetchReplyByTweetId($tweet1_id);
        $this->assertSame(count($reply_tweets), 0);
        // fetch all
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 1);
    }
    // check whether reply is replyable
    public function test_reply_success2_for_reply_reply() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // reply
        $reply_tweet1_id = Tweet::replyTweet($tweet1_id, "test reply1", $user1_id, $user1_cookie);
        $this->assertSame($reply_tweet1_id == false, false);
        $reply_tweet2_id = Tweet::replyTweet($reply_tweet1_id, "test reply1", $user1_id, $user1_cookie);
        $this->assertSame($reply_tweet2_id == false, false);
        // fetch from fetchReplyByTweetId
        $reply_tweets = Tweet::fetchReplyByTweetId($tweet1_id);
        $this->assertSame(count($reply_tweets), 1);
        $reply_tweets = Tweet::fetchReplyByTweetId($reply_tweet1_id);
        $this->assertSame(count($reply_tweets), 1);
        // fetch all
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 3);
        $this->assertSame($all_tweets[0]["is_reply"], $reply_tweet1_id);
        $this->assertSame($all_tweets[1]["is_reply"], $tweet1_id);
        $this->assertSame((int)$all_tweets[2]["is_reply"], 0);
    }
    // check whether retweet is replyable
    public function test_reply_success3_for_retweet_reply() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // retweet
        $retweet_id = Tweet::retweetTweet($tweet1_id, $user1_id, $user1_cookie);
        // reply
        $reply_tweet1_id = Tweet::replyTweet($retweet_id, "test reply1", $user1_id, $user1_cookie);
        $this->assertSame($reply_tweet1_id == false, false);
        // fetch from fetchReplyByTweetId
        $reply_tweets = Tweet::fetchReplyByTweetId($retweet_id);
        $this->assertSame(count($reply_tweets), 1);
        // fetch all
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 3);
        $this->assertSame($all_tweets[0]["is_reply"], $retweet_id);
        $this->assertSame((int)$all_tweets[1]["is_reply"], 0);
        $this->assertSame((int)$all_tweets[2]["is_reply"], 0);
    }
    // check 2 people reply work
    public function test_reply_success4_for_2people() {
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        // users
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        // tweets
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet1", $user2_id, $user2_cookie);
        // reply 1 & 2
        $reply_tweet1_id = Tweet::replyTweet($tweet1_id, "test reply1", $user1_id, $user1_cookie);
        $this->assertSame($reply_tweet1_id == false, false);
        $reply_tweet1_id = Tweet::replyTweet($tweet1_id, "test reply1", $user2_id, $user2_cookie);
        $this->assertSame($reply_tweet1_id == false, false);
        // fetch from fetchReplyByTweetId
        $reply_tweets = Tweet::fetchReplyByTweetId($tweet1_id);
        $this->assertSame(count($reply_tweets), 2);
        // fetch all
        $all_tweets = Tweet::fetchAll();
        $this->assertSame(count($all_tweets), 4);
        $this->assertSame($all_tweets[0]["is_reply"], $tweet1_id);
        $this->assertSame($all_tweets[1]["is_reply"], $tweet1_id);
        $this->assertSame((int)$all_tweets[2]["is_reply"], 0);
        $this->assertSame((int)$all_tweets[3]["is_reply"], 0);
        // fetch timeline
        $timeline_tweets = Tweet::fetchTimeline();
        $this->assertSame(count($timeline_tweets), 2);
        $this->assertSame((int)$timeline_tweets[0]["is_reply"], 0);
        $this->assertSame((int)$timeline_tweets[1]["is_reply"], 0);
    }
}