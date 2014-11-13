<?php

class Home extends Bundle
{
	public $using_modules = array("AdminHelper");

	public function admin_index()
	{
		$this->render('admin');
	}

	public function index()
	{
		$this->render();
	}

	public function greeting(array $param = null)
	{
		if (isset($param))
			$this->bind(array('name', $param[0]));
		else
			$this->bind(array('name', 'stranger'));
		$this->render();
	}
}
