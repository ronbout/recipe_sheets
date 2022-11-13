<?php

defined('ABSPATH') or die('Direct script access disallowed.');

// Load the Google API PHP Client Library.
require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/vendor/autoload.php';

function generate_ingredients_report($report_id, $sheet_name="Ingredients") {
	global $wpdb; 

	$sheets = initializeSheets();
	
	$sql = "
		SELECT ingred.* 
		FROM tc_ingredients ingred
		ORDER BY ingred.name ASC
	";

	$ingreds = $wpdb->get_results($sql, ARRAY_A);
	
	$report_data = array_values_multi($ingreds);
	create_report($sheets, $report_id, $sheet_name, $report_data);	
}

function generate_measure_units_report($report_id, $sheet_name="Units") {
	global $wpdb; 

	$sheets = initializeSheets();
	
	$sql = "
		SELECT units.* 
		FROM tc_measure_units units
		ORDER BY units.name ASC
	";

	$units = $wpdb->get_results($sql, ARRAY_A);
	
	$report_data = array_values_multi($units);
	create_report($sheets, $report_id, $sheet_name, $report_data);	
}