<?php

defined('ABSPATH') or die('Direct script access disallowed.');

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

function import_recipe_printed_status($working_month, $month_info) {

	$test_month = strtolower(date("F", strtotime($working_month)));
	echo "<h2>Printed Status Month: ", $test_month, "</h2>";
	$sheets = initializeSheets();
	$recipe_data = getPrintedData($sheets, $month_info['worksheet_doc_id']);

	$test_month = strtolower(date("F", strtotime($working_month)));
	$recipe_data = array_filter($recipe_data, function($row) use ($test_month) {
		return (isset($row[SUPPORT_MONTH_COL]) && trim(strtolower($row[SUPPORT_MONTH_COL]) === $test_month));
	});

	// echo '<h1>Count: ', count($recipe_data), "</h1>";
	// echo '<pre>';
	// print_r($recipe_data);
	// echo '</pre>';
	// die;

	update_recipe_table_printed($recipe_data);
}

function getPrintedData($sheets, $sheet_id) {
	try{
		$spreadsheetId = $sheet_id;
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

function update_recipe_table_printed($recipe_rows) {
	/**
	 * for now, we only need to know if a recipe has been entered
	 * for that purpose, just need to see if at least one ingredient
	 */
	$recipe_status_rows = array_filter($recipe_rows, function($recipe_row) {
		$recipe_status = isset($recipe_row[SUPPORT_RECIPE_STATUS_COL]) ? strtolower($recipe_row[SUPPORT_RECIPE_STATUS_COL]) : false;
		return $recipe_status;
	});

	$recipe_worksheet_ids = array_column($recipe_status_rows, SUPPORT_RECIPE_WORKSHEET_ID_COL);
	$recipe_worksheet_ids = array_map(function($id) {
		return sanitize_text_field($id);
	}, $recipe_worksheet_ids);

	echo '<h1>Printed Recipe Count: ', count($recipe_status_rows), "</h1>";
	// echo '<pre>';
	// print_r($recipe_worksheet_ids);
	// echo '</pre>';
	// die;

	update_recipe_status_printed($recipe_worksheet_ids);

	// the same spreadsheet has the source info
	$recipe_source_rows = array_filter($recipe_rows, function($recipe_row) {
		$recipe_status = isset($recipe_row[SUPPORT_RECIPE_SOURCE_COL]) ? $recipe_row[SUPPORT_RECIPE_SOURCE_COL] : false;
		return $recipe_status;
	});

	$recipe_source_rows = array_map(function($row) {
		$tmp_row = $row;
		$tmp_row[SUPPORT_RECIPE_SOURCE_COL] = esc_url_raw($tmp_row[SUPPORT_RECIPE_SOURCE_COL] );
		return $tmp_row;
	}, $recipe_source_rows);

	
	echo '<h1>Source Count: ', count($recipe_source_rows), "</h1>";
	// echo '<pre>';
	// print_r($recipe_source_rows);
	// echo '</pre>';
	// die;

	update_recipe_source($recipe_source_rows);
}

 
function update_recipe_status_printed($recipe_worksheet_ids) {
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
		$recipe_id = $recipe_source_row[SUPPORT_RECIPE_WORKSHEET_ID_COL];
		$recipe_source = $recipe_source_row[SUPPORT_RECIPE_SOURCE_COL];

		$sql = "
		UPDATE $recipes_table
		SET source = %s
		WHERE worksheet_id = %s";

		$rows_affected = $wpdb->get_results(
			$wpdb->prepare($sql, $recipe_source, $recipe_id)
		);

	}
	
}