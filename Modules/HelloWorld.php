<?php

class HelloWorld extends Module
{
	public $name;

	public static function Hello()
	{
		echo "Hello World";
	}

	public function Greeting()
	{
		echo "Hello ".$this->name;
	}
}
