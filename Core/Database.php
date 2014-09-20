<?php

class Database
{
	protected static 	$Name = "geekr";
	protected static 	$User = "root";
	protected static	$Password = "root";
	protected static	$Adress = "localhost";
	protected static	$Port = 8889;

	protected function Connect()
	{
		try
		{
			$db = new PDO('mysql:host='.Database::$Adress.';port='.Database::$Port.';dbname='.Database::$Name, Database::$User, Database::$Password);
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
		$db = Database::Connect();
		$request = $db->query($query);
		$request->execute();
		$result = $request->fetchAll();
		return ($result);
	}

	public static function Request($query, array $params, $needResult)
	{
		$db = Database::Connect();
		$request = $db->prepare($query);
		$request->bindParams($params);
		$request->execute();
		if ($needResult)
		{
			$results = $request->fetchAll();
			return ($results);
		}
	}

}

?>