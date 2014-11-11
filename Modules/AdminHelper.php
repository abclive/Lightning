<?php

class AdminHelper extends Module
{
	public function OnBundleLoaded($bundle)
	{
		echo "Module AdminHelper loaded from ".$bundle->bundle_name;
	}
}
