<?php

defined('ABSPATH') or die('Direct script access disallowed.');

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

function import_ingredients_data_from_sheets() {
	$sheets = initializeSheets();
	$ingred_data = get_ingredient_data($sheets);
	
	echo '<h2>Ingredients Found Count: ', count($ingred_data), "</h2>";
	// echo '<pre>';
	// print_r($ingred_data);
	// echo '</pre>';
	// die;
	
	$insert_cnt = load_ingredients_table($ingred_data);
	
	echo "<h2>$insert_cnt Ingredients Inserted</h2>";
}

function get_ingredient_data($sheets) {
	try{

			$spreadsheetId = JUNE_WORKING_DOC_ID;
			$range = 'Ingredients!A2:F';
			$response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
			$values = $response->getValues();
			return $values;
		}
		catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo 'Message: ' .$e->getMessage();
		}
}

function load_ingredients_table($ingred_data){
	global $wpdb;

	$sql = "DELETE FROM tc_ingredients WHERE 1";
	$wpdb->query($sql);
	$sql = "ALTER TABLE tc_ingredients AUTO_INCREMENT = 1";
	$wpdb->query($sql);


	$insert_sql = "
		INSERT INTO tc_ingredients 
			(name, normalized, pluralized, depluralize, derivative, mince)
			VALUES
	";

	$insert_cnt = 0;
	foreach($ingred_data as $ingred_info) {
		$name = $ingred_info[INGRED_NAME_COL];
		$normalized = isset($ingred_info[INGRED_NORMALIZED_COL]) ? $ingred_info[INGRED_NORMALIZED_COL] : '';
		$pluralized = isset($ingred_info[INGRED_PLURALIZED_COL]) ? $ingred_info[INGRED_PLURALIZED_COL] : '';
		$depluralize = isset($ingred_info[INGRED_DEPLURALIZE_COL]) ? $ingred_info[INGRED_DEPLURALIZE_COL] : '';
		$derivative = isset($ingred_info[INGRED_DERIVATIVE_COL]) ? $ingred_info[INGRED_DERIVATIVE_COL] : '';
		$mince = isset($ingred_info[INGRED_MINCE_COL]) ? $ingred_info[INGRED_MINCE_COL] : '';

		$sql = $insert_sql . "(%s, %s, %s, %s, %s, %s)";
		$parms = array($name, $normalized, $pluralized, $depluralize, $derivative, $mince);

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
