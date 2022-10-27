<?php

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

require RECIPE_SHEETS_PLUGIN_INCLUDES . 'recipe-worksheets-parms.php';

define('MAY_SUBMMISSION_SHEET_ID', '1BJ5fMNXsv6Le8AofW9ylpRkT_L6hzo8fE8egL7h2reQ');

$working_month = '2022-05-01';
$month_name = date("F", strtotime($working_month));

$sheets = initializeSheets();

$recipe_ingreds = get_recipe_ingreds($working_month);

$recipe_ingreds_submit_rows = get_recipe_ingreds_submit_rows($recipe_ingreds);

create_submission_ingreds_sheet($sheets, $recipe_ingreds_submit_rows);

echo "<pre>";
print_r($recipe_ingreds_submit_rows);
echo "</pre>";
die;

function get_recipe_ingreds($month) {
	global $wpdb;

	$sql = "
		SELECT ingred.name, ingred.pluralized, ingreds.*, rec.worksheet_id
		FROM tc_recipe_ingredients ingreds
			JOIN tc_ingredients ingred ON ingred.id = ingreds.ingred_id
			JOIN tc_recipes rec ON rec.id = ingreds.recipe_id
			JOIN tc_recipe_requests req ON req.id = rec.request_id
		WHERE req.month_year = %s
		ORDER BY ingreds.recipe_id, ingreds.ingred_cnt ASC 
	";

	$sql = $wpdb->prepare($sql, $month);
	$recipe_ingred_rows = $wpdb->get_results($sql, ARRAY_A);

	if (!$recipe_ingred_rows) {
		echo "<h2>Error retrieving recipe ingredient rows: ", $wpdb->error, "</h2>";
		die;
	}
	return $recipe_ingred_rows;

}

function get_recipe_ingreds_submit_rows($ingred_rows) {
	$ingred_submit_rows = array_map(function ($row) {
		$id_prefix = "RGWW";
		$recipe_id = $id_prefix . $row['worksheet_id'];
		$ingred_id = $recipe_id . '-' . $row['ingred_cnt'];
		$ingred_group = $row['plural'] == '1' ? ucfirst(strtolower($row['pluralized'])) : ucfirst(strtolower($row['name']));
		$ingred = strtolower($ingred_group);
		return array( 
			$recipe_id,
			$ingred_id,
			$ingred_group,
			$row['measure'],
			'',
			$row['unit'],
			$ingred,
			$row['notes'],
		);
	}, $ingred_rows);

	return $ingred_submit_rows;
}

function create_submission_ingreds_sheet($sheets, $recipe_ingreds_submit_rows) {
	try{
		$spreadsheetId = MAY_SUBMMISSION_SHEET_ID;
		$range = 'Ingredients!A2';
		$body = new Google_Service_Sheets_ValueRange(['values' => $recipe_ingreds_submit_rows]);

		$params = ['valueInputOption' => 'RAW'];
		$response = $sheets->spreadsheets_values->update($spreadsheetId, $range,
		$body, $params);
		return $response;
	}
	catch(Exception $e) {
		// TODO(developer) - handle error appropriately
		echo 'Message: ' .$e->getMessage();
	}
}