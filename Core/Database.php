<?php

class Database
{
	private static 	$Name = "geekr";
	private static 	$User = "root";
	private static	$Password = "root";
	private static	$Address = "localhost";
	private static	$Port = 8889;

	protected function Connect()
	{
		try
		{
			$db = new PDO('mysql:host='.self::$Address.';port='.self::$Port.';dbname='.self::$Name, self::$User, self::$Password);
			return ($db);
		}
		catch (PDOException $e)
		{
			print("Error establishing a connection with the database: ".$e->getMessage()."</br>");
			die();
		}
        return (false);
	}

	public function Query($query)
	{
		$db = $this->Connect();
		$request = $db->query($query);
		$request->execute();
		$result = $request->fetchAll();
		return ($result);
	}

	public function Request($query, array $params, $needResult = false)
	{
		$db = $this->Connect();
		$request = $db->prepare($query);
		$request->execute($params);
		if ($needResult)
		{
			$results = $request->fetchAll();
			return ($results);
		}
        return (false);
	}

}
