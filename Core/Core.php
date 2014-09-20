<?php

$core = new Core();

class Core
{
	public function __construct()
	{
		self::LoadDependencies("Core");
		self::LoadDependencies("Bundles");
		$exceptions = array();
		if (array_search("locale", Configuration::$routes_params) !== false)
		{
			$exceptions[] = array(
				"param" => "locale",
				"exceptions" => Configuration::$available_locales
			);
		}
		Route::start(Configuration::$base_dir, Configuration::$routes_params, $exceptions);
	}

	public static function LoadBundle($bundle, $action = "index", array $parameter = null)
	{
		$bundle = ucfirst(strtolower($bundle));
		$action = strtolower($action);
		$reflection = new ReflectionClass($bundle);
		if ($reflection->hasMethod($action))
		{
			$loaded_bundle = $reflection->newInstance();
			if ($parameter != null)
				$loaded_bundle->$action($parameter);
			else
				$loaded_bundle->$action();
		}
		else
			throw new Exception("Page not found", 404);
	}

	public static function Redirect()
	{
		if (isset($_GET['bundle']))
		{
			if (self::RouteHasParameter() === true)
			{
				$parameter = array_slice($_GET, (isset($_GET['locale'])) ? count(Configuration::$routes_params) : count(Configuration::$routes_params) - 1);
				if ($parameter != null)
					self::LoadBundle($_GET['bundle'], (isset($_GET['action'])) ? $_GET['action'] : Configuration::$default_method_call, $parameter);
				else
					throw new Exception("Page not found", 404);
			}
			else
				self::LoadBundle($_GET['bundle'], (isset($_GET['action'])) ? $_GET['action'] : Configuration::$default_method_call);
		}
		else
			self::LoadBundle(Configuration::$index_bundle, Configuration::$default_method_call);
	}

	public static function LoadDependencies($folder)
	{
		foreach (glob($folder."/*.php") as $filename)
			require_once($filename);
	}

	public static function RouteHasParameter()
	{
		if (isset($_GET['locale']) && count($_GET) > count(Configuration::$routes_params))
			return (true);
		else if (!isset($_GET['locale']) && count($_GET) > count(Configuration::$routes_params) - 1)
			return (true);
		return (false);

	}
}

?>