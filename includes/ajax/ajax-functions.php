<?php 

defined('ABSPATH') or die('Direct script access disallowed.');

function tc_ajax_import_recipe_sheets_data() {
	if (!check_ajax_referer('recipe_sheets-ajax-nonce','security', false)) {
		echo '<h2>Security error loading data.  <br>Please Refresh the page and try again.</h2>';
		wp_die();
	}

	require_once RECIPE_SHEETS_PLUGIN_INCLUDES.'ajax/import-recipe-sheets-data.php';
	import_recipe_sheets_data_all();

	wp_die();
}

if ( is_admin() ) {
	add_action('wp_ajax_import_recipe_sheets','tc_ajax_import_recipe_sheets_data');
}