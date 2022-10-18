<?php

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

define('MONTH_COL', 0);
define('RECIPE_TYPE_COL', 1);
define('VIRGIN_ID_COL', 2);
define('RECIPE_WORKSHEET_ID_COL', 3);
define('RECIPE_FIELD_COL', 4);
define('RECIPE_FIELD_CNT_COL', 5);
define('RECIPE_FIELD_STEP_COL', 6);
define('RECIPE_FIELD_DESC_COL', 7);
define('RECIPE_MEASURE_COL', 8);
define('RECIPE_UNIT_COL', 9);
define('RECIPE_NOTES_COL', 10);

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
		$range = 'Recipe Entry!B2:L';
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
	$recipe_rows = array_filter($recipe_rows, function($recipe_row) {
		$fieldname = strtolower($recipe_row[RECIPE_FIELD_COL]);
		$fielddesc = strtolower($recipe_row[RECIPE_FIELD_DESC_COL]);
		return ('ingredient' === $fieldname && 1 == $recipe_row[RECIPE_FIELD_STEP_COL] && $fielddesc) ;
	});

	$recipe_worksheet_ids = array_column($recipe_rows, RECIPE_WORKSHEET_ID_COL);
	$recipe_worksheet_ids = array_map(function($id) {
		return sanitize_text_field($id);
	}, $recipe_worksheet_ids);

	echo '<h1>Count: ', count($recipe_worksheet_ids), "</h1>";
	echo '<pre>';
	print_r($recipe_worksheet_ids);
	echo '</pre>';
	// die;

	return update_recipe_rows($recipe_worksheet_ids);
}

 
function update_recipe_rows($recipe_worksheet_ids) {
	global $wpdb;

	$recipes_table = "tc_recipes";

	$placeholders = array_fill(0, count($recipe_worksheet_ids), '%s');
	$placeholders = implode(', ', $placeholders);
	
	$insert_values = rtrim($insert_values, ',');
	
	$sql = "
		UPDATE $recipes_table
		SET recipe_status = 'entered'
		WHERE worksheet_id in ($placeholders)";

	$rows_affected = $wpdb->get_results(
		$wpdb->prepare($sql, $recipe_worksheet_ids)
	);
	return $rows_affected;
}