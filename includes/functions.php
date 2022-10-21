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


// basic binary search routine 
function binary_search($needle, $haystack, $start=0, $end=0) {
	$end = !$end ? count($haystack) -1 : $end;

	if (is_string($needle)) {
		$needle = trim(strtolower($needle));
	}

	if ($end < $start) {
		return -1;
	}

	if ($end == $start) {
		if ($needle !== trim(strtolower($haystack[$end]))) {
			return -1;
		} else {
			return $end;
		}
	} 

	$comp_ndx = ceil (($end + $start) / 2);
	echo "<p>start: $start - end: $end - comp_ndx: $comp_ndx</p>";
	$comp_value = $haystack[$comp_ndx];	
	if (is_string($comp_value)) {
		$comp_value = trim(strtolower($comp_value));
	}

	echo "<p>needle: *$needle* - comp value: *$comp_value*</p>";
	if ($needle == $comp_value) {
		return $comp_ndx;
	} elseif ($needle < $comp_value) {
		return binary_search($needle, $haystack, $start, $comp_ndx - 1);
	} else {
		return binary_search($needle, $haystack, $comp_ndx + 1, $end);
	}
}