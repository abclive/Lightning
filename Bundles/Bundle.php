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

	public function save(array $data)
	{
		$request = "INSERT INTO ";

		if (isset($data[0]))
			$request = $request.$data[0];
		else
			$request = $request.strtolower(get_class($this));

		if (isset($data['rows']) && is_array($data['rows']))
		{
			$request = $request." (";
			foreach ($data['rows'] as $key => $value)
			{
				if ($key < count($data['rows']) - 1)
					$request = $request.$value.", ";
				else
					$request = $request.$value.")";
			}
		}

		if (isset($data['values']))
		{
			$request = $request." VALUES (";
			foreach ($data['values'] as $key => $value)
			{
				if ($key < count($data['values']) - 1)
					$request = $request."?, ";
				else
					$request = $request."?)";
			}
		}
		else
			throw new Exception("Bundle::save() require a valid data array", 1001);

		Database::Request($request, $data['values']);
	}
}
