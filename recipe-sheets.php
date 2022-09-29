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

require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'react-setup.php';

// we use GROUP_CONCAT in a number of instances.  To ensure that the
// size of that field is always large enough, change it at the session level.
global $wpdb;

$wpdb->query("SET SESSION group_concat_max_len = 30000;");

// set up page templates
function recipes_add_react_portal_template ($templates) {
	$templates['venue-react-portal.php'] = 'Venue React Portal';
	return $templates;
	}
add_filter ('theme_page_templates', 'recipes_add_react_portal_template');

function recipes_react_redirect_page_template ($template) {
	if (is_page_template('venue-react-portal.php')) {
		$template = plugin_dir_path( __FILE__ ).'includes/page-templates/venue-react-portal.php';
	}
	return $template;
}
add_filter ('page_template', 'recipes_react_redirect_page_template');

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
