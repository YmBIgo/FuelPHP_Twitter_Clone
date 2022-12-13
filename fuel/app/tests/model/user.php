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
	// check fetch by id
	public function test_user_fetch2_id() {
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
}