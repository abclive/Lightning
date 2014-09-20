<?php

class Bundle
{
	public $bundle_name;

	public function __construct()
	{
		$bundle_name = get_class($this);
	}

	public function fetch(array $query = array())
	{
		$request = "SELECT ";
		if (isset($query['conditions']['row']))
			$request = $request.$query['conditions']['row']." FROM ";
		else
			$request = $request."* FROM ";

		if (isset($query[0]))
			$request = $request.strtolower($query[0])." ";
		else
			$request = $request.strtolower(get_class($this));


		if (isset($query['conditions']))
		{
			if (isset($query['conditions']['where']))
				$request = $request." WHERE ".$query['conditions']['where'][0]."='".$query['conditions']['where'][1]."'";
			if (isset($query['conditions']['limit']))
				$request = $request." LIMIT ".$query['conditions']['limit'][0]." , ".$query['conditions']['limit'][1];
			if (isset($query['conditions']['order']) && isset($query['conditions']['order']['by']))
			{
				$request = $request." ORDER BY ".$query['conditions']['order']['by'];
				if (isset($query['conditions']['order'][0]) && strtoupper($query['conditions']['order'][0]) === "DESC")
					$request = $request." ".strtoupper($query['conditions']['order'][0]);
				else if (isset($query['conditions']['order'][0]) && strtoupper($query['conditions']['order'][0]) === "ASC")
					$request = $request." ".strtoupper($query['conditions']['order'][0]);
			}
		}

		$result = Database::Query($request);
		return ($result);

	}
}

?>