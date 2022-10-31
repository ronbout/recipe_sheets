<?php

defined('ABSPATH') or die('Direct script access disallowed.');

function convert_recipe_title($title) {
	$new_title = ucwords($title);
	$new_title = str_replace(' And ', ' & ');
	$new_title = str_replace(' With ', ' with ');
	$new_title = str_replace(' In ', ' in ');
	
	return $new_title;
}

function convert_recipe_desc($desc) {
	$new_desc = ucwords($desc);
	$new_desc = str_replace(' And ', ' & ');
	$new_desc = str_replace(' With ', ' with ');
	$new_desc = str_replace(' In ', ' in ');
	
	return $new_desc;
}