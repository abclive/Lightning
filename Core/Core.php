<?php

$core = new Core();

class Core
{
	public function __construct()
	{
		self::LoadFolder("Core");
		self::LoadFile("Bundles/Bundle.php");
		self::LoadFile("Modules/Module.php");
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
		self::LoadDependencies($bundle);
		self::LoadFile("Bundles/".$bundle.".php");
		$action = strtolower($action);
		$reflection = new ReflectionClass($bundle);
		if ($reflection->hasMethod($action))
		{
			$loaded_bundle = $reflection->newInstance();
			$loaded_bundle->current_action = $action;
			$loaded_bundle->bundle_name = $bundle;
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

	public static function LoadFolder($folder)
	{
		foreach (glob($folder."/*.php") as $filename)
			require_once($filename);
	}

	public static function LoadDependencies($bundle_name)
	{
		$filename = Configuration::$dependencies_folder."/".$bundle_name.".lbdp";
		if (file_exists($filename) === true)
		{
			$file_content = file_get_contents($filename);
			$pos = strpos($file_content, "<-");
			if ($pos !== false)
			{
				$line = substr($file_content, $pos + 3);
				$dependencies = explode(", ", trim($line));
				foreach ($dependencies as $dep)
				{
					$dependency = explode(":", $dep);
					if ($dependency[0] === "Bundle")
						self::LoadFile("Bundles/".$dependency[1].".php");
					else if ($dependency[0] === "Module")
						self::LoadFile("Modules/".$dependency[1].".php");
				}
			}
			else
				throw new Exception("Error on dependencies parsing in file: ".$filename, 101);
		}
	}

	public static function LoadFile($filename)
	{
		if (file_exists($filename))
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
