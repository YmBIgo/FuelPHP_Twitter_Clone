<?php

/**
 * Test For User Controller
 *
 * @group ControllerUser
 * 
 * run with below command
 * 
 * < php oil test --group=ControllerUser >
 */

use \Model\User;

class Test_Controller_User extends TestCase {
	// new
	//
	// You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.
	//

	// check whether request would return token in inner system
	public function test_new_status_and_token() {
		User::deleteAllUsers();
		//
		$request = Request::forge("users/new");
		$response = $request->execute()->response();
		$actual_status_code = $response->status;
		$expected = 200;
		$this->assertSame($expected, $actual_status_code);
		// check CSRF Token
		$token = $response->body->content->token;
		$token_key = $token["token_key"];
		$actual_token = $token["token"];
		$this->assertSame($token_key == false, false);
		$this->assertSame($actual_token == false, false);
	}
	// check whether valid cookie user returns "already logged in" title
	public function test_new_valie_cookie_return_fail() {
		User::deleteAllUsers();
		[$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
		//
		$cookie = array();
		$cookie["user_id"] = $user1_id;
		$cookie["cookie_value"] = $user1_cookie;
		//
		$request = Request::forge("http://localhost:8081/users/new", "curl")->set_method("GET");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, "xml")->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$this->assertSame($title, "You have already logged in...");
	}
	// check whether invalid cookie user returns form
	public function test_new_invalid_cookie_user_return_form() {
		User::deleteAllUsers();
		[$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
		//
		$cookie = array();
		$cookie["user_id"] = $user1_id."12";
		$cookie["cookie_value"] = $user1_cookie;
		//
		$request = Request::forge("http://localhost:8081/users/new", "curl")->set_method("GET");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, "xml")->to_array();
		$email_input = $array_body["body"]["div"]["div"][1]["form"]["input"][0];
		$email_input_name = $email_input["@attributes"]["name"];
		$this->assertSame($email_input_name, "email");
		$password_input = $array_body["body"]["div"]["div"][1]["form"]["input"][1];
		$password_input_name = $password_input["@attributes"]["name"];
		$this->assertSame($password_input_name, "password");
		$token_input = $array_body["body"]["div"]["div"][1]["form"]["input"][2];
		$token_input_name = $token_input["@attributes"]["name"];
		$token_input_value = $token_input["@attributes"]["value"];
		$this->assertSame($token_input_name, "fuel_csrf_token");
		$this->assertSame($token_input_value == false, false);
	}

	// create
	//
	// You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.
	//

	// check whether request for duplicated email will return false result in inner system
	public function test_create_status_and_user_fail() {
		User::deleteAllUsers();
		User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
		//
		$_POST["email"] = "hoge12@hoge.com";
		$_POST["password"] = "hogehoge";
		$request = Request::forge("users/create")->set_method("POST");
		$response = $request->execute()->response();
		$actual_status_code = $response->status;
		$expected = 200;
		$this->assertSame($expected, $actual_status_code);
		// check user ID
		$user_id = $response->body->content->user_id;
		$this->assertSame($user_id, false);
	}
	// check whether request for valid email will return id
	public function test_create_user_success() {
		User::deleteAllUsers();
		User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		$users = User::fetchAll();
		//
		$_POST["email"] = "hoge12@hoge.com";
		$_POST["password"] = "hogehoge";
		/*
		// have tried csrf check but not work...
		// so @controller there is not checking csrf on test environment.
			$key = Config::get("security.csrf_token_key");
			$val = Security::fetch_token();
			$_POST[$key] = $val;
		*/
		$request = Request::forge("users/create")->set_method("POST");
		$response = $request->execute()->response();
		// check user ID
		$user_id = $response->body->content->user_id;
		$this->assertSame((int)$user_id, $users[0]["id"] + 1);
	}
	// check whether cookie user cannot create user
	public function test_create_with_cookie_user_fail() {
		User::deleteAllUsers();
		[$user1_id, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		// set cookie
		$cookie = array();
		$cookie["user_id"] = $user1_id;
		$cookie["cookie_value"] = $user1_cookie;
		// set post
		$post = array();
		$post["email"] = "hoge2@hoge.com";
		$post["password"] = "hogehoge";
		//
		$request = Request::forge("http://localhost:8081/users/create", "curl")->set_method("POST");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$request->set_params($post);
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, "xml")->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$this->assertSame($title, "You have already logged in...");
	}
	// check whether invalid cookie user can create user for id
	public function test_create_with_invalid_cookie_user1_success() {
		User::deleteAllUsers();
		[$user1_id, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		$hashed_password = str_repeat("*", 8);
		// set cookie
		$cookie = array();
		$cookie["user_id"] = $user1_id + 10;
		$cookie["cookie_value"] = $user1_cookie;
		// set post
		$post = array();
		$post["email"] = "hoge2@hoge.com";
		$post["password"] = "hogehoge";
		//
		$request = Request::forge("http://localhost:8081/users/create", "curl")->set_method("POST");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$request->set_params($post);
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, "xml")->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$new_user_id = $user1_id + 1;
		$this->assertSame($title, "User Create Success with ID = ".$new_user_id);
		$title2 = $array_body["body"]["div"]["div"][1]["p"];
		$this->assertSame($title2, "Your User is "." with password of ".$hashed_password);
	}
	// check whether invalid cookie user can create user for email
	public function test_create_with_invalid_cookie_user2_success() {
		User::deleteAllUsers();
		[$user1_id, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		$hashed_password = str_repeat("*", 8);
		// set cookie
		$cookie = array();
		$cookie["user_id"] = $user1_id;
		$cookie["cookie_value"] = $user1_cookie."_test_fail";
		// set post
		$post = array();
		$post["email"] = "hoge2@hoge.com";
		$post["password"] = "hogehoge";
		//
		$request = Request::forge("http://localhost:8081/users/create", "curl")->set_method("POST");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$request->set_params($post);
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, "xml")->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$new_user_id = $user1_id + 1;
		$this->assertSame($title, "User Create Success with ID = ".$new_user_id);
		$title2 = $array_body["body"]["div"]["div"][1]["p"];
		$this->assertSame($title2, "Your User is "." with password of ".$hashed_password);
	}
	// check invalid cookie user cannot create duplicated email user
	public function test_create_with_invalid_cookie_user_fails_with_duplicate_email() {
		User::deleteAllUsers();
		[$user1_id, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		// set cookie
		$cookie = array();
		$cookie["user_id"] = $user1_id;
		$cookie["cookie_value"] = $user1_cookie."_test_fail";
		// set post
		$post = array();
		$post["email"] = "hoge1@hoge.com";
		$post["password"] = "hogehoge";
		//
		$request = Request::forge("http://localhost:8081/users/create", "curl")->set_method("POST");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$request->set_params($post);
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, "xml")->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$this->assertSame($title, "You have entered incorrect information.");
	}

	// index
	public function test_index_status_and_users() {
		User::deleteAllUsers();
		User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
		User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
		//
		$request = Request::forge("users/index")->set_method("GET");
		$response = $request->execute()->response();
		$actual_status_code = $response->status;
		$expected = 200;
		$this->assertSame($expected, $actual_status_code);
		// check user 
		$users = $response->body->content->users;
		$this->assertSame(count($users), 2);
	}
	// show
	// should check whether illegal value produce null value
	public function test_show_status_and_user_success() {
		User::deleteAllUsers();
		$user1 = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
		$user2 = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
		//
		$request = Request::forge("users/show/".$user1[0])->set_method("GET");
		$response = $request->execute()->response();
		$actual_status_code = $response->status;
		$expected = 200;
		$this->assertSame($expected, $actual_status_code);
		// check user
		$user = $response->body->content->user;
		$this->assertSame($user == false, false);
		$this->assertSame($user["name"], "User");
	}
	public function test_show_user_fail() {
		User::deleteAllUsers();
		$user1 = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
		$user2 = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
		$user_id = $user1[0]+10;
		//
		$request = Request::forge("users/show/".$user_id)->set_method("GET");
		$response = $request->execute()->response();
		// check user
		$user = $response->body->content->user;
		$this->assertSame($user, null);
	}
	// session new
	//
	// You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.
	//

	// check whether session new is displayed when cookie is null
	public function test_session_new_status_and_token() {
		User::deleteAllUsers();
		$user1 = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
		// without cookie
		$request = Request::forge("http://localhost:8081/users/new", "curl")->set_method("GET");
		$response = $request->execute()->response();
		$this->assertSame($response->status, 200);
		$array_body = Format::forge($response->body, "xml")->to_array();
		$email_input = $array_body["body"]["div"]["div"][1]["form"]["input"][0];
		$email_input_name = $email_input["@attributes"]["name"];
		$this->assertSame($email_input_name, "email");
		$password_input = $array_body["body"]["div"]["div"][1]["form"]["input"][1];
		$password_input_name = $password_input["@attributes"]["name"];
		$this->assertSame($password_input_name, "password");
		$token_input = $array_body["body"]["div"]["div"][1]["form"]["input"][2];
		$token_input_name = $token_input["@attributes"]["name"];
		$token_input_value = $token_input["@attributes"]["value"];
		$this->assertSame($token_input_name, "fuel_csrf_token");
		$this->assertSame($token_input_value == false, false);
	}
	// check whether session new is not displayed when cookie is valid
	public function test_session_new_cookie_user_valid() {
		User::deleteAllUsers();
		[$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
		// with valid cookie
		$cookie = array();
		$cookie["cookie_value"] = $user1_cookie;
		$cookie["user_id"] = $user1_id;
		// 
		$request = Request::forge("http://localhost:8081/users/new", "curl")->set_method("GET");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, "xml")->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$this->assertSame($title, "You have already logged in...");
	}
	// check whether session new is displayed when cookie is not valid
	public function test_session_new_cookie_user_not_valie() {
		User::deleteAllUsers();
		[$user1_id, $user1_cookie] = User::insertUser("hoge12@hoge.com", "hogehoge", null, null);
		// with invalid cookie
		$cookie = array();
		$cookie["cookie_value"] = $user1_cookie."_test_fail";
		$cookie["user_id"] = $user1_id;
		// 
		$request = Request::forge("http://localhost:8081/users/new", "curl")->set_method("GET");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, "xml")->to_array();
		$email_input = $array_body["body"]["div"]["div"][1]["form"]["input"][0];
		$email_input_name = $email_input["@attributes"]["name"];
		$this->assertSame($email_input_name, "email");
		$password_input = $array_body["body"]["div"]["div"][1]["form"]["input"][1];
		$password_input_name = $password_input["@attributes"]["name"];
		$this->assertSame($password_input_name, "password");
		$token_input = $array_body["body"]["div"]["div"][1]["form"]["input"][2];
		$token_input_name = $token_input["@attributes"]["name"];
		$token_input_value = $token_input["@attributes"]["value"];
		$this->assertSame($token_input_name, "fuel_csrf_token");
		$this->assertSame($token_input_value == false, false);
	}

	// session create
	//
	// You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.
	//

	// check whether user validation is success without cookie
	public function test_session_create_status_and_user_validation_success() {
		User::deleteAllUsers();
		[$user1, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		[$user2, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
		$password_hashed = str_repeat("*", 8);
		// set POST
		$post = array();
		$post["email"] = "hoge1@hoge.com";
		$post["password"] = "hogehoge";
		//
		$request = Request::forge("http://localhost:8081/users/session/create", "curl")->set_method("POST");
		$request->set_params($post);
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, 'xml')->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$title2 = $array_body["body"]["div"]["div"][1]["p"];
		$this->assertSame($title, "User Create Success with ID = ".$user1);
		$this->assertSame($title2, "Your User is  with password of ".$password_hashed);
	}
	// check whether user validation is fails without cookie for email
	public function test_session_create_user_validation_fail_for_email() {
		User::deleteAllUsers();
		[$user1, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		[$user2, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
		$password_hashed = str_repeat("*", 8);
		// set POST
		$post = array();
		$post["email"] = "hoge1@hoge.com"."12";
		$post["password"] = "hogehoge";
		//
		$request = Request::forge("http://localhost:8081/users/session/create", "curl")->set_method("POST");
		$request->set_params($post);
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, 'xml')->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$this->assertSame($title, "You have entered incorrect information.");
	}
	// check whether user validation is fails without cookie for password
	public function test_session_create_user_validation_fail_for_password() {
		User::deleteAllUsers();
		[$user1, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		[$user2, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
		$password_hashed = str_repeat("*", 8);
		// set POST
		$post = array();
		$post["email"] = "hoge1@hoge.com";
		$post["password"] = "hogehoge"."12";
		//
		$request = Request::forge("http://localhost:8081/users/session/create", "curl")->set_method("POST");
		$request->set_params($post);
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, 'xml')->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$this->assertSame($title, "You have entered incorrect information.");
	}
	// check whether user validation is fails without cookie for password and email
	public function test_session_create_user_validation_fail_for_password_and_email() {
		User::deleteAllUsers();
		[$user1, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		[$user2, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
		$password_hashed = str_repeat("*", 8);
		// set POST
		$post = array();
		$post["email"] = "hoge1@hoge.com"."12";
		$post["password"] = "hogehoge"."12";
		//
		$request = Request::forge("http://localhost:8081/users/session/create", "curl")->set_method("POST");
		$request->set_params($post);
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, 'xml')->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$this->assertSame($title, "You have entered incorrect information.");
	}
	// check whether cookie user cannot login through session create
	public function test_session_create_cookie_user_cannot_login() {
		User::deleteAllUsers();
		[$user1, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		[$user2, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
		// set cookie
		$cookie = array();
		$cookie["cookie_value"] = $user2_cookie;
		$cookie["user_id"] = $user2;
		// set POST
		$post = array();
		$post["email"] = "hoge1@hoge.com";
		$post["password"] = "hogehoge";
		// Login
		$request = Request::forge("http://localhost:8081/users/session/create", "curl")->set_method("POST");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$request->set_params($post);
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, 'xml')->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$this->assertSame($title, "You have already logged in...");
	}
	// check whether invalid cookie user can login throught session create
	public function test_session_create_invalid_cookie_user_can_login() {
		User::deleteAllUsers();
		[$user1, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		[$user2, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
		$password_hashed = str_repeat("*", 8);
		// set invalid cookie
		$cookie = array();
		$cookie["cookie_value"] = $user2_cookie."_test_fail";
		$cookie["user_id"] = $user2;
		// set POST
		$post = array();
		$post["email"] = "hoge1@hoge.com";
		$post["password"] = "hogehoge";
		// Login
		$request = Request::forge("http://localhost:8081/users/session/create", "curl")->set_method("POST");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$request->set_params($post);
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, 'xml')->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$title2 = $array_body["body"]["div"]["div"][1]["p"];
		$this->assertSame($title, "User Create Success with ID = ".$user1);
		$this->assertSame($title2, "Your User is  with password of ".$password_hashed);
	}

	// edit
	//
	// You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.
	//
	// should check cookie check ok and fail.
	public function test_edit_status_and_cookie_user_success() {
		User::deleteAllUsers();
		[$user2, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
		$cookie = array();
		$cookie["cookie_value"] = $user2_cookie;
		$cookie["user_id"] = $user2;
		// Login
		$request = Request::forge("http://localhost:8081/users/edit", "curl")->set_method("GET");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$response = $request->execute()->response();
		$this->assertSame($response->status, 200);
		$array_body = Format::forge($response->body, 'xml')->to_array();
		$name_input = $array_body["body"]["div"]["div"][1]["form"]["input"][0];
		$name_input_value = $name_input["@attributes"]["value"];
		$this->assertSame($name_input_value, "User");
		$describe_input_value = $array_body["body"]["div"]["div"][1]["form"]["textarea"];
		$this->assertSame($describe_input_value, "Your Introduction here.");
		$csrf_token = $array_body["body"]["div"]["div"][1]["form"]["input"][1];
		$csrf_token_value = $csrf_token["@attributes"]["value"];
		$this->assertSame($csrf_token == false, false);
	}
	public function test_edit_cookie_user_fail() {
		User::deleteAllUsers();
		[$user2, $user2_cookie] = User::insertUser("hoge2@hoge.com", "hogehoge", null, null);
		$cookie = array();
		$cookie["cookie_value"] = $user2_cookie."_test_fail";
		$cookie["user_id"] = $user2;
		// Login Fail
		$request = Request::forge("http://localhost:8081/users/edit", "curl")->set_method("GET");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, "xml")->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$this->assertSame($title, "You should login first to edit your user data...");
	}
	// update
	//
	// You should startup test server by [ env FUEL_ENV=test php -S localhost:8081 ] @./public before running these tests.
	//
	// should check cookie check ok and fail

	// check whether can change name and description value.
	public function test_update_status_and_cookie_user_success() {
		User::deleteAllUsers();
		[$user1, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		// Login
		$cookie = array();
		$cookie["cookie_value"] = $user1_cookie;
		$cookie["user_id"] = $user1;
		// Update User
		$request = Request::forge("http://localhost:8081/users/update", "curl")->set_method("POST");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$post_name = "Sample User";
		$post_description = "My Introduction here .";
		$post = ["name" => $post_name, "description" => $post_description];
		$request->set_params($post);
		$response = $request->execute()->response();
		$this->assertSame($response->status, 200);
		$array_body = Format::forge($response->body, "xml")->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$this->assertSame($title, "User is successfully updated!");
		// Check User has been updated.
		$request2 = Request::forge("http://localhost:8081/users/show/".$user1, "curl")->set_method("GET");
		$request2->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$response2 = $request2->execute()->response();
		$this->assertSame($response2->status, 200);
		$array_body2 = Format::forge($response2->body, "xml")->to_array();
		$user_name = $array_body2["body"]["div"]["div"][1]["h3"];
		$user_description = $array_body2["body"]["div"]["div"][1]["p"];
		$this->assertSame(trim($user_name), $post_name."さん");
		$this->assertSame($user_description, "紹介：".$post_description);
	}
	// check whether login fail does not update values
	public function test_update_cookie_user_fail() {
		User::deleteAllUsers();
		[$user1, $user1_cookie] = User::insertUser("hoge1@hoge.com", "hogehoge", null, null);
		$original_user_name = "User";
		$original_user_description = "Your Introduction here.";
		// Login
		$cookie = array();
		$cookie["cookie_value"] = $user1_cookie."_test_fail";
		$cookie["user_id"] = $user1;
		// Update User
		$request = Request::forge("http://localhost:8081/users/update", "curl")->set_method("POST");
		$request->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$post_name = "Sample User";
		$post_description = "My Introduction here .";
		$post = ["name" => $post_name, "description" => $post_description];
		$request->set_params($post);
		$response = $request->execute()->response();
		$array_body = Format::forge($response->body, "xml")->to_array();
		$title = $array_body["body"]["div"]["div"][1]["h5"];
		$this->assertSame($title, "Please enter correct inputs...");
		// Check User has not been updated.
		$request2 = Request::forge("http://localhost:8081/users/show/".$user1, "curl")->set_method("GET");
		$request2->set_option(CURLOPT_COOKIE, build_cookie($cookie));
		$response2 = $request2->execute()->response();
		$array_body2 = Format::forge($response2->body, "xml")->to_array();
		$user_name = $array_body2["body"]["div"]["div"][1]["h3"];
		$user_description = $array_body2["body"]["div"]["div"][1]["p"];
		$this->assertSame(trim($user_name) == $post_name."さん", false);
		$this->assertSame($user_description == "紹介：".$post_description, false);
		$this->assertSame(trim($user_name), $original_user_name."さん");
		$this->assertSame($user_description, "紹介：".$original_user_description);
	}
}

function build_cookie($data) {
	$cookie = [];
	foreach ($data as $key => $value) {
		array_push($cookie, $key.'='.urlencode($value));
	}
	if (count($cookie) > 0) {
		return trim(implode("; ", $cookie));
	}
	return false;
}
