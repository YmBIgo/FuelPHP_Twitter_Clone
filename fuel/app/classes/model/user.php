<?php

namespace Model;
use \DB;

class User extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"name" => array(
			"label" => "Name",
			"data_type" => "varchar",
		),
		"email" => array(
			"label" => "Email",
			"data_type" => "varchar",
		),
		"description" => array(
			"label" => "Description",
			"data_type" => "text",
		),
		"status" => array(
			"label" => "Status",
			"data_type" => "int",
		),
		"hashed_password" => array(
			"label" => "Hashed Password",
			"data_type" => "varchar",
		),
		"password" => array(
			"label" => "Password",
			"data_type" => "varchar",
		),
		"cookie_value" => array(
			"label" => "Cookie Value",
			"data_type" => "varchar",
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
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'property' => 'updated_at',
			'mysql_timestamp' => false,
		),
	);

	protected static $_conditions = array(
		'order_by' => array('id' => 'desc'),
	);

	protected static $_table_name = 'users';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
	);

	public static function fetchAll() {
		$query = DB::query('SELECT * FROM users', DB::SELECT);
		$result = $query->as_assoc()->execute();
		return $result;
	}
	public static function fetchByIdSafe($id) {
		$query = DB::select('id', 'name', 'email', 'description', 'status')->from('users');
		$query->where('id', $id);
		$result = $query->as_assoc()->execute();
		return $result;
	}
	public static function fetchByEmailSafe($email) {
		$query = DB::select('id', 'name', 'email', 'description', 'status')->from('users');
		$query->where('email', $email);
		$result = $query->as_assoc()->execute();
		return $result;
	}
	public static function fetchByEmailUnsafe($email) {
		$query = DB::select('id', 'name', 'email', 'description', 'status', 'cookie_value', 'password', 'hashed_password')->from('users');
		$query->where('email', $email);
		$result = $query->as_assoc()->execute();
		return $result;
	}
	public static function fetchByCookieSafe($cookie_value) {
		$query = DB::select('id', 'name', 'email', 'description', 'status')->from('users');
		$query->where('cookie_value', $cookie_value);
		$result = $query->as_assoc()->execute();
		return $result;
	}
	public static function insertUser($email, $password, $cookie) {
		$email_check = preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email);
		if (!$email || !$email_check || strlen($password) < 8 || strlen($password) > 50 || $cookie) { return false; }
		$user_result = User::fetchByEmailSafe($email)[0];
		if ($user_result != false) {
			return [false, false];
		}
		$hashed_password = \Crypt::encode($password);
		$rand_cookie = \Str::random('alnum', 16);
		list($insert_id, $rows_affected) = DB::insert('users')->columns(array('name', 'email', 'status', 'cookie_value', 'password', 'hashed_password', 'description'))->values(array('User', $email, 1, $rand_cookie, $password, $hashed_password, 'Your Introduction here.'))->execute();
		if ($rows_affected > 0) {
			return [$insert_id, $rand_cookie];
		} else {
			return [false, false];
		}
	}
	public static function deleteAllUsers() {
		$query = DB::delete('users');
		$query->execute();
	}

}
