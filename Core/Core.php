<?php

class Core
{
	private static $routes_params = array("locale", "bundle", "action");
	public static $module_dependencies = array();
	public static $bundle_dependencies = array();

	public function __construct()
	{
		print_r("Bonjour");
		self::LoadFolder("Core");
		self::LoadFile("Bundles/Bundle.php");
		self::LoadFile("Modules/Module.php");
		$exceptions = array();
		if (array_search("locale", self::$routes_params) !== false)
		{
			$exceptions[] = array(
				"param" => "locale",
				"exceptions" => Configuration::$available_locales
			);
		}
		Route::start(Configuration::$base_dir, self::$routes_params, $exceptions);
	}

	protected static function Uses(array $dependencies)
	{
		foreach ($dependencies as $dep)
		{
			if (file_exists($dep.".php"))
			{
				require_once($dep.".php");
				if (strstr($dep, "Modules"))
				{
					$module = substr($dep, strpos($dep, "/") + 1);
					self::$module_dependencies[] = new $module;
				}
				else if (strstr($dep, "Bundles"))
				{
					$bundle = substr($dep, strpos($dep, "/") + 1);
					self::$bundle_dependencies[] = new $bundle;
				}
			}
		}
	}

	protected static function LoadBundle($bundle, $action = "index", array $parameter = null)
	{
		$bundle = ucfirst(strtolower($bundle));
		self::LoadFile("Bundles/".$bundle.".php");
		$action = strtolower($action);
		try {
			$reflection = new ReflectionClass($bundle);
		}
		catch (Exception $e) {
			throw new Exception("Bundle not found", 404);
		}
		if ($reflection->hasMethod($action))
		{
			$loaded_bundle = $reflection->newInstance();
			$loaded_bundle->current_action = $action;
			$loaded_bundle->bundle_name = $bundle;
			$loaded_bundle->db = new Database();
			foreach (self::$module_dependencies as $module)
			{
				$module->db = new Database();
				$module->OnBundleLoaded($loaded_bundle);
			}
			if ($parameter != null)
			{
				foreach (self::$module_dependencies as $module)
					$module->HasRouteParams($loaded_bundle, $action, $parameter);
			}
			if ($reflection->hasMethod("beforeFilter"))
			{
				foreach (self::$module_dependencies as $module)
					$module->BeforeFilter($loaded_bundle);
				$loaded_bundle->beforeFilter();
			}
			if ($parameter != null)
				$loaded_bundle->$action($parameter);
			else
				$loaded_bundle->$action();
			if ($reflection->hasMethod("afterFilter"))
			{
				$loaded_bundle->afterFilter();
				foreach (self::$module_dependencies as $module)
					$module->AfterFilter($loaded_bundle);
			}
		}
		else
			throw new Exception("Page not found", 404);
	}

	public static function Redirect(array $args)
	{
		if (count($args) >= 2)
		{
			if (count($args) == 2)
				self::LoadBundle($args[0], $args[1]);
			else
			{
				$bundle = $args[0];
				$action = $args[1];
				unset($args[0]);
				unset($args[1]);
				self::LoadBundle($bundle, $action, $args);
			}
		}
	}

	public function RedirectRoute()
	{
		if (isset($_GET['bundle']))
		{
			if (self::RouteHasParameter() === true)
			{
				$parameter = array_slice($_GET, (isset($_GET['locale'])) ? count(self::$routes_params) : count(self::$routes_params) - 1);
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

	public static function LoadFile($filename)
	{
		if (file_exists($filename))
			require_once($filename);
	}

	protected static function RouteHasParameter()
	{
		if (isset($_GET['locale']) && count($_GET) > count(self::$routes_params))
			return (true);
		else if (!isset($_GET['locale']) && count($_GET) > count(self::$routes_params) - 1)
			return (true);
		return (false);

	}

	public function HandleError(Exception $e)
	{
		$error = $e;
		if (file_exists("Views/Layouts/error.php"))
			require_once("Views/Layouts/error.php");
	}
}
