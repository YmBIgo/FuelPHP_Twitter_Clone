<?php

/**
 * Test For Tweet Controller
 *
 * @group ControllerTweet
 * 
 * run with below command
 * 
 * < php oil test --group=ControllerTweet >
 * 
 * You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.
 */

use \Model\User;
use \Model\Tweet;

class Test_Controller_Tweet extends TestCase {
    // show
    //
    // You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.

    // check whether tweet be displayed & display correct user 
    public function test_show_status_and_tweet_success() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // display /tweets/show/$tweet_id
        $request_url = "http://localhost:8081/tweets/show/".$tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("GET");
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $tweet_username = $array_body["body"]["div"]["div"][1]["h5"];
        $tweet_content = $array_body["body"]["div"]["div"][1]["p"];
        $this->assertSame($tweet_username, "UserName : ");
        $this->assertSame($tweet_content, "Content : test tweet1");
    }
    // check whether tweet that is not exists would't be displayed
    public function test_show_tweet_fail() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // display /tweets/show/$tweet_id+10
        $wrong_tweet_id = $tweet_id + 10;
        $request_url = "http://localhost:8081/tweets/show/".$wrong_tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("GET");
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $tweet_not_found_title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($tweet_not_found_title, "Tweet Not Found...");
    }

    // index
    //
    // You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.

    // check whether tweet would be displayed
    public function test_index_status_and_tweets_success1() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        //
        $request = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $tweets_html = $array_body["body"]["div"]["div"][1]["div"];
        $this->assertSame(count($tweets_html), 2);
        $this->assertSame($tweets_html[0]["p"]["a"], "test tweet2");
        $this->assertSame($tweets_html[1]["p"]["a"], "test tweet1");
    }
    // check whether tweet list would not displayed when insertion fails with invalid cookie (this test is not for testing inner work of \Model\Tweet)
    public function test_index_tweets_fails() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $wrong_cookie = $user1_cookie."_test_fail";
        $wrong_tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $wrong_cookie);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        //
        $request = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $tweets_html = $array_body["body"]["div"]["div"][1]["div"];
        // bit strange implementation
        // Because $array_body["body"]["div"]["div"][1]["div"] only has 1 div element, they return inner elements.
        $this->assertSame(count($tweets_html), 3);
    }
    // check whether tweet list would be displayed when 2 people tweets.
    public function test_index_tweets_success2() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $tweet3_id = Tweet::insertTweet("test tweet3", $user2_id, $user2_cookie);
        $tweet4_id = Tweet::insertTweet("test tweet4", $user2_id, $user2_cookie);
        //
        $request = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $tweets_html = $array_body["body"]["div"]["div"][1]["div"];
        $this->assertSame(count($tweets_html), 4);
        $this->assertSame($tweets_html[0]["p"]["a"], "test tweet4");
        $this->assertSame($tweets_html[2]["p"]["a"], "test tweet2");
    }
    // check whether tweet list would be displayed when 3 people tweets.
    public function test_index_tweets_success3() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        [$user3_id, $user3_cookie] = User::insertUser("hoge3@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $tweet3_id = Tweet::insertTweet("test tweet3", $user2_id, $user2_cookie);
        $tweet4_id = Tweet::insertTweet("test tweet4", $user2_id, $user2_cookie);
        $tweet5_id = Tweet::insertTweet("test tweet5", $user3_id, $user3_cookie);
        $tweet6_id = Tweet::insertTweet("test tweet6", $user3_id, $user3_cookie);
        //
        $request = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $tweets_html = $array_body["body"]["div"]["div"][1]["div"];
        $this->assertSame(count($tweets_html), 6);
        $this->assertSame($tweets_html[0]["p"]["a"], "test tweet6");
        $this->assertSame($tweets_html[3]["p"]["a"], "test tweet3");
        $this->assertSame($tweets_html[4]["p"]["a"], "test tweet2");
    }

    // new
    //
    // You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.

    // check whether valid cookie user can see tweet new & get token
    public function test_new_status_and_display_success() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        //
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        //
        $request = Request::forge("http://localhost:8081/tweets/new", "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $textarea_input = $array_body["body"]["div"]["div"][1]["div"]["form"]["textarea"];
        $textarea_name = $textarea_input["@attributes"]["name"];
        $this->assertSame($textarea_name, "content");
        $token_input = $array_body["body"]["div"]["div"][1]["div"]["form"]["input"][0];
        $token_name = $token_input["@attributes"]["name"];
        $token_value = $token_input["@attributes"]["value"];
        $this->assertSame($token_name, "fuel_csrf_token");
        $this->assertSame($token_value == false, false);
    }
    // check whether invalid cookie user cannot see tweet new
    public function test_new_display_fail_for_invalid_cookie() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        // 
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie."_test_fail";
        $cookie["user_id"] = $user1_id;
        //
        $request = Request::forge("http://localhost:8081/tweets/new", "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "You should login first to enter your tweet...");
    }
    // check whether invalid user id user cannot see tweet new
    public function test_new_display_fail_for_invalie_userid() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        // 
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id + 10;
        //
        $request = Request::forge("http://localhost:8081/tweets/new", "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "You should login first to enter your tweet...");
    }
    // check whether user without cookie cannot see tweet new
    public function test_new_display_fail_for_non_cookie_user() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        //
        $request = Request::forge("http://localhost:8081/tweets/new", "curl")->set_method("GET");
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "You should login first to enter your tweet...");
    }

    // create
    //
    // You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.

    // check whether valid user can create tweet
    //
    // try to check csrf by getting fuel_csrf_token from /tweets/new
    //   -> it is hard to immitate cookie on curl ... 
    public function test_create_status_and_create_success() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $old_tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // setting
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        $post = array();
        $post["content"] = "test tweet2";
        // firstly get /tweets/new csrf
        /*
        $request = Request::forge("http://localhost:8081/tweets/new", "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $request->set_option(CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36");
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $csrf_input = $array_body["body"]["div"]["div"][1]["div"]["form"]["input"][0];
        $csrf_name = $csrf_input["@attributes"]["name"];
        $csrf_token = $csrf_input["@attributes"]["value"];
        $this->assertSame($csrf_name, "fuel_csrf_token");
        $this->assertSame($csrf_token == false, false);
        $post[$csrf_name] = $csrf_token;
        */
        // get /tweets/create
        $request = Request::forge("http://localhost:8081/tweets/create", "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $request->set_params($post);
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Successfully create your tweet!");
        // check tweet count in \Model\Tweet
        $tweets = Tweet::fetchAll();
        $this->assertSame(count($tweets), 2);
        // check /tweets/index
        // should not use after implmenting following & followed
        $request2 = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request2->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response2 = $request2->execute()->response();
        $array_body2 = Format::forge($response2->body, "xml")->to_array();
        $tweets = $array_body2["body"]["div"]["div"][1]["div"];
        $this->assertSame(count($tweets), 2);
    }
    // check whether invalid cookie user display fail
    public function test_create_display_fail_for_invalid_cookie() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $old_tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // setting
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie."test_fail";
        $cookie["user_id"] = $user1_id;
        $post = array();
        $post["content"] = "test tweet2";
        //
        // get /tweets/create
        $request = Request::forge("http://localhost:8081/tweets/create", "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $request->set_params($post);
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // get html
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "You should login first to create your tweet...");
    }
    // check whether invalid user id user display fail
    public function test_create_display_fail_for_invalid_userid() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $old_tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // setting
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id + 10;
        $post = array();
        $post["content"] = "test tweet2";
        //
        // get /tweets/create
        $request = Request::forge("http://localhost:8081/tweets/create", "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $request->set_params($post);
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // get html
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "You should login first to create your tweet...");
    }
    // check whether non cookie user display fail
    public function test_create_display_fail_for_non_cookie_user() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $old_tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // setting
        $post = array();
        $post["content"] = "test tweet2";
        //
        // get /tweets/create
        $request = Request::forge("http://localhost:8081/tweets/create", "curl")->set_method("POST");
        $request->set_params($post);
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // get html
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "You should login first to create your tweet...");
    }
    // check whether tweet longger than 140 fail
    public function test_create_display_fail_for_more_than_140() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $old_tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // setting
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        $post = array();
        $post["content"] = "test tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweettest tweet2";
        //
        // get /tweets/create
        $request = Request::forge("http://localhost:8081/tweets/create", "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $request->set_params($post);
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // get html
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Fail to create tweet.");
    }
    // check whether tweet with 0 text fail
    public function test_create_display_fail_for_0_text() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $old_tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // setting
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        $post = array();
        $post["content"] = "";
        //
        // get /tweets/create
        $request = Request::forge("http://localhost:8081/tweets/create", "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $request->set_params($post);
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // get html
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Fail to create tweet.");
    }
}