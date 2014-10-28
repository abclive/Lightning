<?php

class Home extends Bundle
{
	public function index()
	{
		$this->render();
	}

	public function greeting(array $param)
	{
		$this->bind(array('name', $param[0]));
		$this->render();
	}
}
