<?php

require_once("Core/Core.php");

$core = new Core();
try {
	$core->RedirectRoute();
}
catch (Exception $e)
{
	$core->HandleError($e);
}

