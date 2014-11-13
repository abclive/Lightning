<?php

class Core
{
	private static $routes_params = array("locale", "bundle", "action");

	public function __construct()
	{
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

	protected static function LoadBundle($bundle, $action = "index", array $parameter = null)
	{
		$bundle = ucfirst(strtolower($bundle));
		if (self::CheckCache($bundle) == true)
			self::LoadDependencies($bundle);
		else
			self::CreateDependencies($bundle);
		self::LoadFile("Bundles/".$bundle.".php");
		$action = strtolower($action);
		$reflection = new ReflectionClass($bundle);
		if ($reflection->hasMethod($action))
		{
			$loaded_bundle = $reflection->newInstance();
			$loaded_bundle->current_action = $action;
			$loaded_bundle->bundle_name = $bundle;
			$loaded_bundle->db = new Database();
			if ($reflection->hasProperty(Configuration::$module_variable))
			{
				$modules = $reflection->getProperty(Configuration::$module_variable);
				$modules = $modules->getValue($loaded_bundle);
				foreach ($modules as $m)
					$loaded_bundle->modules[$m] = new $m();
				foreach ($loaded_bundle->modules as $module)
					$module->db = new Database();
			}
			foreach ($loaded_bundle->modules as $module)
				$module->OnBundleLoaded($loaded_bundle);
			if ($parameter != null)
			{
				foreach ($loaded_bundle->modules as $module)
					$module->HasRouteParams($loaded_bundle, $action, $parameter);
			}
			if ($reflection->hasMethod("beforeFilter"))
				$loaded_bundle->beforeFilter();
			if ($parameter != null)
				$loaded_bundle->$action($parameter);
			else
				$loaded_bundle->$action();
			if ($reflection->hasMethod("afterFilter"))
				$loaded_bundle->afterFilter();
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

	protected static function LoadDependencies($bundle_name)
	{
		$filename = "Cache/dependencies/".$bundle_name;
		if (file_exists($filename))
		{
			$file_content = file_get_contents($filename);
			$pos = strpos($file_content, " -- ");
			if ($pos !== false)
			{
				$file_content = substr($file_content, $pos + 4);
				$dependencies = explode(",", trim($file_content));
				$dependencies = explode(", ", trim($file_content));
				foreach ($dependencies as $dep)
				{
					$dependency = explode(":", $dep);
					if ($dependency[0] === "Bundle")
						self::LoadFile("Bundles/".$dependency[1].".php");
					else if ($dependency[0] === "Module")
						self::LoadFile("Modules/".$dependency[1].".php");
				}
			}
		}
		else
			self::CreateDependencies($bundle_name);
	}

	protected static function CreateDependencies($bundle_name)
	{
		$filename = "Bundles/".$bundle_name.".php";
		if (file_exists($filename))
		{
			$content = null;
			$file_content = file_get_contents($filename);
			$modules_dependencies = self::GetDependencies(Configuration::$module_variable, $file_content);	
			$bundle_dependencies = self::GetDependencies(Configuration::$bundle_variable, $file_content);
			if ($modules_dependencies !== false)
			{
				$modules_dependencies = array_values($modules_dependencies);
				foreach ($modules_dependencies as $key => $md)
				{
					$md = "Module:".$md;
					if ((count($modules_dependencies) === 1  || $key - 1 === count($modules_dependencies)) && $bundle_dependencies === false)
						$content = $content.$md;
					else
						$content = $content.$md.", ";
				}
			}
			if ($bundle_dependencies !== false)
			{
				$bundle_dependencies = array_values($bundle_dependencies);
				foreach ($bundle_dependencies as $key => $md)
				{
					$md = "Bundle:".$md;
					if ($key + 1 === count($bundle_dependencies))
						$content = $content.$md;
					else
						$content = $content.$md.", ";
				}
			}
			if (isset($content))
			{
				$content = filemtime($filename)." -- ".$content;
				$filename = "Cache/dependencies/".$bundle_name;
				if (file_exists($filename))
					unlink($filename);
				file_put_contents($filename, $content);
				self::LoadDependencies($bundle_name);
			}
		}
	}

	protected static function GetDependencies($variable, $file_content)
	{
		$pos = strpos($file_content, $variable);
		if ($pos !== false)
		{
			$pos = strpos($file_content, "(", $pos + strlen($variable));
			$endpos = strpos($file_content, ")", $pos);
			if ($endpos !== false)
			{
				$dependency_content = substr($file_content, $pos + 1, $endpos - $pos - 1);
				$dependency_content = explode('"', $dependency_content);
				$dependency_content = array_filter($dependency_content);
				$dependency_content = array_diff($dependency_content, array(", ", ','));
				return ($dependency_content);
			}
		}
		return (false);
	}

	protected static function CheckCache($bundle_name)
	{
		$filename = "Bundles/".$bundle_name.".php";
		if (file_exists($filename))
		{
			$lastedit = filemtime($filename);
			$filename = "Cache/dependencies/".$bundle_name;
			if (file_exists($filename))
			{
				$file_content = file_get_contents($filename);
				$pos = strpos($file_content, " -- ");
				$time = substr($file_content, 0, $pos);
				if ($time == $lastedit)
					return (true);
				return (false);
			}
			return (false);
		}
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
}
