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
        $tweet_username = $array_body["body"]["div"]["div"][1]["h5"][0];
        $tweet_content = $array_body["body"]["div"]["div"][1]["p"][0];
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
        // set cookie
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        //
        $request = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $tweets_html = $array_body["body"]["div"]["div"][1]["div"];
        $this->assertSame(count($tweets_html), 2);
        $this->assertSame($tweets_html[0]["p"]["a"], "test tweet2");
        $this->assertSame($tweets_html[1]["p"]["a"], "test tweet1");
    }
    // check whether invalid cookie user cannot see tweet index
    public function test_index_invalid_user_cannot_see() {
        // initilization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        // set cookie
        $cookie = array();
        $invalid_cookie = $user1_cookie."_test_fail";
        $cookie["cookie_value"] = $invalid_cookie;
        $cookie["user_id"] = $user1_id;
        //
        $request = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $fail_html = $array_body["body"]["div"]["div"][1]["div"];
        $this->assertSame($fail_html["h5"], "You should login first to retweet ...");
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
        // set cookie
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        //
        $request = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        $tweets_content = $array_body["body"]["div"]["div"][1]["div"]["p"]["a"];
        // bit strange implementation
        // Because $array_body["body"]["div"]["div"][1]["div"] only has 1 div element, they return inner elements.
        $this->assertSame($tweets_content, "test tweet1");
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
        // set cookie
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        //
        $request = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
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
        // set cookie
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        //
        $request = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
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

    // retweet
    //
    // You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.

    // check whether retweet success
    //  -> should check @tweets/index most recent tweet & original tweet unretweet button 
    public function test_retweet_status_and_success_at_tweet_show() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // setting
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        // post /tweets/retweet/:id
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // parse data from /tweets/retweet/:id
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Successfully create your retweet!");
        // check tweets/index page
        $request2 = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request2->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response2 = $request2->execute()->response();
        $array_body2 = Format::forge($response2->body, "xml")->to_array();
        // parse data from /tweets/index
        $recent_tweet = $array_body2["body"]["div"]["div"][1]["div"][0];
        $retweet_title = $recent_tweet["p"][0]["small"];
        $this->assertSame($retweet_title, "[ Retweet Tweet ] ");
        $original_tweet = $array_body2["body"]["div"]["div"][1]["div"][1];
        $retweet_button = $original_tweet["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($retweet_button, "Unretweet");
        // check tweets/show page
        $request3 = Request::forge("http://localhost:8081/tweets/show/".$tweet_id, "curl")->set_method("GET");
        $request3->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response3 = $request3->execute()->response();
        $array_body3 = Format::forge($response3->body, "xml")->to_array();
        // parse data fron /tweets/show
        $tweet = $array_body3["body"]["div"]["div"][1];
        $tweet_retweet = $tweet["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($tweet_retweet, "Unretweet");
    }
    // check whether invalid cookie user fail retweet
    public function test_retweet_invalid_cookie_user_fail() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        // setting
        $wrong_cookie = array();
        $wrong_cookie["cookie_value"] = $user1_cookie."_test_fail";
        $wrong_cookie["user_id"] = $user1_id;
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        // post /tweets/retweet/:id
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($wrong_cookie));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // parse data from /tweets/retweet/:id
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Fail to create retweet.");
        // check tweets/index page
        $request2 = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request2->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response2 = $request2->execute()->response();
        $array_body2 = Format::forge($response2->body, "xml")->to_array();
        // parse data from /tweets/index
        $index_page_tweets = $array_body2["body"]["div"]["div"][1]["div"];
        $this->assertSame(count($index_page_tweets), 2);
        $original_tweet = $array_body2["body"]["div"]["div"][1]["div"][1];
        $retweet_button = $original_tweet["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($retweet_button, "Retweet");
    }
    // check whether invalid userid user fail retweet
    public function test_retweet_invalid_userid_user_fail() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        // setting
        $wrong_cookie = array();
        $wrong_cookie["cookie_value"] = $user1_cookie;
        $wrong_cookie["user_id"] = $user1_id + 10;
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        // post /tweets/retweet/:id
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($wrong_cookie));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // parse data from /tweets/retweet/:id
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Fail to create retweet.");
        // check tweets/index page
        $request2 = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request2->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response2 = $request2->execute()->response();
        $array_body2 = Format::forge($response2->body, "xml")->to_array();
        // parse data from /tweets/index
        $index_page_tweets = $array_body2["body"]["div"]["div"][1]["div"];
        $this->assertSame(count($index_page_tweets), 2);
        $original_tweet = $array_body2["body"]["div"]["div"][1]["div"][1];
        $retweet_button = $original_tweet["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($retweet_button, "Retweet");
    }
    // check whether no cookie user fail retweet
    public function test_retweet_non_cookie_user_fail() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        // setting
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        // post /tweets/retweet/:id
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("POST");
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // parse data from /tweets/retweet/:id
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Fail to create retweet.");
        // check tweets/index page
        $request2 = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request2->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response2 = $request2->execute()->response();
        $array_body2 = Format::forge($response2->body, "xml")->to_array();
        // parse data from /tweets/index
        $index_page_tweets = $array_body2["body"]["div"]["div"][1]["div"];
        $this->assertSame(count($index_page_tweets), 2);
        $original_tweet = $array_body2["body"]["div"]["div"][1]["div"][1];
        $retweet_button = $original_tweet["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($retweet_button, "Retweet");
    }
    // check whether can retweet other people's tweet1 (for 2 people)
    public function test_retweet_2_people() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user2_id, $user2_cookie);
        // setting
        $cookie1 = array();
        $cookie1["cookie_value"] = $user1_cookie;
        $cookie1["user_id"] = $user1_id;
        $cookie2 = array();
        $cookie2["cookie_value"] = $user2_cookie;
        $cookie2["user_id"] = $user2_id;
        // post /tweets/retweet/:id 1
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet2_id;
        $request = Request::forge($request_url, "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie1));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // parse data from /tweets/retweet/:id 1
        $success_title1 = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($success_title1, "Successfully create your retweet!");
        // post /tweets/retweet/:id 2
        $request2_url = "http://localhost:8081/tweets/retweet/".$tweet1_id;
        $request2 = Request::forge($request2_url, "curl")->set_method("POST");
        $request2->set_option(CURLOPT_COOKIE, build_cookie($cookie2));
        $response2 = $request2->execute()->response();
        $array_body2 = Format::forge($response2->body, "xml")->to_array();
        // parse data from /tweets/retweet/:id 2
        $success_title2 = $array_body2["body"]["div"]["div"][1]["h5"];
        $this->assertSame($success_title2, "Successfully create your retweet!");
        // get /tweets/index
        $request3 = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request3->set_option(CURLOPT_COOKIE, build_cookie($cookie1));
        $response3 = $request3->execute()->response();
        $array_body3 = Format::forge($response3->body, "xml")->to_array();
        // parse data from /tweets/index
        $timeline_tweets = $array_body3["body"]["div"]["div"][1]["div"];
        $this->assertSame(count($timeline_tweets), 4);
        $first_tweet = $timeline_tweets[0];
        $first_tweet_subtitle = $first_tweet["p"][0]["small"];
        $this->assertSame($first_tweet_subtitle, "[ Retweet Tweet ] ");
        $second_tweet = $timeline_tweets[1];
        $first_tweet_subtitle = $second_tweet["p"][0]["small"];
        $this->assertSame($first_tweet_subtitle, "[ Retweet Tweet ] ");
        $third_tweet = $timeline_tweets[2];
        $third_tweet_retweet_button = $third_tweet["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($third_tweet_retweet_button, "Unretweet");
        $forth_tweet = $timeline_tweets[3];
        $forth_tweet_retweet_button = $forth_tweet["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($forth_tweet_retweet_button, "Retweet");
    }
    // check whether invalid tweet id retweet fail
    public function test_retweet_invalid_tweet_id_fail() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user2_id, $user2_cookie);
        // setting
        $cookie1 = array();
        $cookie1["cookie_value"] = $user1_cookie;
        $cookie1["user_id"] = $user1_id;
        // post /tweets/retweet/:invalid_tweet_id
        $invalid_tweet_id = $tweet2_id + 10;
        $request_url = "http://localhost:8081/tweets/retweet/".$invalid_tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie1));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // parse data from /tweets/retweet/:invalid_tweet_id
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Fail to create retweet.");
        // get /tweets/index
        $request = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie1));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // parse data from /tweets/index
        $timeline_tweet = $array_body["body"]["div"]["div"][1]["div"];
        $this->assertSame(count($timeline_tweet), 2);
        $first_tweet_retweet = $timeline_tweet[0]["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($first_tweet_retweet, "Retweet");
        $second_tweet_retweet = $timeline_tweet[1]["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($second_tweet_retweet, "Retweet");
    }
    // check whether duplicate retweet fail
    public function test_retweet_duplicate_retweet_fail() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user2_id, $user2_cookie);
        // setting
        $cookie1 = array();
        $cookie1["cookie_value"] = $user1_cookie;
        $cookie1["user_id"] = $user1_id;
        // post /tweets/retweet/:id
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet2_id;
        $request = Request::forge($request_url, "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie1));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // parse data from /tweets/retweet/:invalid_tweet_id
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Successfully create your retweet!");
        // post /tweets/retweet/:id again
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet2_id;
        $request = Request::forge($request_url, "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie1));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // parse data from /tweets/retweet/:invalid_tweet_id
        $title = $array_body["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Fail to create retweet.");
        // get /tweets/index
        $request = Request::forge("http://localhost:8081/tweets/index", "curl")->set_method("GET");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie1));
        $response = $request->execute()->response();
        $array_body = Format::forge($response->body, "xml")->to_array();
        // parse data from /tweets/index
        $timeline_tweet = $array_body["body"]["div"]["div"][1]["div"];
        $this->assertSame(count($timeline_tweet), 3);
        $first_tweet_retweet = $timeline_tweet[0]["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($first_tweet_retweet, "Retweet");
        $second_tweet_retweet = $timeline_tweet[1]["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($second_tweet_retweet, "Unretweet");
        $third_tweet_retweet = $timeline_tweet[2]["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($third_tweet_retweet, "Retweet");
    }

    // retweet
    //
    // You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.

    // check whether unretweet success
    //  -> should check @tweets/index most recent tweet & original tweet retweet button 
    public function test_unretweet_status_and_success_at_tweet_show() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // setting
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        // post /tweets/retweet/:id 
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        // get /tweets/index
        $request_url2 = "http://localhost:8081/tweets/index";
        $request2 = Request::forge($request_url2, "curl")->set_method("GET");
        $request2->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response2 = $request2->execute()->response();
        $array_body2 = Format::forge($response2->body, "xml")->to_array();
        // parse data from /tweets/index
        $timeline_tweets = $array_body2["body"]["div"]["div"][1]["div"];
        $this->assertSame(count($timeline_tweets), 2);
        $first_tweet_form = $timeline_tweets[1]["div"]["div"][1]["form"];
        $first_tweet_unretweet_url = $first_tweet_form["@attributes"]["action"];
        $first_tweet_unretweet_button = $first_tweet_form["input"][1]["@attributes"]["value"];
        $this->assertSame("http://localhost:8081/tweets/unretweet/".$tweet_id, $first_tweet_unretweet_url);
        $this->assertSame($first_tweet_unretweet_button, "Unretweet");
        // get /tweets/show/:id
        $request_url3 = "http://localhost:8081/tweets/show/".$tweet_id;
        $request3 = Request::forge($request_url3, "curl")->set_method("GET");
        $request3->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response3 = $request3->execute()->response();
        $array_body3 = Format::forge($response3->body, "xml")->to_array();
        // parse data from /tweets/show/:id
        $unretweet_button = $array_body3["body"]["div"]["div"][1]["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($unretweet_button, "Unretweet");
        // post /tweets/unretweet/:id 
        $request_url4 = "http://localhost:8081/tweets/unretweet/".$tweet_id;
        $request4 = Request::forge($request_url4, "curl")->set_method("POST");
        $request4->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response4 = $request4->execute()->response();
        $array_body4 = Format::forge($response4->body, "xml")->to_array();
        // parse data from /tweets/retweet/:id 
        $title = $array_body4["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Successfully cancel your retweet!");
    }
    // check whether invalid cookie user fail unretweet
    public function test_unretweet_invalid_cookie_user_fail_unretweet() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // setting
        $cookie = array();
        $wrong_cookie = array();
        $invalid_cookie = $user1_cookie."_test_fail";
        $wrong_cookie["cookie_value"] = $invalid_cookie;
        $wrong_cookie["user_id"] = $user1_id;
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        // retweet
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        // unretweet
        $request2_url = "http://localhost:8081/tweets/unretweet/".$tweet_id;
        $request2 = Request::forge($request2_url, "curl")->set_method("POST");
        $request2->set_option(CURLOPT_COOKIE, build_cookie($wrong_cookie));
        $response2 = $request2->execute()->response();
        $array_body2 = Format::forge($response2->body, "xml")->to_array();
        // parse data from unretweet
        $title = $array_body2["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Fail to cancel retweet.");
    }
    // check whether invalid userid user fail unretweet
    public function test_unretweet_invalid_userid_user_fail_unretweet() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        // setting
        $cookie = array();
        $wrong_cookie = array();
        $wrong_cookie["cookie_value"] = $user1_cookie;
        $wrong_cookie["user_id"] = $user1_id + 10;
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        // retweet
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        // unretweet
        $request2_url = "http://localhost:8081/tweets/unretweet/".$tweet_id;
        $request2 = Request::forge($request2_url, "curl")->set_method("POST");
        $request2->set_option(CURLOPT_COOKIE, build_cookie($wrong_cookie));
        $response2 = $request2->execute()->response();
        $array_body2 = Format::forge($response2->body, "xml")->to_array();
        // parse data from unretweet
        $title = $array_body2["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Fail to cancel retweet.");
    }
    // check whether non cookie user fail unretweet
    public function test_unretweet_non_cookie_user_fail_unretweet() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        // retweet
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        // unretweet
        $request2_url = "http://localhost:8081/tweets/unretweet/".$tweet_id;
        $request2 = Request::forge($request2_url, "curl")->set_method("POST");
        $response2 = $request2->execute()->response();
        $array_body2 = Format::forge($response2->body, "xml")->to_array();
        // parse data from unretweet
        $title = $array_body2["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Fail to cancel retweet.");
    }
    // check whether non exist tweet fail unretweet
    public function test_unretweet_non_exist_tweet_fail_unretweet() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        // retweet
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        // unretweet
        $invalid_tweet_id = $tweet_id + 10;
        $request2_url = "http://localhost:8081/tweets/unretweet/".$invalid_tweet_id;
        $request2 = Request::forge($request2_url, "curl")->set_method("POST");
        $request2->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response2 = $request2->execute()->response();
        $array_body2 = Format::forge($response2->body, "xml")->to_array();
        // parse data from unretweet
        $title = $array_body2["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Fail to cancel retweet.");
    }
    // check whether non retweet tweet cannot unretweet
    public function test_unretweet_non_retweet_tweet_fail_unretweet() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
        $tweet_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $cookie = array();
        $cookie["cookie_value"] = $user1_cookie;
        $cookie["user_id"] = $user1_id;
        // retweet
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet_id;
        $request = Request::forge($request_url, "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response = $request->execute()->response();
        // unretweet
        $request2_url = "http://localhost:8081/tweets/unretweet/".$tweet2_id;
        $request2 = Request::forge($request2_url, "curl")->set_method("POST");
        $request2->set_option(CURLOPT_COOKIE, build_cookie($cookie));
        $response2 = $request2->execute()->response();
        $array_body2 = Format::forge($response2->body, "xml")->to_array();
        // parse data from unretweet
        $title = $array_body2["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Fail to cancel retweet.");
    }
    // check whether after 2 people retweet can unretweet
    public function test_unretweet_2people_retweet_can_unretweet() {
        // initialization
        User::deleteAllUsers();
        Tweet::deleteAllTweets();
        [$user1_id, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
        [$user2_id, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
        $tweet1_id = Tweet::insertTweet("test tweet1", $user1_id, $user1_cookie);
        $tweet2_id = Tweet::insertTweet("test tweet2", $user1_id, $user1_cookie);
        $tweet3_id = Tweet::insertTweet("test tweet3", $user2_id, $user2_cookie);
        $tweet4_id = Tweet::insertTweet("test tweet4", $user2_id, $user2_cookie);
        // setting
        $cookie1 = array();
        $cookie1["cookie_value"] = $user1_cookie;
        $cookie1["user_id"] = $user1_id;
        $cookie2 = array();
        $cookie2["cookie_value"] = $user2_cookie;
        $cookie2["user_id"] = $user2_id;
        // retweet 1
        $request_url = "http://localhost:8081/tweets/retweet/".$tweet1_id;
        $request = Request::forge($request_url, "curl")->set_method("POST");
        $request->set_option(CURLOPT_COOKIE, build_cookie($cookie1));
        $response = $request->execute()->response();
        // retweet 2
        $request2_url = "http://localhost:8081/tweets/retweet/".$tweet2_id;
        $request2 = Request::forge($request2_url, "curl")->set_method("POST");
        $request2->set_option(CURLOPT_COOKIE, build_cookie($cookie2));
        $response2 = $request2->execute()->response();
        // unretweet 1
        $request3_url = "http://localhost:8081/tweets/unretweet/".$tweet1_id;
        $request3 = Request::forge($request3_url, "curl")->set_method("POST");
        $request3->set_option(CURLOPT_COOKIE, build_cookie($cookie1));
        $response3 = $request3->execute()->response();
        $array_body3 = Format::forge($response3->body, "xml")->to_array();
        $title = $array_body3["body"]["div"]["div"][1]["h5"];
        $this->assertSame($title, "Successfully cancel your retweet!");
        // get /tweets/show/:id
        $request4_url = "http://localhost:8081/tweets/show/".$tweet1_id;
        $request4 = Request::forge($request4_url, "curl")->set_method("GET");
        $request4->set_option(CURLOPT_COOKIE, build_cookie($cookie1));
        $response4 = $request4->execute()->response();
        $array_body4 = Format::forge($response4->body, "xml")->to_array();
        $retweet_button = $array_body4["body"]["div"]["div"][1]["div"]["div"][1]["form"]["input"][1]["@attributes"]["value"];
        $this->assertSame($retweet_button, "Retweet");
    }
}