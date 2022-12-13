<?php

use \Model\User;

/**
 * Test For User Model
 *
 * @group ModelUser
 *
 * run with below command
 *
 * < php oil test --group=ModelUser >
 */

class Test_Model_User extends TestCase {

	// < Delete Check >
	//
	// check whether can delete all user or not
	public function test_user_delection() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		User::deleteAllUsers();
		$all_user = User::fetchAll();
		$this->assertSame(count($all_user), 0);
	}

	// < Insertion Check >
	//
	// check whether user insertion work
	public function test_user_insertion() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		$all_user = User::fetchAll();
		$this->assertSame(count($all_user), 1);
	}
	// check whether user insertion work @boundary data1 [ email without @ ]
	public function test_user_insertion_boundary1() {
		User::deleteAllUsers();
		User::insertUser("hogehoge.com", "hogehoge", null);
		$all_user = User::fetchAll();
		$this->assertSame(count($all_user), 0);
	}
	// check whether user insertion work @boundary data2 [ email without @ and . ]
	public function test_user_insertion_boundary2() {
		User::deleteAllUsers();
		User::insertUser("hogehogecom", "hogehoge", null);
		$all_user = User::fetchAll();
		$this->assertSame(count($all_user), 0);
	}
	// check whether user insertion work @boundary data3 [ password length ]
	public function test_user_insertion_boundary3() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehog", null);
		$all_user = User::fetchAll();
		$this->assertSame(count($all_user), 0);
	}
	// check whether user insertion work @boundary data4 [ cookie not null ]
	public function test_user_insertion_boundary4() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", "hogehoge");
		$all_user = User::fetchAll();
		$this->assertSame(count($all_user), 0);
	}
	// check whether user insertion work @boundary data4 [ email already exists ]
	public function test_user_insertion_boundary5() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		$all_user = User::fetchAll();
		$this->assertSame(count($all_user), 1);
	}

	// < Fetch Check >
	//
	// check fetch by email
	public function test_user_fetch1_email() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		User::insertUser("hoge1@hoge.com", "hogehoge", null);
		$all_user = User::fetchAll();
		$this->assertSame(count($all_user), 2);
		$result = User::fetchByEmailSafe("hoge@hoge.com")[0];
		$this->assertSame($result == false, false);
		$this->assertSame($result["email"], "hoge@hoge.com");
	}
	// check fetch by email unsafe
	public function test_user_fetch2_email_unsafe() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		User::insertUser("hoge1@hoge.com", "hogehoge", null);
		$all_user = User::fetchAll();
		$this->assertSame(count($all_user), 2);
		$result = User::fetchByEmailUnSafe("hoge@hoge.com")[0];
		$this->assertSame($result == false, false);
		$this->assertSame($result["email"], "hoge@hoge.com");
		$this->assertSame($result["password"], "hogehoge");
	}
	// check fetch by id
	public function test_user_fetch3_id() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		User::insertUser("hoge1@hoge.com", "hogehoge", null);
		$all_user = User::fetchAll();
		$this->assertSame(count($all_user), 2);
		$result1 = User::fetchByEmailSafe("hoge@hoge.com")[0];
		$this->assertSame($result1 == false, false);
		$id2 = (int)$result1["id"] + 1;
		$result2 = User::fetchByIdSafe($id2)[0];
		$this->assertSame($result2["email"], "hoge1@hoge.com");
	}
	// check fetch by password and email [ success1 ]
	public function test_user_fetch4_email_and_password_success1() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		User::insertUser("hoge1@hoge.com", "hogehoge", null);
		$result = User::fetchByEmailAndPassword("hoge@hoge.com", "hogehoge");
		$this->assertSame($result, true);
	}
	public function test_user_fetch5_email_and_password_success2() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		User::insertUser("hoge1@hoge.com", "hogehoge", null);
		$result = User::fetchByEmailAndPassword("hoge1@hoge.com", "hogehoge");
		$this->assertSame($result, true);
	}
	// check fetch by password and email [ fail with email ]
	public function test_user_fetch6_email_and_password_fail_email() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		User::insertUser("hoge1@hoge.com", "hogehoge", null);
		$result = User::fetchByEmailAndPassword("hoge2@hoge.com", "hogehoge");
		$this->assertSame($result, false);
	}
	// check fetch by password and email [ fail with password ]
	public function test_user_fetch7_email_and_password_fail_password() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		User::insertUser("hoge1@hoge.com", "hogehoge", null);
		$result = User::fetchByEmailAndPassword("hoge@hoge.com", "hogehoge2");
		$this->assertSame($result, false);
	}
	// check fetch by password and email [ fail with email and password ]
	public function test_user_fetch8_email_and_password_fail_email_and_password() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		User::insertUser("hoge1@hoge.com", "hogehoge", null);
		$result = User::fetchByEmailAndPassword("hoge2@hoge.com", "hogehoge2");
		$this->assertSame($result, false);
	}

	// < Update Check >
	//
	// check update normal information
	public function test_user_update1_name() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		$user = User::fetchByEmailUnSafe("hoge@hoge.com")[0];
		$this->assertSame($user == false, false);
		$name = "example name";
		$update_result = User::updateUserNormal($user["id"], $user["cookie_value"], $name, "");
		$this->assertSame($update_result, true);
		$user_info = User::fetchByEmailSafe("hoge@hoge.com")[0];
		$this->assertSame($user_info["name"], $name);
	}
	public function test_user_update2_description() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		$user = User::fetchByEmailUnSafe("hoge@hoge.com")[0];
		$this->assertSame($user == false, false);
		$description = "Hello. This is my Introduction.";
		$update_result = User::updateUserNormal($user["id"], $user["cookie_value"], "", $description);
		$this->assertSame($update_result, true);
		$user_info = User::fetchByEmailSafe("hoge@hoge.com")[0];
		$this->assertSame($user_info["description"], $description);
	}
	public function test_user_update3_name_and_description() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		$user = User::fetchByEmailUnSafe("hoge@hoge.com")[0];
		$this->assertSame($user == false, false);
		$name = "Example Name";
		$description = "Hello. This is my Introduction.";
		$update_result = User::updateUserNormal($user["id"], $user["cookie_value"], $name, $description);
		$this->assertSame($update_result, true);
		$user_info = User::fetchByEmailSafe("hoge@hoge.com")[0];
		$this->assertSame($user_info["name"], $name);
		$this->assertSame($user_info["description"], $description);
	}
	// in email update test, we should test whether email is not duplicated
	public function test_user_update4_email() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		$user = User::fetchByEmailUnSafe("hoge@hoge.com")[0];
		$this->assertSame($user == false, false);
		$email = "hoge2@hoge.com";
		$update_result = User::updateUserEmail($user["id"], $user["cookie_value"], $email);
		$this->assertSame($update_result, true);
		$user_info_old = User::fetchByEmailSafe("hoge@hoge.com")[0];
		$this->assertSame($user_info_old, false);
		$user_info_new = User::fetchByEmailSafe("hoge2@hoge.com")[0];
		$this->assertSame($user_info_new == false, false);
		$this->assertSame($user_info_new["email"], $email);
	}
	// email duplication that results in fail
	public function test_user_update5_email_fail() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		User::insertUser("hoge1@hoge.com", "hogehoge", null);
		$user = User::fetchByEmailUnSafe("hoge@hoge.com")[0];
		$this->assertSame($user == false, false);
		$email = "hoge1@hoge.com";
		$update_result = User::updateUserEmail($user["id"], $user["cookie_value"], $email);
		$this->assertSame($update_result, false);
	}
	// password
	public function test_user_update6_password() {
		User::deleteAllUsers();
		User::insertUser("hoge@hoge.com", "hogehoge", null);
		$user = User::fetchByEmailUnSafe("hoge@hoge.com")[0];
		$this->assertSame($user == false, false);
		$new_password = "hogehoge2";
		$update_result = User::updateUserPassword($user["id"], $user["cookie_value"], $new_password);
		$this->assertSame($update_result, true);
		$user_check_result = User::fetchByEmailAndPassword("hoge@hoge.com", $new_password);
		$this->assertSame($user_check_result, true);
	}
}