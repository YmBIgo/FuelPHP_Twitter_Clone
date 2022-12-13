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
			$this->template->title = 'Users &raquo; '.$user['name'];
		} else {
			$data['user'] = null;
			$this->template->title = "User Not Found";
		}
		$this->template->content = View::forge('users/show', $data);
	}

	public function action_index()
	{
		$data["subnav"] = array('index'=> 'active' );
		$this->template->title = 'Users &raquo; Index';
		$this->template->content = View::forge('users/index', $data);
	}

	public function action_create()
	{
		// get user input & user cookie information
		$data = array();
		$cookie_value = Cookie::get('cookie_value');
		$data["cookie_value"] = $cookie_value;
		$cookie_user = User::fetchByCookieSafe($cookie_value)[0];
		$data["cookie_user"]  = $cookie_user;
		$post = Input::post();
		$data["email"] = $post['email'];
		$data["password"] = str_repeat("*", strlen($post['password']));
		// check cookie
		if ($cookie_user != null) {
			$data["subnav"] = array('create'=> 'active' );
			$this->template->title = 'Users &raquo; Create';
			$this->template->content = View::forge('users/create', $data);
			return;
		}
		// check csrf
		if (Security::check_token()) {
			// insert user check
			[$user_id, $created_cookie_value] = User::insertUser($post['email'], $post['password'], $cookie_value);
			if ($user_id != false) {
				// set cookie and user id
				Cookie::set("cookie_value", $created_cookie_value, 60*60*24*100);
				$data["user_id"] = $user_id;
			} else {
				$data["user_id"] = false;
			}
		} else {
			$data["user_id"] = false;
		}
		$data["subnav"] = array('create'=> 'active' );
		$this->template->title = 'Users &raquo; Create';
		$this->template->content = View::forge('users/create', $data);
	}

	public function action_new()
	{
		// check cookie
		$data = array();
		$cookie_value = Cookie::get('cookie_value');
		$cookie_user  = User::fetchByCookieSafe($cookie_value)[0];
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
		$this->template->title = 'Users &raquo; New';
		$this->template->content = View::forge('users/new', $data);
	}

	public function action_edit()
	{
		$data["subnav"] = array('edit'=> 'active' );
		$this->template->title = 'Users &raquo; Edit';
		$this->template->content = View::forge('users/edit', $data);
	}

	public function action_session_new() {
		$cookie_value = Cookie::get('cookie_value');
		$data["cookie_value"] = $cookie_value;
		$cookie_user = User::fetchByCookieSafe($cookie_value)[0];
		$data["cookie_user"] = $cookie_user;
		$data["subnav"] = array('session_new'=> 'active' );
		if ($cookie_value != null) {
			$this->template->title = 'Users &raquo; Session New';
			$this->template->content = View::forge('users/create', $data);
			return;
		}
		$this->template->title = 'Users &raquo; Session New';
		$this->template->content = View::forge('users/session_new', $data);
	}

	public function action_session_create() {
		$data["subnav"] = array('session_create'=> 'active' );
		$this->template->title = 'Users &raquo; Session Create';
		$this->template->content = View::forge('users/session_create', $data);
	}

}
