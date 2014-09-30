<?php

class Configuration
{

	/*
	**	Locale
	**	=================
	**	Default language locale fetched on the database
	**	and printed on the site
	*/
	public static	$default_locale = "fr";
	public static	$available_locales = array("fr", "en");

	/*
	**	Routes
	**	=================
	**	@routes_params: Typical route structure
	**	@base_dir: Site base address
	*/
	public static	$base_dir = "http://localhost/Lightning";
	public static	$routes_params = array("locale", "bundle", "action");
	public static	$default_method_call = "index";
	public static	$index_bundle = "Home";
}
