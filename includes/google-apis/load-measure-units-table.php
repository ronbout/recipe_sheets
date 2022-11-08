<?php

defined('ABSPATH') or die('Direct script access disallowed.');

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

function import_units_data_from_sheets() {
	$sheets = initializeSheets();
	$units_data = get_units_data($sheets);
	
	echo '<h2>Measure Units Found Count: ', count($units_data), "</h2>";
	// echo '<pre>';
	// print_r($units_data);
	// echo '</pre>';
	// die;
	
	$insert_cnt = load_measure_units_table($units_data);
	
	echo "<h2>$insert_cnt Ingredients Inserted</h2>";
}

function get_units_data($sheets) {
	try{

			$spreadsheetId = JUNE_WORKING_DOC_ID;
			$range = 'Units!A2:F';
			$response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
			$values = $response->getValues();
			return $values;
		}
		catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo 'Message: ' .$e->getMessage();
		}
}

function load_measure_units_table($units_data){
	global $wpdb;

	$sql = "DELETE FROM tc_measure_units WHERE 1";
	$wpdb->query($sql);
	$sql = "ALTER TABLE tc_measure_units AUTO_INCREMENT = 1";
	$wpdb->query($sql);


	$insert_sql = "
		INSERT INTO tc_measure_units 
			(name, normalized, pluralized, depluralize, mark, derivative, cheese)
			VALUES
	";

	$insert_cnt = 0;
	foreach($units_data as $ingred_info) {
		$name = $ingred_info[UNITS_NAME_COL];
		$normalized = isset($ingred_info[UNITS_NORMALIZED_COL]) ? $ingred_info[UNITS_NORMALIZED_COL] : '';
		$pluralized = isset($ingred_info[UNITS_PLURALIZED_COL]) ? $ingred_info[UNITS_PLURALIZED_COL] : '';
		$depluralize = isset($ingred_info[UNITS_DEPLURALIZE_COL]) ? $ingred_info[UNITS_DEPLURALIZE_COL] : '';
		$mark = isset($ingred_info[UNITS_MARK_COL]) ? $ingred_info[UNITS_MARK_COL] : '';
		$derivative = isset($ingred_info[UNITS_DERIVATIVE_COL]) ? $ingred_info[UNITS_DERIVATIVE_COL] : '';
		$cheese = isset($ingred_info[UNITS_CHEESE_COL]) ? $ingred_info[UNITS_CHEESE_COL] : '';

		$sql = $insert_sql . "(%s, %s, %s, %s, %s, %s, %s)";
		$parms = array($name, $normalized, $pluralized, $depluralize, $mark, $derivative, $cheese);

		$sql = $wpdb->prepare($sql, $parms);
		$result = $wpdb->query($sql);

		if (!$result) {
			echo "<h1>Error inserting $name</h1>";
			echo "<h2>Error: ", $wpdb->error, "</h2>";
			die;
		}
		$insert_cnt++;

	}
	return $insert_cnt;
}
