<?php

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

function import_recipe_entry_status($working_month, $month_info) {

	$sheets = initializeSheets();
	$recipe_data = getEntryData($sheets, $month_info['worksheet_doc_id']);

	$test_month = strtolower(date("F", strtotime($working_month)));
	echo "<h2>Recipes Entered Status Month: ", $test_month, "</h2>";
	$recipe_data = array_filter($recipe_data, function($row) use ($test_month) {
		return (isset($row[ENTRY_MONTH_COL]) && trim(strtolower($row[ENTRY_MONTH_COL]) === $test_month));
	});

	// echo '<h1>Count: ', count($recipe_data), "</h1>";
	// echo '<pre>';
	// print_r($recipe_data);
	// echo '</pre>';
	// die;

	update_recipe_table_entry($recipe_data);
}

function getEntryData($sheets, $sheet_id) {
	try{

		$spreadsheetId = $sheet_id;
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

function update_recipe_table_entry($recipe_rows) {
	/**
	 * for now, we only need to know if a recipe has been entered
	 * for that purpose, just need to see if at least one ingredient
	 */
	$recipe_rows = array_filter($recipe_rows, function($recipe_row) {
		$fieldname = strtolower($recipe_row[RECIPE_FIELD_COL]);
		$fielddesc = strtolower($recipe_row[RECIPE_FIELD_DESC_COL]);
		return ('ingredient' === $fieldname && 1 == $recipe_row[RECIPE_FIELD_STEP_COL] && $fielddesc) ;
	});

	$recipe_worksheet_ids = array_column($recipe_rows, ENTRY_RECIPE_WORKSHEET_ID_COL);
	$recipe_worksheet_ids = array_map(function($id) {
		return sanitize_text_field($id);
	}, $recipe_worksheet_ids);

	echo '<h1>Recipe Entered Count: ', count($recipe_worksheet_ids), "</h1>";
	// echo '<pre>';
	// print_r($recipe_worksheet_ids);
	// echo '</pre>';
	// die;

	return update_recipe_rows_entry($recipe_worksheet_ids);
}

 
function update_recipe_rows_entry($recipe_worksheet_ids) {
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