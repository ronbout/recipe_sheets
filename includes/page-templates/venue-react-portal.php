<?php
/*
Template Name: Venue React Portal
*/

/**
 * 	The entry landing page for all venues *** REACT VERSION
 *  Date:  8/20/2022
 * 	Author: Ron Boutilier
 */
defined('ABSPATH') or die('Direct script access disallowed.');

if ( !is_user_logged_in()) {
	require_once TASTE_PLUGIN_PATH.'page-templates/thetaste-venue-login.php';
	die();
} else {
	$user_info = get_user_venue_info();
	$user = $user_info['user'];
	$role = $user_info['role'];
	$admin = ('ADMINISTRATOR' === strtoupper($role));

	if ('VENUE' !== strtoupper($role) && !$admin) {
		echo "<h2>Role: $role </h2>";
		die('You must be logged in as a Venue to access this page.');
	}
	if (!$admin) {
		$venue_name = $user_info['venue_name'];
		$venue_type = $user_info['venue_type'];
		$use_new_campaign = $user_info['use_new_campaign'];
		$venue_voucher_page = $user_info['venue_voucher_page'];
		$type_desc = $venue_type;
	}

}

require_once TASTE_REACT_PORTAL_PLUGIN_INCLUDES.'page-templates/partials/venue-head.php';
require_once  TASTE_REACT_PORTAL_PLUGIN_INCLUDES.'page-templates/partials/venue-navbar.php';

$venue_id = '';
if ($admin) {
	$nav_links = array(
		array(
			'title' => 'Log Out',
			'url' => wp_logout_url(get_site_url()),
			'active' => false
		),
	);
	// check if the Venue ID is in GET from form 
	if (isset($_GET['venue-id'])) {
		$venue_id = $_GET['venue-id'];
		// add the link to return to venue selection
		$venue_select_link = array(
			'title' => 'Venue Selection',
			'url' => get_page_link(),
			'active' => false
		);
		array_unshift($nav_links, $venue_select_link);
		// get venue name and other info
		$user_info = get_user_venue_info($venue_id);
		$venue_name = $user_info['venue_name'];
		$venue_type = $user_info['venue_type'];
		$use_new_campaign = $user_info['use_new_campaign'];
		$venue_voucher_page = $user_info['venue_voucher_page'];
		$type_desc = $venue_type;
	} 
} else {
	//$nav_links = venue_navbar_standard_links($user_info['use_new_campaign'], $user_info['venue_voucher_page']);
	$venue_id = $user->ID;
}

$venue_table = $wpdb->prefix."taste_venue";
$navbar_get = $admin ? "venue-id=$venue_id" : "";
$nav_links = venue_navbar_standard_links($user_info['use_new_campaign'], $user_info['venue_voucher_page'], $admin, $navbar_get);
?>

<body>
  <?php
if (!$venue_id) {
	// display form to select Venue as user is admin w/o a venue selected
	display_venue_select(true, 0, true, get_page_link());
	echo '<script type="text/javascript" src= "' . TASTE_PLUGIN_INCLUDES_URL . '/js/thetaste-venue-select.js"></script>';
	die();
}


?>


  <?php venue_navbar($nav_links); ?>
  <div class="container" id="main-wrapper">
    <div class="row pb-4">
      <div class="col-sm-12 col-xl-12 dashboard_grid_cols d-flex align-items-center flex-column">
        <h2 class="dashboard_heading mt-5 font-weight-bold text-left">Welcome to Your New Dashboard,
          <?php echo $venue_name; ?> </h2>
      </div>
    </div>
		<div id="react-portal-container"></div>
  </div>
	<?php wp_footer() ?>
</body>

</html>