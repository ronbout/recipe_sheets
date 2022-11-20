<?php

defined('ABSPATH') or die('Direct script access disallowed.');

define('MIN_INSTRUCTS_TEMP', 100);
define('MAX_INSTRUCTS_TEMP',300);

function convert_recipe_title($title) {
	$new_title = ucwords(trim($title));
	$new_title = str_replace(' And ', ' & ', $new_title);
	$new_title = str_replace(' With ', ' with ', $new_title);
	$new_title = str_replace(' In ', ' in ', $new_title);
	
	return $new_title;
}

function convert_recipe_desc($desc) {
	if ('' === trim($desc)) {
		return '';
	}
	$new_desc = ucfirst(trim($desc));
	if (str_ends_test($new_desc, ',')) {
		$new_desc = substr_replace($new_desc, '.', -1, 1);
	}
	$new_desc = str_ends_test($new_desc, '.') || str_ends_test($new_desc, '!') || str_ends_test($new_desc, '?') ? $new_desc : $new_desc . '.';
	return $new_desc;
}

function convert_recipe_times($time_desc) {
	$new_time_desc = trim(str_ireplace('minutes', '', $time_desc));
	$new_time_desc = intval($new_time_desc);
	return $new_time_desc;
}

function convert_recipe_ingred_notes($notes) {
	return strtolower(trim($notes));	
}

function convert_recipe_instructions($instructs) {
	$instructs = convert_recipe_desc($instructs);

	$status = 0; // no degree conversion

	// check for oven degrees to add fan-forced
	while ((false !== strpos(strtolower($instructs), "oven") || false !== strpos(strtolower($instructs), "bake")) && false !== strpos(strtolower($instructs), "degree") && $status >= 0 ) {
		$instruct_data = convert_degree($instructs);
		$instructs = $instruct_data['new_instructs'];
		$status = $instruct_data['status'];
		// add to various lists based on data
	}
	
	return array( 
		'status' => $status,
		'instructs' => $instructs,
	);
}

function convert_degree($new_instructs) {
	$status = 1;
	if (false !== strpos($new_instructs, 'fan-forced')) {
		// echo "already fan forced: <p>$new_instructs</p>";
		$status = -1; // already included fan forced text
	}
	
	// get string prior to "degree"
	$temp = rtrim(stristr($new_instructs, 'degree', true));
	// echo "<p>$temp</p>";
	
	$temp_loc = strrpos($temp, ' ') + 1;
	
	// echo "<p>temp loc: $temp_loc</p>";
	
	$temp = intval(trim(strrchr($temp, " ")));
	// echo "<p>$temp</p>";
	
	$temp = round_to_5($temp);
	
	if ($temp < MIN_INSTRUCTS_TEMP || $temp > MAX_INSTRUCTS_TEMP) {
		// echo "trouble temp: <p>$new_instructs</p>";
		$status = -2; // outside range
	}
	
	// echo "<p>So far: $temp</p>";
	
	$temp_display = get_fan_force_display($temp);
	
	// echo "<h2>$temp_display</h2>";

	$search_terms = array( 
		'degrees Celsius',
		'degree Celsius',
		'degrees',
		'degree',
	);

	$success_flag = true;
	foreach ($search_terms as $search_term) {
		$end_pos = str_end_pos($search_term, $new_instructs);
		if (false !== $end_pos) {
			$replace_len = $end_pos - $temp_loc + 1;
			$new_instructs = substr_replace($new_instructs, $temp_display, $temp_loc, $replace_len);
			$success_flag = true;
			break;
		}
	}

	if (!$success_flag) {
		$status = -3;
	}

	// echo "<p>success? $success_flag </p>";
	
	// echo $new_instructs;
	return array( 
		'status' => $status,
		'new_instructs' => $new_instructs,
	);

}

function get_fan_force_display($temp) {
	$fan_temp = $temp - 20;
	return "{$temp}°C/{$fan_temp}°C fan-forced";
}

function round_to_5($nbr) {
	return round($nbr / 5) * 5;
}

function str_end_pos($needle, $haystack) {
	$needle_len = strlen($needle);
	$fnd_pos = stripos($haystack, $needle);
	if (false === $fnd_pos) {
		return false;
	}
	return $fnd_pos + $needle_len - 1;
}

function convert_recipe_group($group) {
	if (!trim($group)) {
		return '';
	}
	return "For the " . strtolower(trim($group));
}