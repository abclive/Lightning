<?php

Core::Uses(array('Modules/AdminHelper'));

class Home extends Bundle
{
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
			$this->bind(['name', $param[0]]);
		else
			$this->bind(['name', 'stranger']);
		$this->render();
	}
}
