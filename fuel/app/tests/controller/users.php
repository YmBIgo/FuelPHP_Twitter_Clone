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
	public function test_new_status_and_token() {
		$request = Request::forge("users/new");
		$current_cookie_value = \Cookie::get("cookie_value");
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
	// create
	public function test_create_status_and_user_fail() {
		User::deleteAllUsers();
		User::insertUser("hoge12@hoge.com", "hogehoge", null);
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
	public function test_create_user_success() {
		User::deleteAllUsers();
		//
		$_POST["email"] = "hoge12@hoge.com";
		$_POST["password"] = "hogehoge";
		/* have tried csrf check but not work...
		//
		$key = Config::get("security.csrf_token_key");
		$val = Security::fetch_token();
		$_POST[$key] = $val;
		*/
		$request = Request::forge("users/create")->set_method("POST");
		$response = $request->execute()->response();
		// check user ID
		$user_id = $response->body->content->user_id;
		$this->assertSame($user_id == false, false);
	}
}

