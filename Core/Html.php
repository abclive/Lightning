<?php

class Html
{
	public static function LinkCSS($filename)
	{
		$link = Configuration::$base_dir."/Ressources/css/".$filename;
		return($link);
	}

	public static function LinkImage($filename)
	{
		$link = Configuration::$base_dir."/Ressources/images/".$filename;
		return($link);
	}

	public static function LinkJS($filename)
	{
		$link = Configuration::$base_dir."/Ressources/js/".$filename;
		return($link);
	}

	public static function Link($bundle, $action, array $params = null)
	{
		if (!isset($params))
			return(Configuration::$base_dir."/".$bundle."/".$action);
		else
		{
			$link = Configuration::$base_dir."/".$bundle."/".$action;
			foreach ($params as $p)
				$link =  $link."/".$p;
			return ($link);
		}
	}
}
