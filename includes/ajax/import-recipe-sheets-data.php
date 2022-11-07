<?php 

defined('ABSPATH') or die('Direct script access disallowed.');

function import_recipe_sheets_data_all($routine) {
	require RECIPE_SHEETS_PLUGIN_INCLUDES . 'recipe-worksheets-parms.php';

	switch($routine) {
		case 0:
			echo "<h2>Import Data</h2>";
			require RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/load-ingredients-table.php';
			import_ingredients_data_from_sheets();
			import_all_recipe_requests_and_names($recipe_worksheets_parms);
			import_all_recipe_entry_status($recipe_worksheets_parms);
			// import_all_recipe_printed_status($recipe_worksheets_parms);
			break;
		case 1:
			echo "<h2>Import Data</h2>";
			require RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/load-ingredients-table.php';
			import_ingredients_data_from_sheets();
			break;
		case 2:
			echo "<h2>Import Data</h2>";
			import_all_recipe_requests_and_names($recipe_worksheets_parms);
			break;
		case 3:
			echo "<h2>Import Data</h2>";
			import_all_recipe_entry_status($recipe_worksheets_parms);
			break;
		case 4: 
			import_all_recipe_image_data($recipe_worksheets_parms, 'WO');
			break;
		case 5: 
			import_all_recipe_image_data($recipe_worksheets_parms, 'Catalog');
			break;
		case 6:
			clear_recipe_tables();
			break;
		case 7:
			clear_ingredients_table();	
			break;
	}


	// clear_tables();
	

	// TODO: load photographed status for each month 

	// TODO: load exported status for each month 
}

function import_all_recipe_requests_and_names($recipe_worksheets_parms) {
	require RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/get-recipe-names-per-request.php';
	// Load requests and recipes for each month

	foreach($recipe_worksheets_parms as $month => $month_data) {
		// if ('2022-06-01' == $month) {
		// 	import_recipe_requests_and_names($month, $month_data);
		// }
		import_recipe_requests_and_names($month, $month_data);
	}
}

function import_all_recipe_entry_status($recipe_worksheets_parms) {
	require RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/get-recipe-entry-status.php';
	// load recipe entry status for each month
	foreach($recipe_worksheets_parms as $month => $month_data) {
		// if ('2022-06-01' == $month) {
			// import_recipe_entry_status($month, $month_data);
		// }
		import_recipe_entry_status($month, $month_data);
	}
}
	
/*
function import_all_recipe_printed_status($recipe_worksheets_parms) {
	require RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/get-recipe-printed-status.php';
	// load recipe printed status for each month
	foreach($recipe_worksheets_parms as $month => $month_data) {
		// if ('2022-04-01' == $month) {
		// 	import_recipe_printed_status($month, $month_data);
		// }
		import_recipe_printed_status($month, $month_data);
	}
}
*/

function import_all_recipe_image_data($recipe_worksheets_parms, $recipe_type) {
	require RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/get-recipe-working-images.php';
	// load recipe printed status for each month
	if ('WO' === $recipe_type) {
		foreach($recipe_worksheets_parms as $month => $month_data) {
			if ('2022-05-01' == $month) {
				import_recipe_image_data($month, $month_data, $recipe_type);
			}
		}
	} else {
		import_recipe_image_data($month, $month_data, $recipe_type);
	}
}


function clear_recipe_tables() {
	global $wpdb;


	$sql = "DELETE FROM tc_recipe_requests WHERE 1";
	$db_results = $wpdb->query($sql);
	echo "<h2>$db_results recipe requests deleted</h2>";

	$sql = "DELETE FROM tc_recipe_ingredients WHERE 1";
	$db_results = $wpdb->query($sql);
	echo "<h2>$db_results recipe ingredients deleted</h2>";

	$sql = "DELETE FROM tc_recipe_instructions WHERE 1";
	$db_results = $wpdb->query($sql);
	echo "<h2>$db_results recipe instructions deleted</h2>";

	$sql = "DELETE FROM tc_recipe_names WHERE 1";
	$db_results = $wpdb->query($sql);
	echo "<h2>$db_results recipe names deleted</h2>";
	
	$sql = "DELETE FROM tc_recipes WHERE 1";
	$db_results = $wpdb->query($sql);
	echo "<h2>$db_results recipes deleted</h2>";
}

function clear_ingredients_table() {
	global $wpdb;

	$sql = "DELETE FROM tc_ingredients WHERE 1";
	$db_results = $wpdb->query($sql);
	echo "<h2>$db_results ingredients deleted</h2>";

}