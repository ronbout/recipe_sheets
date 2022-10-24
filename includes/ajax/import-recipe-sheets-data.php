<?php 

defined('ABSPATH') or die('Direct script access disallowed.');

function import_recipe_sheets_data_all() {
	require RECIPE_SHEETS_PLUGIN_INCLUDES . 'recipe-worksheets-parms.php';
	require RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/get-recipe-names-per-request.php';
	require RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/get-recipe-entry-status.php';
	require RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/get-recipe-printed-status.php';
	echo "<h2>Import Data</h2>";

	// clear_tables();
	
	// Load requests and recipes for each month
	// TODO:  eventually, deal with db id matching WO worksheet id and
	//				virgin/new recipes being assigned with auto inc.  for now,
	// 				the db recipe id is unrelated to the worksheet id
	foreach($recipe_worksheets_parms as $month => $month_data) {
		// if ('2022-06-01' == $month) {
		// 	import_recipe_requests_and_names($month, $month_data);
		// // }
		import_recipe_requests_and_names($month, $month_data);
	}

	// load recipe entry status for each month
	foreach($recipe_worksheets_parms as $month => $month_data) {
		if ('2022-06-01' == $month) {
			// import_recipe_entry_status($month, $month_data);
		}
		import_recipe_entry_status($month, $month_data);
	}
	
	// load recipe printed status for each month
	foreach($recipe_worksheets_parms as $month => $month_data) {
		// if ('2022-04-01' == $month) {
		// 	import_recipe_printed_status($month, $month_data);
		// }
		import_recipe_printed_status($month, $month_data);
	}

	// TODO: load photographed status for each month 

	// TODO: load exported status for each month 


}

function clear_tables() {
	global $wpdb;

	$sql = "DELETE FROM tc_recipes WHERE 1";

	$wpdb->query($sql);

	$sql = "DELETE FROM tc_recipe_requests WHERE 1";

	$wpdb->query($sql);

}