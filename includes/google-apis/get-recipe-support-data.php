<?php

defined('ABSPATH') or die('Direct script access disallowed.');

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

function import_recipe_support_data($working_month, $month_info) {

	$test_month = strtolower(date("F", strtotime($working_month)));
	echo "<h2>Support Data Month: ", $test_month, "</h2>";
	$sheets = initializeSheets();
	$support = get_support_data($sheets, $month_info['worksheet_doc_id']);

	$support = array_filter($support, function($row) {
		return is_int(intval($row[0])) && intval($row[0]);
	});

	// echo '<pre>';
	// print_r($support);
	// echo '</pre>';
	// die;

	update_recipe_support_cnt($support);

	echo '<h3>' . count($support) . ' Recipes Support Data updated</h3>';

}


function get_support_data($sheets, $sheet_id) {
	try{

		$spreadsheetId = $sheet_id;
		$range = 'Support Data!A2:F';
		$response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
		$recipe_rows = $response->getValues();

		return $recipe_rows;
		}
		catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo 'Message: ' .$e->getMessage();
		}
}

function update_recipe_support_cnt($support_rows) {
	global $wpdb;

	foreach($support_rows as $row) {
		$support_cnt = $row[0];
		$worksheet_id = $row[1];

		$sql = "
			SELECT id, worksheet_id, orig_child_id 
			FROM tc_recipes
			WHERE worksheet_id = %s OR orig_child_id = %s
		";

		$sql = $wpdb->prepare($sql, array($worksheet_id, $worksheet_id));
		$db_recipe = $wpdb->get_row($sql, ARRAY_A);

		if ($db_recipe['worksheet_id'] == $worksheet_id && $db_recipe['orig_child_id']) {
			continue;
		}
		$db_id = $db_recipe['id'];


		$db->result = $wpdb->update('tc_recipes', array( 'support_data_cnt' => $support_cnt), array( 'id' => $db_id),
										array('%d')); 
	}

	if (false === $db_result) {
		echo "<h2>Error updating worksheet id: $worksheet_id support cnt: $support_cnt </h2>";
		die;
	}

}