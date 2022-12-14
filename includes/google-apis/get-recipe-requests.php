<?php

defined('ABSPATH') or die('Direct script access disallowed.');

// Load the Google API PHP Client Library.
// and access <<MONTH>> Brief WorkBook
require_once __DIR__ . '/vendor/autoload.php';

define('CUISINE_COL', 0);
define('MEAL_TYUPE_COL', 1);
define('CLASS_COL', 2);
define('DIETARY_COL', 3);
define('PREP_TIME_COL', 4);
define('EQUIPMENT_COL', 5);
define('RECIPE_COUNT_COL', 6);
define('NOTES_COL', 7);

$working_month = '2022-06-01';

$sheets = initializeSheets();
$recipe_data = getData($sheets);

// echo '<h1>Count: ', count($recipe_data), "</h1>";
// echo '<pre>';
// print_r($recipe_data);
// echo '</pre>';
// die;

load_recipe_request_table($recipe_data, $working_month);

/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return An authorized Analytics Reporting API V4 service object.
 */
function initializeSheets()
{

	// Use the developers console and download your service account
	// credentials in JSON format. Place them in this directory or
	// change the key file location if necessary.
	$KEY_FILE_LOCATION = __DIR__ . '/credentials.json';

	// Create and configure a new client object.
	$client = new Google_Client();
	$client->setApplicationName("Google Sheets");
	$client->setAuthConfig($KEY_FILE_LOCATION);
	$client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);
	$sheets = new Google\Service\Sheets($client);

	return $sheets;
}


function getData($sheets) {
	try{

			// $spreadsheetId = '1XNONqFyWBN5qX-1fSt8zMZ7TMVkEsgPDb_H1OL6fc5Q';
			$spreadsheetId = '1UOZnbKQ6dq3H1cbMoyJgFVOjvfQFkwNgXUZtVRLicpU';
			$range = 'Wholly Owned  May 2022!B4:I';
			// $renderOption = array('valueRenderOption' => 'FORMULA');
			$response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
			$values = $response->getValues();
			return $values;
		}
		catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo 'Message: ' .$e->getMessage();
		}
}

function load_recipe_request_table($data, $dt) {
	global $wpdb;

	$recipe_requests_table = "rg_recipe_guru_requests_brief";

	$db_result = $wpdb->delete($recipe_requests_table, array('month_year' => $dt));

	$current_vals = reset_current_vals();
	$requests_list = array();

	foreach($data as $ndx => $recipe_row) {
		if (!count($recipe_row)) {
			continue;
		}
		if ($recipe_row[RECIPE_COUNT_COL]) {
			if ($current_vals['recipe_count'] && $current_vals['classification']) {
				// store current request vals and start a new request
				$requests_list[] = $current_vals;
				$current_vals = reset_current_vals();
			}
		}

		load_current_vals($current_vals, $recipe_row);
	
	}
	if ($current_vals['recipe_count'] && $current_vals['classification']) {
		// store current request vals and start a new request
		$requests_list[] = $current_vals;
		$current_vals = reset_current_vals();
	}
	update_request_table($requests_list, $dt, $recipe_requests_table);
}

function reset_current_vals() {
		$current_vals = array( 
			'cuisine' => null,
			'meal_type' => null,
			'classification' => null,
			'dietary' => null,
			'prep_time' => null,
			'equipment' => null,
			'recipe_count' => null, 
			'notes' => null,
	);
}

function load_current_vals(&$current_vals, $recipe_row) {
	if ($recipe_row[CUISINE_COL]) {
		$current_vals['cuisine'] = sanitize_text_field($recipe_row[CUISINE_COL]);
	}
	if ($recipe_row[MEAL_TYUPE_COL]) {
		$current_vals['meal_type'] = sanitize_text_field($recipe_row[MEAL_TYUPE_COL]);
	}
	if ($recipe_row[CLASS_COL]) {
		$current_vals['classification'] = sanitize_text_field($recipe_row[CLASS_COL]);
	}
	if ($recipe_row[DIETARY_COL]) {
		$current_vals['dietary'] = sanitize_text_field($recipe_row[DIETARY_COL]);
	}
	if ($recipe_row[PREP_TIME_COL]) {
		$current_vals['prep_time'] = sanitize_text_field($recipe_row[PREP_TIME_COL]);
	}
	if ($recipe_row[EQUIPMENT_COL]) {
		$current_vals['equipment'] = sanitize_text_field($recipe_row[EQUIPMENT_COL]);
	}
	if ($recipe_row[RECIPE_COUNT_COL]) {
		$current_vals['recipe_count'] = sanitize_text_field($recipe_row[RECIPE_COUNT_COL]);
	}
	if ($recipe_row[NOTES_COL]) {
		$current_vals['notes'] = sanitize_text_field($recipe_row[NOTES_COL]);
	}
}

function update_request_table($requests_list, $dt, $requests_table) {
	global $wpdb;

	$insert_values = '';
	$insert_parms = [];
	
	foreach ($requests_list as $recipe_request) {
		$cuisine = isset($recipe_request['cuisine']) ? $recipe_request['cuisine'] : '';
		$meal_type = isset($recipe_request['meal_type']) ? $recipe_request['meal_type'] : '';
		$classification = isset($recipe_request['classification']) ? $recipe_request['classification'] : 'N/A';
		$dietary = isset($recipe_request['dietary']) ? $recipe_request['dietary'] : '';
		$prep_time = isset($recipe_request['prep_time']) ? $recipe_request['prep_time'] : '';
		$equipment = isset($recipe_request['equipment']) ? $recipe_request['equipment'] : '';
		$recipe_count = isset($recipe_request['recipe_count']) ? $recipe_request['recipe_count'] : 0;
		$notes = isset($recipe_request['notes']) ? $recipe_request['notes'] : '';

		$insert_values .= '(%s, %s, %s, %s, %s, %s, %d, %s, %s),';
		$insert_parms[] = $cuisine;
		$insert_parms[] = $meal_type;
		$insert_parms[] = $classification;
		$insert_parms[] = $dietary;
		$insert_parms[] = $prep_time;
		$insert_parms[] = $equipment;
		$insert_parms[] = $recipe_count;
		$insert_parms[] = $notes;
		$insert_parms[] = $dt;
	}
	$insert_values = rtrim($insert_values, ',');

	$sql = "INSERT into $requests_table
						(cuisine, meal_type, classification, dietary, prep_time, equipment, recipe_count, notes, month_year)
					VALUES $insert_values";

	$rows_affected = $wpdb->query(
		$wpdb->prepare($sql, $insert_parms)
	);
	echo "<h3>Rows Inserted: $rows_affected</h3>";
	echo "<pre>";
	print_r($requests_list);
	echo "</pre>";
}
 