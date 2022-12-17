<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.9-dev
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2019 Fuel Development Team
 * @link       https://fuelphp.com
 */

return array(
	/**
	 * -------------------------------------------------------------------------
	 *  Default route
	 * -------------------------------------------------------------------------
	 *
	 */

	'_root_' => 'welcome/index',

	/**
	 * -------------------------------------------------------------------------
	 *  Page not found
	 * -------------------------------------------------------------------------
	 *
	 */

	'_404_' => 'welcome/404',

	/**
	 * -------------------------------------------------------------------------
	 *  Example for Presenter
	 * -------------------------------------------------------------------------
	 *
	 *  A route for showing page using Presenter
	 *
	 */

	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
	// users
	'users/show(/:id)?' => array('users/show', 'name' => 'user_show'),
	'users/edit' => array('users/edit', 'name' => 'user_edit'),
	'users/session/new' => array('users/session_new', 'name' => 'user_session_new'),
	'users/session/create' => array('users/session_create', 'name' => 'user_session_create'),
	// tweets
	'tweets/show(/:id)' => array('tweets/show', 'name' => 'tweet_show'),
	'tweets/new' => array('tweets/new', 'name' => 'tweet_new'),
	'tweets/create' => array('tweets/create', 'name' => 'tweet_new'),
	'tweets/retweet(/:id)' => array('tweets/retweet', 'name' => 'tweet_retweet'),
	'tweets/unretweet(/:id)' => array('tweets/unretweet', 'name' => 'tweet_unretweet'),
	'tweets/reply(/:id)' => array('tweets/reply', 'name' => 'tweet_reply'),
);
