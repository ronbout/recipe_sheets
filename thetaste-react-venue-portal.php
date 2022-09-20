<?php

/*
    Plugin Name: TheTaste React Venue Portal Plugin
    Plugin URI: http://thetaste.ie
    Description: React front end Venue portal 
		Version: 1.0.0
		Date: 9/20/2022
    Author: Ron Boutilier
    Text Domain: taste-plugin
 */

defined('ABSPATH') or die('Direct script access disallowed.');

define('TASTE_REACT_PORTAL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TASTE_REACT_PORTAL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TASTE_REACT_PORTAL_PLUGIN_INCLUDES', TASTE_REACT_PORTAL_PLUGIN_PATH.'includes/');
define('TASTE_REACT_PORTAL_PLUGIN_INCLUDES_URL', TASTE_REACT_PORTAL_PLUGIN_URL.'includes/');
define('TASTE_REACT_PORTAL_PLUGIN_BUILD', TASTE_REACT_PORTAL_PLUGIN_PATH.'build/');
define('TASTE_REACT_PORTAL_PLUGIN_BUILD_URL', TASTE_REACT_PORTAL_PLUGIN_URL.'build/');

// we use GROUP_CONCAT in a number of instances.  To ensure that the
// size of that field is always large enough, change it at the session level.
global $wpdb;

$wpdb->query("SET SESSION group_concat_max_len = 30000;");

$uploads_info = wp_get_upload_dir();
$uploads_base_url = $uploads_info['baseurl'];
!defined('TASTE_VENUE_UPLOADS_BASE_URL') && define('TASTE_VENUE_UPLOADS_BASE_URL', $uploads_base_url);

// set up page templates
function taste_add_react_portal_template ($templates) {
	$templates['venue-react-portal.php'] = 'Venue React Portal';
	return $templates;
	}
add_filter ('theme_page_templates', 'taste_add_react_portal_template');

function taste_react_redirect_page_template ($template) {
	if (is_page_template('venue-react-portal.php')) {
		$template = plugin_dir_path( __FILE__ ).'includes/page-templates/venue-react-portal.php';
	}
	return $template;
}
add_filter ('page_template', 'taste_react_redirect_page_template');

function taste_react_enqueues() {

	wp_enqueue_script('taste-react-portal-indexjs', TASTE_REACT_PORTAL_PLUGIN_BUILD_URL . 'index.js', array('wp-element'), '1.0', true);
  wp_enqueue_style('taste-venue-css', TASTE_PLUGIN_INCLUDES_URL . '/style/css/thetaste-venue.css');
  wp_enqueue_style('taste-react-portal-css', TASTE_REACT_PORTAL_PLUGIN_BUILD_URL . 'index.css');
  wp_localize_script( 'taste-react-portal-indexjs', 'tasteVenuePortal', array(
    'apiUrl' => home_url('/wp-json/thetaste/v1/'),
    'nonce' => wp_create_nonce('wp_rest'),
  ) );
}

add_action('wp_enqueue_scripts', 'taste_react_enqueues');


function taste_react_portal_hide_admin_bar($bool) {
  if ( is_page_template( 'venue-react-portal.php' ) ) :
    return false;
  else :
    return $bool;
  endif;
}
add_filter('show_admin_bar', 'taste_react_portal_hide_admin_bar');

add_action( 'rest_api_init', function()
{
    header( "Access-Control-Allow-Origin: *" );
} );
