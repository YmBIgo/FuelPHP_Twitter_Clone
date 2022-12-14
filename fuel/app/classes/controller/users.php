<?php

use \Model\User;

class Controller_Users extends Controller_Template
{

	public function action_show()
	{
		$params = Request::active()->params();
		$id = $params['id'];
		$user = User::fetchByIdSafe($id)[0];
		$data = array();
		$data["subnav"] = array('show'=> 'active' );
		if ( $user != false ) {
			$data['user'] = $user;
			$this->template->title = 'Users - '.$user['name'];
		} else {
			$data['user'] = null;
			$this->template->title = "User Not Found";
		}
		$this->template->content = View::forge('users/show', $data);
	}

	public function action_index()
	{
		$data = array();
		$users = User::fetchAll();
		$data["users"] = $users;
		$data["subnav"] = array('index'=> 'active' );
		$this->template->title = 'Users - Index';
		$this->template->content = View::forge('users/index', $data);
	}

	public function action_create()
	{
		// get user input & user cookie information
		$data = array();
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user_id = Cookie::get("user_id");
		$data["cookie_value"] = $cookie_value;
		$cookie_user = User::fetchByCookieAndId($cookie_value, $cookie_user_id)[0];
		$data["cookie_user"]  = $cookie_user;
		$post = Input::post();
		$data["email"] = $post['email'];
		$data["password"] = str_repeat("*", strlen($post['password']));
		// check cookie
		if ($cookie_user != null) {
			$data["user_id"] = false;
			$data["subnav"] = array('create'=> 'active' );
			$this->template->title = 'Users - Create';
			$this->template->content = View::forge('users/create', $data);
			return;
		}
		// check csrf
		if (Security::check_token() || \Fuel::$env == "test") {
			// insert user check
			[$user_id, $created_cookie_value] = User::insertUser($post['email'], $post['password'], $cookie_value, $cookie_user_id);
			if ($user_id != false) {
				// set cookie and user id
				Cookie::set("cookie_value", $created_cookie_value, 60*60*24*100);
				Cookie::set("user_id", $user_id, 60*60*24*100);
				$data["user_id"] = $user_id;
			} else {
				$data["user_id"] = false;
			}
		} else {
			$data["user_id"] = false;
		}
		$data["subnav"] = array('create'=> 'active' );
		$this->template->title = 'Users - Create';
		$this->template->content = View::forge('users/create', $data);
	}

	public function action_new()
	{
		// check cookie
		$data = array();
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user_id = Cookie::get("user_id");
		$cookie_user  = User::fetchByCookieAndId($cookie_value, $cookie_user_id)[0];
		$data["cookie_value"] = $cookie_value;
		$data["cookie_user"] = $cookie_user;
		if ( $cookie_user == false ) {
			// insert csrf data
			$token = array();
			$token['token_key'] = Config::get('security.csrf_token_key');
			$token['token'] = Security::fetch_token();
			$data["token"] = $token;
		}
		$data["subnav"] = array('new'=> 'active' );
		$this->template->title = 'Users - New';
		$this->template->content = View::forge('users/new', $data);
	}

	public function action_edit()
	{
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user_id = Cookie::get("user_id");
		$cookie_user  = User::fetchByCookieAndId($cookie_value, $cookie_user_id)[0];
		$data["cookie_value"] = $cookie_value;
		$data["cookie_user"] = $cookie_user;

		if ( $cookie_user != false ) {
			// insert csrf data
			$token = array();
			$token["token_key"] = Config::get("security.csrf_token_key");
			$token["token"] = Security::fetch_token();
			$data["token"] = $token;
		}
		$data["subnav"] = array('edit'=> 'active' );
		$this->template->title = 'Users - Edit';
		$this->template->content = View::forge('users/edit', $data);
	}

	public function action_update() {
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user_id = Cookie::get("user_id");
		$cookie_user  = User::fetchByCookieAndId($cookie_value, $cookie_user_id)[0];
		$data["cookie_value"] = $cookie_value;
		$data["cookie_user"] = $cookie_user;
		$post = Input::post();
		if ($cookie_user == false) {
			$user_id = false;
			$data["user_id"] = $user_id;
			$data["subnav"] = array('edit'=> 'active' );
			$this->template->title = 'Users - Update';
			$this->template->content = View::forge('users/update', $data);
			return;
		}
		// check csrf
		if (Security::check_token() || \Fuel::$env == "test") {
			$result = User::updateUserNormal($cookie_user["id"], $cookie_value, $post["name"], $post["description"]);
			if ($result == false) {
				$user_id = false;
			} else {
				$user_id = $cookie_user["id"];
			}
		} else {
			$user_id = false;
		}
		$data["user_id"] = $user_id;
		$data["subnav"] = array('edit'=> 'active' );
		$this->template->title = 'Users - Update';
		$this->template->content = View::forge('users/update', $data);
	}

	public function action_session_new() {
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user_id = Cookie::get("user_id");
		$data["cookie_value"] = $cookie_value;
		$cookie_user = User::fetchByCookieAndId($cookie_value, $cookie_user_id)[0];
		$data["cookie_user"] = $cookie_user;
		if ($cookie_user == false) {
			// insert csrf data
			$token = array();
			$token["token_key"] = Config::get("security.csrf_token_key");
			$token["token"] = Security::fetch_token();
			$data["token"] = $token;
		}
		$data["subnav"] = array('session_new'=> 'active' );
		$this->template->title = 'Users - Session New';
		$this->template->content = View::forge('users/session_new', $data);
	}

	public function action_session_create() {
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user_id = Cookie::get("user_id");
		$data["cookie_value"] = $cookie_value;
		$cookie_user = User::fetchByCookieAndId($cookie_value, $cookie_user_id)[0];
		$data["cookie_user"] = $cookie_user;
		$post = Input::post();
		$data["email"] = $post['email'];
		$data["password"] = str_repeat("*", strlen($post['password']));
		$data["subnav"] = array('session_create'=> 'active' );
		if ($cookie_user != false) {
			$data['user_id'] = false;
			$this->template->title = 'Users - Session Create';
			$this->template->content = View::forge('users/session_create', $data);
			return;
		}
		// check csrf
		if (Security::check_token() || \Fuel::$env == "test") {
			$is_user_valid = User::fetchByEmailAndPassword($post["email"], $post["password"]);
			if ( $is_user_valid != false ) {
				Cookie::set("cookie_value", $is_user_valid["cookie_value"], 60*60*24*100);
				Cookie::set("user_id", $is_user_valid["id"], 60*60*24*100);
				$data["user_id"] = $is_user_valid["id"];
			} else {
				$data["user_id"] = false;
			}
		} else {
			$data["user_id"] = false;
		}
		$this->template->title = 'Users - Session Create';
		$this->template->content = View::forge('users/session_create', $data);
	}

}
