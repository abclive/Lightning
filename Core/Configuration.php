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
	**	@base_dir: Site base address
	**	@default_method_call: If not specified in the adress called by default method.
	**	@index_bundle: The root bundle
	*/
	public static	$base_dir = "http://localhost/Lightning";
	public static	$default_method_call = "index";
	public static	$index_bundle = "Home";

	/*
	**	Dependencies
	**	=================
	**	@bundle_variable: The name of the bundle dependency variable.
	**	@module_variable: The name of the module dependency variable.
	*/
	public static	$bundle_variable = "using_bundles";
	public static	$module_variable = "using_modules";

	/*
	**	Views
	**	=================
	**	@default_layout: Name of the default layout file name
	*/
	public static	$default_layout = "default";
}
