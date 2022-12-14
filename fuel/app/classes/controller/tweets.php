<?php

class Controller_Tweets extends Controller_Template
{

	public function action_show()
	{
		$data["subnav"] = array('show'=> 'active' );
		$this->template->title = 'Tweets &raquo; Show';
		$this->template->content = View::forge('tweets/show', $data);
	}

	public function action_index()
	{
		$data["subnav"] = array('index'=> 'active' );
		$this->template->title = 'Tweets &raquo; Index';
		$this->template->content = View::forge('tweets/index', $data);
	}

	public function action_new()
	{
		$data["subnav"] = array('new'=> 'active' );
		$this->template->title = 'Tweets &raquo; New';
		$this->template->content = View::forge('tweets/new', $data);
	}

	public function action_create()
	{
		$data["subnav"] = array('create'=> 'active' );
		$this->template->title = 'Tweets &raquo; Create';
		$this->template->content = View::forge('tweets/create', $data);
	}

}
