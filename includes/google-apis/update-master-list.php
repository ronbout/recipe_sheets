<?php

defined('ABSPATH') or die('Direct script access disallowed.');
global $wpdb;
// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'recipe-worksheets-parms.php';

$sheets = initializeSheets();

define('MASTER_RECIPE_CNT_COL', 0);
define('MASTER_WORKING_MONTH_COL', 1);
define('MASTER_WORKSHEET_ID_COL', 2);
define('MASTER_VIRGIN_ID_COL', 2);
define('MASTER_RECIPE_TITLE_COL', 3);
define('MASTER_RECIPE_TYPE_COL', 5);
define('MASTER_WORKING_MONTH_COL', 6);

// load in June and July recipes from db

$sql = "
	(SELECT rec.*, req.month_year
	FROM tc_recipes rec
	JOIN tc_recipe_requests req ON req.id = rec.request_id
	WHERE rec.recipe_type = 'WO'
		AND (req.month_year = '2022-06-01' OR req.month_year = '2022-07-01'))
	UNION
	(SELECT rec.*, req.month_year
	FROM tc_recipes rec
	JOIN tc_recipes parent ON parent.worksheet_id = rec.parent_recipe_id
	JOIN tc_recipe_requests req ON req.id = parent.request_id
	WHERE rec.recipe_type = 'Catalog'
		AND (req.month_year = '2022-06-01' OR req.month_year = '2022-07-01'))
	ORDER BY month_year ASC, recipe_type DESC, worksheet_id ASC 
";

$recipe_rows = $wpdb->get_results($sql, ARRAY_A);

$recipe_cnt = 1606;
$master_rows = array_map(function ($row) use (&$recipe_cnt) {
	$month_name = date("F", strtotime($row['month_year']));
	$recipe_type = 'WO' === $row['recipe_type'] ? 'WO' : 'Virgin';
	$photo_date = $row['photo_date'] ? $row['photo_date'] : '';
	$camera_id = $row['camera_id'] ? $row['camera_id'] : '';
	$submission_batch = $row['submission_batch'] ? $row['submission_batch'] : '';
	return array(
		$recipe_cnt++,
		$month_name,
		$row['worksheet_id'],
		'',
		$row['recipe_title'],
		$recipe_type,
		$month_name,
		'',
		$photo_date,
		$camera_id,
		$submission_batch,

	);
}, $recipe_rows);

update_master_list_sheet($sheets, RECIPE_CATALOGUE_ID, 'Master List', 'A994', $master_rows);

echo '<h2>Recipes Loaded: ' . count($recipe_rows) . '</h2>';
echo '<pre>';
print_r($recipe_rows);
echo '</pre>';
die;

function update_master_list_sheet($sheets, $sheet_id, $sheet_name, $sheet_range, $sheet_rows) {
	try{
		$range = "$sheet_name!$sheet_range";
		$body = new Google_Service_Sheets_ValueRange(['values' => $sheet_rows]);

		$params = ['valueInputOption' => 'RAW'];
		$response = $sheets->spreadsheets_values->update($sheet_id, $range,
		$body, $params);
		return $response;
	}
	catch(Exception $e) {
		// TODO(developer) - handle error appropriately
		echo 'Message: ' .$e->getMessage();
	}
}