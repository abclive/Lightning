<?php

class Module extends Core
{
	public function OnBundleLoaded($bundle) {}

	public function BeforeFilter($bundle) {}

	public function AfterFilter($bundle) {}

	public function HasRouteParams($bundle, $action, $params) {}

	public function OnBundleRendered($bundle) {}
}
