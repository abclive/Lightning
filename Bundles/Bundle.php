<?php

class Bundle
{
	public $bundle_name;
	public $current_action;
	public $data;

	public function __construct()
	{
		$this->bundle_name = get_class($this);
	}

	public function bind(array $params)
	{
		$keys = array_keys($params);
		if (is_array($params[$keys[0]]))
		{
			foreach ($params as $key => $value)
			{
				foreach ($value as $v)
					$this->data[$key][] = $v;
			}
		}
		else if (count($params) == 2)
			$this->data[$params[0]] = $params[1];
		else
			throw new Exception("Bundle::bind() takes an array with at least 2 entries.", 201);
	}

	public function render($layout_name = null)
	{
		foreach ($this->data as $varname => $data)
			$$varname = $data;

		if (isset($layout_name))
			require_once("Views/Layouts/".$layout_name.".php");
		else
			require_once("Views/Layouts/".Configuration::$default_layout.".php");
		require_once("Views/".$this->bundle_name."/".$this->current_action.".php");
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
			$request = $request.strtolower($this->bundle_name);


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
			$request = $request.strtolower($this->bundle_name);

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
