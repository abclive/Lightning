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
		$this->save(array('streams',
			'rows' => array('title', 'description'),
			'values' => array('Hello', 'World')
		));
	}

	public function greeting(array $param)
	{
		echo "Hello ".$param[0];
	}
}
