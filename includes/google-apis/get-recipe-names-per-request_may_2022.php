<?php

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

define('CUISINE_COL', 0);
define('MEAL_TYPE_COL', 1);
define('CLASS_COL', 2);
define('DIETARY_COL', 3);
define('PREP_TIME_COL', 4);
define('EQUIPMENT_COL', 5);
define('RECIPE_COUNT_COL', 6);
define('NOTES_COL', 7);
define('BRIEF_RECIPE_TITLE_COL', 8);
define('BRIEF_VIRGIN_TITLE_COL', 9);
define('BRIEF_WORKSHEET_ID_COL', 10);
define('BRIEF_VIRGIN_ID_COL', 11);
define('BRIEF_RECIPE_TYPE_COL', 12);


// define('ROOT_ID_COL', 8);
// define('WORKSHEET_ID_COL', 9);
// define('VIRGIN_ID_COL', 10);
// define('RECIPE_TITLE_COL', 11);
// define('RECIPE_TYPE_COL', 12);
// define('MONTH_COL', 13);

define('MAY_WORKING_DOC_ID', '1F3DdkZv7Gq4lu-0MyM68_HBGRg8NK1-sVZbIKBnqP74');
define('MAY_BRIEF_ID', '189HnWpTZDUaYRdsBwc-62sYpLu5cKv7u8Ujq9XiJ19E');
define('JUNE_BRIEF_ID', '1XNONqFyWBN5qX-1fSt8zMZ7TMVkEsgPDb_H1OL6fc5Q');

$working_month = '2022-05-01';

define('WORKSHEET_ID_COL', 0);
define('VIRGIN_ID_COL', 1);
define('RECIPE_TITLE_COL', 2);
define('RECIPE_TYPE_COL', 3);
define('MONTH_COL', 4);


$sheets = initializeSheets();
$recipe_working_doc_data = getWorkingDocData($sheets);
$recipe_brief_data = getBriefData($sheets);

$filtered_working_doc_data = filter_working_doc_by_month($recipe_working_doc_data, $working_month);
// $filtered_working_names = array_column($filtered_working_doc_data, RECIPE_TITLE_COL);
$recipe_cnt = 0;
$brief_recipe_w_virgins = array_reduce($recipe_brief_data, function($list, $row) use (&$recipe_cnt, $filtered_working_doc_data) {
	$row[BRIEF_WORKSHEET_ID_COL] = $filtered_working_doc_data[$recipe_cnt][WORKSHEET_ID_COL];
	$row[BRIEF_VIRGIN_ID_COL] = $filtered_working_doc_data[$recipe_cnt][VIRGIN_ID_COL];
	$row[BRIEF_RECIPE_TYPE_COL] = "WO";
	$list[] = $row;
	$recipe_cnt++;
	if ($row[BRIEF_VIRGIN_TITLE_COL]) {
		$tmp_row = array();
		$tmp_row[BRIEF_RECIPE_TITLE_COL] = $row[BRIEF_VIRGIN_TITLE_COL];
		$tmp_row[BRIEF_WORKSHEET_ID_COL] = $filtered_working_doc_data[$recipe_cnt][WORKSHEET_ID_COL];
		$tmp_row[BRIEF_VIRGIN_ID_COL] = $filtered_working_doc_data[$recipe_cnt][VIRGIN_ID_COL];
		$tmp_row[BRIEF_RECIPE_TYPE_COL] = "Virgin";
		$list[] = $tmp_row;
		$recipe_cnt++;
	}
	return $list;
}, []);

// $comp_working_list = array_map(function($name) {
// 	return trim($name);
// }, $filtered_working_names);

// $comp_brief_list = array_map(function($name) {
// 	return trim($name);
// }, $brief_recipe_names);

// $diff_list1 = array_diff($comp_working_list, $comp_brief_list);
// $diff_list2 = array_diff($comp_brief_list, $comp_working_list);

// echo "<h2>Count: ", count($filtered_working_doc_data), "</h2>";
// echo "<pre>";
// print_r($filtered_working_doc_data);
// echo "</pre>";
// die;

// echo '<h1>Brief Row Count: ', count($brief_recipe_w_virgins), "</h1>";
// echo "<pre>";
// print_r($brief_recipe_w_virgins);
// echo "</pre>";
// die;

// echo '<h1>Working Doc Row Count: ', count($filtered_working_names), "</h1>";
// echo '<h1>Brief Row Count: ', count($brief_recipe_names), "</h1>";
// echo '<h1>diff 1 (working only): ', count($diff_list1), "</h1>";
// echo '<h1>diff 2 (brief only): ', count($diff_list2), "</h1>";
// echo '<h1>Intersect Row Count: ', count(array_intersect($comp_working_list, $comp_brief_list)), "</h1>";


load_recipe_request_table($brief_recipe_w_virgins, $working_month);

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


function getWorkingDocData($sheets) {
	try{
			$spreadsheetId = MAY_WORKING_DOC_ID;
			$range = 'Recipe List!K2:O';
			$response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
			$values = $response->getValues();
			return $values;
		}
		catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo 'Message: ' .$e->getMessage();
		}
}

function getBriefData($sheets) {
	try{
			$spreadsheetId = MAY_BRIEF_ID;
			$range = 'Sheet1!B5:K258';
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

	$recipe_requests_table = "tc_recipe_requests";
	$recipes_table = "tc_recipes";

	// easiest approach for now is just delete any for this month and rebuild

	$sql = "
		DELETE rec FROM $recipes_table rec 
		JOIN $recipe_requests_table req ON req.id = rec.request_id
		AND req.month_year = '$dt'
		";
	// 	AND req.month_year = %s
	// ";

	$sql = $wpdb->prepare($sql);

	$db_result = $wpdb->query($sql);

	$sql = "DELETE FROM $recipe_requests_table WHERE month_year = '$dt'";

	$db_result = $wpdb->query($sql);

	$current_vals = reset_current_vals();
	$requests_list = array();
	$recipes_list = array();

	foreach($data as $ndx => $recipe_row) {
		if (!count($recipe_row)) {
			continue;
		}
		if ($recipe_row[RECIPE_COUNT_COL]) {
			if ($current_vals['recipe_count'] && $current_vals['classification']) {
				// store current request vals and start a new request
				$requests_list[] = array(
					'request' => $current_vals,
					'recipes' => $recipes_list
				);
				$current_vals = reset_current_vals();
				$recipes_list = array();
			}
		}

		load_current_vals($current_vals, $recipe_row);
		$recipes_list[] = array(
			'root_id' => intval(sanitize_text_field($recipe_row[BRIEF_WORKSHEET_ID_COL])),
			'recipe_id' => sanitize_text_field($recipe_row[BRIEF_WORKSHEET_ID_COL]),
			'virgin_id' => sanitize_text_field($recipe_row[BRIEF_VIRGIN_ID_COL]),
			'recipe_title' => sanitize_text_field($recipe_row[BRIEF_RECIPE_TITLE_COL]),
			'recipe_type' => sanitize_text_field($recipe_row[BRIEF_RECIPE_TYPE_COL]),
		);
	
	}
	if ($current_vals['recipe_count'] && $current_vals['classification']) {
		// store current request vals and start a new request
		$requests_list[] = array(
			'request' => $current_vals,
			'recipes' => $recipes_list
		);
		$current_vals = reset_current_vals();
		$recipes_list = array();
	}
	update_request_and_recipes_tables($requests_list, $dt, $recipe_requests_table, $recipes_table);
}

function filter_working_doc_by_month($data, $dt) {
	$test_month = strtolower(date("F", strtotime($dt)));
	echo "<h2>Month: ", $test_month, "</h2>";
	$filtered_data = array_filter($data, function($row) use ($test_month) {
		return (isset($row[MONTH_COL]) && strtolower($row[MONTH_COL]) === $test_month);
	});

	$id_col = array_column($filtered_data, WORKSHEET_ID_COL);

	array_multisort($id_col, SORT_ASC, $filtered_data );

	return $filtered_data;
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
	if ($recipe_row[MEAL_TYPE_COL]) {
		$current_vals['meal_type'] = sanitize_text_field($recipe_row[MEAL_TYPE_COL]);
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

function update_request_and_recipes_tables($requests_list, $dt, $requests_table, $recipes_table) {
	global $wpdb;

	// echo '<pre>';
	// print_r($requests_list);
	// echo '</pre>';
	// die;

	foreach ($requests_list as $recipe_request_info) {
		$recipe_request = $recipe_request_info['request'];
		$cuisine = isset($recipe_request['cuisine']) ? $recipe_request['cuisine'] : '';
		$meal_type = isset($recipe_request['meal_type']) ? $recipe_request['meal_type'] : '';
		$classification = isset($recipe_request['classification']) ? $recipe_request['classification'] : 'N/A';
		$dietary = isset($recipe_request['dietary']) ? $recipe_request['dietary'] : '';
		$prep_time = isset($recipe_request['prep_time']) ? $recipe_request['prep_time'] : '';
		$equipment = isset($recipe_request['equipment']) ? $recipe_request['equipment'] : '';
		$recipe_count = isset($recipe_request['recipe_count']) ? $recipe_request['recipe_count'] : 0;
		$notes = isset($recipe_request['notes']) ? $recipe_request['notes'] : '';

		$insert_values = '(%d, %s, %s, %s, %s, %s, %s, %d, %s, %s)';
		$insert_parms = array();
		$insert_parms[] = 1;
		$insert_parms[] = $cuisine;
		$insert_parms[] = $meal_type;
		$insert_parms[] = $classification;
		$insert_parms[] = $dietary;
		$insert_parms[] = $prep_time;
		$insert_parms[] = $equipment;
		$insert_parms[] = $recipe_count;
		$insert_parms[] = $notes;
		$insert_parms[] = $dt;

		$sql = "INSERT into $requests_table
					(distributor_id, cuisine, meal_type, classification, dietary, prep_time, equipment, recipe_count, notes, month_year)
				VALUES $insert_values";

		$rows_affected = $wpdb->query(
			$wpdb->prepare($sql, $insert_parms)
		);

		$request_id = $wpdb->insert_id;

		$recipe_insert_result = insert_recipe_rows($recipe_request_info['recipes'], $request_id, $recipes_table);
	}
}
 
function insert_recipe_rows($recipes, $request_id, $recipes_table) {
	global $wpdb;
	$insert_values = '';
	$insert_parms = [];
	
	foreach ($recipes as $recipe_info) {
		$recipe_id = $recipe_info['recipe_id'];
		$root_id = $recipe_info['root_id'];
		$virgin_id = $recipe_info['virgin_id'];
		$recipe_title = $recipe_info['recipe_title'];
		$recipe_type = $recipe_info['recipe_type'];

		$insert_values .= '(%s, %s, %d, %s, %d, %s),';
		$insert_parms[] = $recipe_id;
		$insert_parms[] = $root_id;
		$insert_parms[] = $virgin_id;
		$insert_parms[] = $recipe_title;
		$insert_parms[] = $request_id;
		$insert_parms[] = $recipe_type;

	}
	
	$insert_values = rtrim($insert_values, ',');
	
	$sql = "INSERT into $recipes_table
		(worksheet_id, root_id, virgin_id, recipe_title, request_id, recipe_type)
	VALUES $insert_values";

	$rows_affected = $wpdb->query(
		$wpdb->prepare($sql, $insert_parms)
	);
	return true;
}