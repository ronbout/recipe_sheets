<?php

/*
    Plugin Name: Recipe Sheets
    Description: React and google sheets api to access recipe guru sheets
		Version: 1.0.0
		Date: 9/28/2022
    Author: Ron Boutilier
    Text Domain: recipe-sheets
 */

defined('ABSPATH') or die('Direct script access disallowed.');

define('RECIPE_SHEETS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RECIPE_SHEETS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RECIPE_SHEETS_PLUGIN_INCLUDES', RECIPE_SHEETS_PLUGIN_PATH.'includes/');
define('RECIPE_SHEETS_PLUGIN_INCLUDES_URL', RECIPE_SHEETS_PLUGIN_URL.'includes/');
define('RECIPE_SHEETS_PLUGIN_BUILD', RECIPE_SHEETS_PLUGIN_PATH.'build/');
define('RECIPE_SHEETS_PLUGIN_BUILD_URL', RECIPE_SHEETS_PLUGIN_URL.'build');

require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'functions.php';
// require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'react-setup.php';
require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'recipe-apis/get-apis.php';
require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'ajax/ajax-functions.php';

// we use GROUP_CONCAT in a number of instances.  To ensure that the
// size of that field is always large enough, change it at the session level.
global $wpdb;

$wpdb->query("SET SESSION group_concat_max_len = 30000;");

require_once RECIPE_SHEETS_PLUGIN_INCLUDES.'/activation-deactivation.php';

register_activation_hook( __FILE__, 'recipe_sheets_activation' );
register_deactivation_hook( __FILE__, 'recipe_sheets_deactivation' );

// set up page templates
function recipes_add_recipe_template ($templates) {
	$templates['recipe-load-sheets-data.php'] = 'Load Recipe Sheets Data';
	$templates['recipe-build-worksheet.php'] = 'Build Worksheet Data';
	$templates['recipe-build-submission.php'] = 'Build Submission Sheet';
	$templates['recipe-update-master-list.php'] = 'Update Recipe Master List';
	$templates['recipe-compare-sheets-entry.php'] = 'Compare Google Sheets';
	return $templates;
	}
add_filter ('theme_page_templates', 'recipes_add_recipe_template');

function recipes_redirect_page_template ($template) {
	if (is_page_template('recipe-load-sheets-data.php')) {
		$template = plugin_dir_path( __FILE__ ).'includes/page-templates/recipe-load-sheets-data.php';
	}
	if (is_page_template('recipe-build-worksheet.php')) {
		$template = plugin_dir_path( __FILE__ ).'includes/page-templates/recipe-build-worksheet.php';
	}
	if (is_page_template('recipe-build-submission.php')) {
		$template = plugin_dir_path( __FILE__ ).'includes/page-templates/recipe-build-submission.php';
	}
	if (is_page_template('recipe-update-master-list.php')) {
		$template = plugin_dir_path( __FILE__ ).'includes/page-templates/recipe-update-master-list.php';
	}
	if (is_page_template('recipe-compare-sheets-entry.php')) {
		$template = plugin_dir_path( __FILE__ ).'includes/page-templates/recipe-compare-sheets-entry.php';
	}
	return $template;
}
add_filter ('page_template', 'recipes_redirect_page_template');

function recipes_react_enqueues() {

	// wp_enqueue_script('taste-react-portal-indexjs', RECIPE_SHEETS_PLUGIN_BUILD_URL . 'index.js', array('wp-element'), '1.0', true);
  // wp_enqueue_style('taste-react-portal-css', RECIPE_SHEETS_PLUGIN_BUILD_URL . 'index.css');
  // wp_localize_script( 'taste-react-portal-indexjs', 'tasteVenuePortal', array(
  //   'apiUrl' => home_url('/wp-json/thetaste/v1/'),
  //   'nonce' => wp_create_nonce('wp_rest'),
  // ) );


}

// add_action('wp_enqueue_scripts', 'recipes_react_enqueues');


// function recipe_sheets_hide_admin_bar($bool) {
//   if ( is_page_template( 'venue-react-portal.php' ) ) :
//     return false;
//   else :
//     return $bool;
//   endif;
// }
// add_filter('show_admin_bar', 'recipe_sheets_hide_admin_bar');

add_action( 'rest_api_init', function()
{
    header( "Access-Control-Allow-Origin: *" );
} );
