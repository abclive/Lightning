<?php

class AdminHelper extends Module
{
	public function OnBundleLoaded($bundle)
	{
		echo "Module AdminHelper loaded from ".$bundle->bundle_name."<br/>";
	}

	public function HasRouteParams($bundle, $action, $params)
	{
		echo "AdminHelper HasRouteParams loaded from ".$bundle->bundle_name." with params: ";
		if ($params[0] === "admin" && strpos($action, "admin_") === false)
		{
			unset($params[0]);
			if (count($params) > 0)
				Core::Redirect(array($bundle->bundle_name, "admin_".$action, $params));
			else
				Core::Redirect(array($bundle->bundle_name, "admin_".$action));
		}
	}

	public function OnBundleRendered($bundle)
	{
		echo "AdminHelper OnBundleRendered loaded from ".$bundle->bundle_name;
	}
}
