<?php

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

define('NAME_COL', 0);
define('NORMALIZED_COL', 1);
define('PLURALIZED_COL', 2);
define('DEPLURALIZE_COL', 3);
define('DERIVATIVE_COL', 4);
define('MINCE_COL', 5);

$sheets = initializeSheets();
$ingred_data = get_ingredient_data($sheets);

echo '<h1>Ingredients Found Count: ', count($ingred_data), "</h1>";
// echo '<pre>';
// print_r($ingred_data);
// echo '</pre>';
// die;

$insert_cnt = load_ingredients_table($ingred_data);

echo "<h1>$insert_cnt Ingredients Inserted</h1>";

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


function get_ingredient_data($sheets) {
	try{

			$spreadsheetId = '1XNONqFyWBN5qX-1fSt8zMZ7TMVkEsgPDb_H1OL6fc5Q';
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

	$insert_sql = "
		INSERT INTO tc_ingredients 
			(name, normalized, pluralized, depluralize, derivative, mince)
			VALUES
	";

	$insert_cnt = 0;
	foreach($ingred_data as $ingred_info) {
		$name = $ingred_info[NAME_COL];
		$normalized = isset($ingred_info[NORMALIZED_COL]) ? $ingred_info[NORMALIZED_COL] : '';
		$pluralized = isset($ingred_info[PLURALIZED_COL]) ? $ingred_info[PLURALIZED_COL] : '';
		$depluralize = isset($ingred_info[DEPLURALIZE_COL]) ? $ingred_info[DEPLURALIZE_COL] : '';
		$derivative = isset($ingred_info[DERIVATIVE_COL]) ? $ingred_info[DERIVATIVE_COL] : '';
		$mince = isset($ingred_info[MINCE_COL]) ? $ingred_info[MINCE_COL] : '';

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
