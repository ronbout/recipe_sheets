<?php 

defined('ABSPATH') or die('Direct script access disallowed.');

function tc_ajax_import_recipe_sheets_data() {
	if (!check_ajax_referer('recipe_sheets-ajax-nonce','security', false)) {
		echo '<h2>Security error loading data.  <br>Please Refresh the page and try again.</h2>';
		wp_die();
	}

	$routine = isset($_POST['routine']) ? $_POST['routine'] : 0;
	require_once RECIPE_SHEETS_PLUGIN_INCLUDES.'ajax/import-recipe-sheets-data.php';
	import_recipe_sheets_data_all($routine);

	wp_die();
}

function tc_ajax_compare_google_sheets() {
	if (!check_ajax_referer('recipe_sheets-ajax-nonce','security', false)) {
		echo '<h2>Security error loading data.  <br>Please Refresh the page and try again.</h2>';
		wp_die();
	}

	if (!isset($_POST['sheet_info']) || !$_POST['sheet_info']) {
		echo '<h2>Missing sheet comparison info</h2>';
		wp_die();
	}

	$sheets_info = $_POST['sheet_info'];

	require_once RECIPE_SHEETS_PLUGIN_INCLUDES.'ajax/compare-google-sheets.php';
	compare_google_sheets($sheets_info);

	wp_die();
}

if ( is_admin() ) {
	add_action('wp_ajax_import_recipe_sheets','tc_ajax_import_recipe_sheets_data');
	add_action('wp_ajax_compare_google_sheets','tc_ajax_compare_google_sheets');
}