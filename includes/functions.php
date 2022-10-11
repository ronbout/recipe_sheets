<?php
/**
  * Add a "browse_resumes" capability to admins and venues
  * Can be used to test for other employer capabilities
  */

function recipe_sheets_add_capability() {
	$role = get_role( 'administrator' );
	// Add a new capability.
	$role->add_cap( 'view_recipe_requests', true );

	// $role = get_role( 'recipe_distributor' );
	// // Add a new capability.
	// $role->add_cap( 'view_recipe_requests', true );
}
 
// Add simple_role capabilities, priority must be after the initial role definition.
add_action( 'init', 'recipe_sheets_add_capability', 11 );
