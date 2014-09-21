<?php

class Database
{
	protected static 	$Name = "geekr";
	protected static 	$User = "root";
	protected static	$Password = "root";
	protected static	$Adress = "localhost";
	protected static	$Port = 8889;

	protected static function Connect()
	{
		try
		{
			$db = new PDO('mysql:host='.self::$Adress.';port='.self::$Port.';dbname='.self::$Name, self::$User, self::$Password);
			return ($db);
		}
		catch (PDOException $e)
		{
			print("Error etasblishing a connection with the database: ".$e->getMessage()."</br>");
			die();
		}
	}

	public static function Query($query)
	{
		$db = self::Connect();
		$request = $db->query($query);
		$request->execute();
		$result = $request->fetchAll();
		return ($result);
	}

	public static function Request($query, array $params, $needResult = false)
	{
		$db = self::Connect();
		$request = $db->prepare($query);
		$request->execute($params);
		if ($needResult)
		{
			$results = $request->fetchAll();
			return ($results);
		}
	}

}
