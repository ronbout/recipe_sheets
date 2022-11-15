<?php 

defined('ABSPATH') or die('Direct script access disallowed.');

function compare_google_sheets($sheets_info) {
	require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'recipe-worksheets-parms.php';

	$report_id = GOOGLE_SHEET_COMPARE_REPORT_ID;
	$sheets = initializeSheets();

	print_r($sheets_info);
	$id1 = $sheets_info['id1'];
	$id2 = $sheets_info['id2'];
	$name1 = $sheets_info['name1'];
	$name2 = $sheets_info['name2'];

	$sheet1 = get_comparison_sheet($sheets, $id1, $name1);
	$sheet2 = get_comparison_sheet($sheets, $id2, $name2);

	echo '<pre>';
	print_r($sheet1);
	print_r($sheet2);
	echo '</pre>';



}


function get_comparison_sheet($sheets, $sheet_id, $sheet_name) {
	try{
		$range = "$sheet_name!A2:AZ";
		$response = $sheets->spreadsheets_values->get($sheet_id, $range);
		$recipe_rows = $response->getValues();

		return $recipe_rows;
		}
		catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo 'Message: ' .$e->getMessage();
		}
}
