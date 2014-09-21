<?php

class Home extends Bundle
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		echo "Hello World";
	}

	public function greeting(array $param)
	{
		echo "Hello ".$param[0];
	}
}
