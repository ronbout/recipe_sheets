<?php

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

define('RECIPE_WORKSHEET_ID_COL', 0);
define('MONTH_COL', 1);
define('RECIPE_STATUS_COL', 2);
define('RECIPE_SOURCE_COL', 4);

define('MAY_WORKING_DOC_ID', '1F3DdkZv7Gq4lu-0MyM68_HBGRg8NK1-sVZbIKBnqP74');
define('JUNE_WORKING_DOC_ID', '1XNONqFyWBN5qX-1fSt8zMZ7TMVkEsgPDb_H1OL6fc5Q');

$working_month = '2022-03-01';

$sheets = initializeSheets();
$recipe_data = getData($sheets);

$test_month = strtolower(date("F", strtotime($working_month)));
$recipe_data = array_filter($recipe_data, function($row) use ($test_month) {
	return (isset($row[MONTH_COL]) && trim(strtolower($row[MONTH_COL]) === $test_month));
});

// echo '<h1>Count: ', count($recipe_data), "</h1>";
// echo '<pre>';
// print_r($recipe_data);
// echo '</pre>';
// die;

update_recipe_table($recipe_data);

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
		$spreadsheetId = MAY_WORKING_DOC_ID;
		$range = 'Support Data!B2:F';
		$response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
		$recipe_rows = $response->getValues();

		return $recipe_rows;
		}
		catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo 'Message: ' .$e->getMessage();
		}
}

function update_recipe_table($recipe_rows) {
	/**
	 * for now, we only need to know if a recipe has been entered
	 * for that purpose, just need to see if at least one ingredient
	 */
	$recipe_status_rows = array_filter($recipe_rows, function($recipe_row) {
		$recipe_status = isset($recipe_row[RECIPE_STATUS_COL]) ? strtolower($recipe_row[RECIPE_STATUS_COL]) : false;
		return $recipe_status;
	});

	$recipe_worksheet_ids = array_column($recipe_status_rows, RECIPE_WORKSHEET_ID_COL);
	$recipe_worksheet_ids = array_map(function($id) {
		return sanitize_text_field($id);
	}, $recipe_worksheet_ids);

	echo '<h1>Printed Recipe Count: ', count($recipe_status_rows), "</h1>";
	echo '<pre>';
	print_r($recipe_worksheet_ids);
	echo '</pre>';
	// die;

	update_recipe_status($recipe_worksheet_ids);

	// the same spreadsheet has the source info
	$recipe_source_rows = array_filter($recipe_rows, function($recipe_row) {
		$recipe_status = isset($recipe_row[RECIPE_SOURCE_COL]) ? $recipe_row[RECIPE_SOURCE_COL] : false;
		return $recipe_status;
	});

	$recipe_source_rows = array_map(function($row) {
		$tmp_row = $row;
		$tmp_row[RECIPE_SOURCE_COL] = esc_url_raw($tmp_row[RECIPE_SOURCE_COL] );
		return $tmp_row;
	}, $recipe_source_rows);

	
	echo '<h1>Source Count: ', count($recipe_source_rows), "</h1>";
	echo '<pre>';
	print_r($recipe_source_rows);
	echo '</pre>';
	// die;

	update_recipe_source($recipe_source_rows);
}

 
function update_recipe_status($recipe_worksheet_ids) {
	global $wpdb;

	$recipes_table = "tc_recipes";

	$placeholders = array_fill(0, count($recipe_worksheet_ids), '%s');
	$placeholders = implode(', ', $placeholders);
	
	$insert_values = rtrim($insert_values, ',');
	
	$sql = "
		UPDATE $recipes_table
		SET recipe_status = 'printed'
		WHERE worksheet_id in ($placeholders)";

	$rows_affected = $wpdb->get_results(
		$wpdb->prepare($sql, $recipe_worksheet_ids)
	);
	return $rows_affected;
}
 
function update_recipe_source($recipe_source_rows) {
	global $wpdb;

	$recipes_table = "tc_recipes";
	
	foreach($recipe_source_rows as $recipe_source_row) {
		$recipe_id = $recipe_source_row[RECIPE_WORKSHEET_ID_COL];
		$recipe_source = $recipe_source_row[RECIPE_SOURCE_COL];

		$sql = "
		UPDATE $recipes_table
		SET source = %s
		WHERE worksheet_id = %s";

		$rows_affected = $wpdb->get_results(
			$wpdb->prepare($sql, $recipe_source, $recipe_id)
		);

	}
	
}