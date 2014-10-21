<?php

class Home extends Bundle
{
	public $using_modules = array('HelloWorld');

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		HelloWorld::Hello();
	}

	public function greeting(array $param)
	{
		$test = new HelloWorld();
		$test->name = $param[0];
		$test->Greeting();
	}
}
