<?php
/**
 *  Functions for adding api customization
 *  related to the Recipe Sheets Distribution system
 * 
 * 	10/07/2022
 * 
 *  Ronald Boutilier
 * 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'create_recipe_sheets_api');
function create_recipe_sheets_api () {
  $api_array = array( 
    'methods' => 'GET',
    'callback' => 'recipe_sheets_dist_requests_api',
		'permission_callback' => '__return_true',
		// 'permission_callback' => function($request) {
		// 	return current_user_can('view_recipe_requests');
		// },
  );

  register_rest_route( 'recipes/v1', '/dist/requests/guru/', $api_array );
}

function recipe_sheets_dist_requests_api($request) {

  // $parameters = $request->get_query_params();
	// if (!isset($parameters['venue-id']) || !$parameters['venue-id'] )  {
	// 	$data = array(
	// 		'error' => "Venue Id is required",
	// 	);
	// 	return new WP_REST_Response($data, 200);
	// }

	// if (!get_current_user_id()  && (!isset($parameters['test']) || 'rontest' != $parameters['test']))  {
	// 	$data = array(
	// 		'error' => "Authentication error",
	// 	);
	// 	return new WP_REST_Response($data, 200);
	//  }

	// $venue_id = $parameters['venue-id'];
	// $venue_data = tapi_get_venue_data($venue_id)[0];

	$dist_data = recipe_sheets_get_dist_requests();

	// print_r($venue_data);
	// die;

	return new WP_REST_Response($dist_data, 200);
}

function recipe_sheets_get_dist_requests() {
	global $wpdb;

	$recipe_requests_file = "rg_recipe_guru_requests_working";
	$recipes_file = "rg_recipes";


	$sql = "
		SELECT req.id, req.cuisine, req.meal_type, req.classification, req.dietary, req.prep_time,
			req.equipment, req.recipe_count,  
			COUNT(DISTINCT rec.id) + COUNT(DISTINCT rec_enter.id) + COUNT(DISTINCT rec_print.id) AS recipes_accepted,
			COUNT(DISTINCT rec_enter.id) + COUNT(DISTINCT rec_print.id) AS recipes_entered, 
			COUNT(DISTINCT rec_print.id) AS recipes_printed,
			req.notes, MONTHNAME( req.month_year) AS month
		FROM $recipe_requests_file req
		LEFT JOIN $recipes_file rec ON rec.request_id = req.id
			AND rec.recipe_type = 'WO' AND rec.recipe_status = 'accepted'
		LEFT JOIN $recipes_file rec_enter ON rec_enter.request_id = req.id
			AND rec_enter.recipe_type = 'WO' AND rec_enter.recipe_status = 'entered'
		LEFT JOIN $recipes_file rec_print ON rec_print.request_id = req.id
			AND rec_print.recipe_type = 'WO' AND rec_print.recipe_status = 'printed'
		GROUP BY req.id
		ORDER BY req.id ASC 
	";

	$requests_rows = $wpdb->get_results($sql, ARRAY_A);

	return $requests_rows;
}
