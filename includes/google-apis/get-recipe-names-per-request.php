<?php

defined('ABSPATH') or die('Direct script access disallowed.');

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

function import_recipe_requests_and_names($working_month, $month_info) {

	$test_month = strtolower(date("F", strtotime($working_month)));
	echo "<h2>Request / Recipes Month: ", $test_month, "</h2>";
	$sheets = initializeSheets();
	$recipe_working_doc_data = getWorkingDocData($sheets, $month_info['worksheet_doc_id']);
	$recipe_brief_data = getBriefData($sheets, $month_info['brief_doc_id'], $month_info['brief_sheet_name']);
	
	$filtered_working_doc_data = filter_working_doc_by_month($recipe_working_doc_data, $working_month);
	$filtered_working_names = array_column($filtered_working_doc_data, RECIPE_TITLE_COL);
	$recipe_cnt = 0;
	$virgin_tier_row = array();
	$virgin_tier_recipes = array();
	$brief_recipe_w_virgins = array_reduce($recipe_brief_data, function($list, $row) use (&$recipe_cnt, $filtered_working_doc_data, &$virgin_tier_row, &$virgin_tier_recipes) {
		if (isset($row[BRIEF_TIER_COL]) && 'virgin' === strtolower($row[BRIEF_TIER_COL])) {
			$virgin_tier_row = $row;
		}
		if (count($virgin_tier_row) ) {
			if (isset($row[BRIEF_RECIPE_TITLE_COL]) && $row[BRIEF_RECIPE_TITLE_COL]) {
				$virgin_tier_recipes[] = $row[BRIEF_RECIPE_TITLE_COL];
			}
			return $list;
		}
		$row[BRIEF_WORKSHEET_ID_COL] = $filtered_working_doc_data[$recipe_cnt][WORKSHEET_ID_COL];
		$row[BRIEF_VIRGIN_ID_COL] = $row[BRIEF_WORKSHEET_ID_COL];
		$row[BRIEF_RECIPE_TYPE_COL] = "WO";
		$tmp_row[BRIEF_PARENT_RECIPE_ID_COL] = null;
		$list[] = $row;
		$recipe_cnt++;
		if ($row[BRIEF_VIRGIN_TITLE_COL]) {
			$tmp_row = array();
			$tmp_row[BRIEF_RECIPE_TITLE_COL] = $row[BRIEF_VIRGIN_TITLE_COL];
			$tmp_row[BRIEF_WORKSHEET_ID_COL] = $filtered_working_doc_data[$recipe_cnt][WORKSHEET_ID_COL];
			$tmp_row[BRIEF_VIRGIN_ID_COL] = $filtered_working_doc_data[$recipe_cnt][VIRGIN_ID_COL];
			$tmp_row[BRIEF_RECIPE_TYPE_COL] = "Catalog";
			$tmp_row[BRIEF_PARENT_RECIPE_ID_COL] = $row[BRIEF_WORKSHEET_ID_COL];
			$list[] = $tmp_row;
			$recipe_cnt++;
		}
		if ($row[BRIEF_VIRGIN_TITLE2_COL]) {
			$tmp_row = array();
			$tmp_row[BRIEF_RECIPE_TITLE_COL] = $row[BRIEF_VIRGIN_TITLE2_COL];
			$tmp_row[BRIEF_WORKSHEET_ID_COL] = $filtered_working_doc_data[$recipe_cnt][WORKSHEET_ID_COL];
			$tmp_row[BRIEF_VIRGIN_ID_COL] = $filtered_working_doc_data[$recipe_cnt][VIRGIN_ID_COL];
			$tmp_row[BRIEF_RECIPE_TYPE_COL] = "Catalog";
			$tmp_row[BRIEF_PARENT_RECIPE_ID_COL] = $row[BRIEF_WORKSHEET_ID_COL];
			$list[] = $tmp_row;
			$recipe_cnt++;
		}
		return $list;
	}, []);

	// echo '<pre>';
	// print_r($virgin_tier_row);
	// print_r($virgin_tier_recipes);
	// echo '</pre>';
	// die;
	
	$comp_working_list = array_map(function($name) {
		return strtolower(trim($name));
	}, $filtered_working_names);
	
	$brief_recipe_names = array_column($brief_recipe_w_virgins, BRIEF_RECIPE_TITLE_COL);
	
	$comp_brief_list = array_map(function($name) {
		return strtolower(trim($name));
	}, $brief_recipe_names);
	
	$diff_list1 = array_diff($comp_working_list, $comp_brief_list);
	$diff_list2 = array_diff($comp_brief_list, $comp_working_list);
	
	echo "<h3>Working Doc Row Count: ", count($filtered_working_names), "</h3>";
	echo "<h3>Brief Row Count: ", count($brief_recipe_names), "</h3>";
	echo "<h3>Working Doc Row Count: ", count($comp_working_list), "</h3>";
	echo "<h3>Brief Row Count: ", count($comp_brief_list), "</h3>";
	echo "<br />";

	if (count($diff_list1) || count($diff_list2) || count($comp_working_list) !== count($comp_brief_list)) {
		echo "<h1>Month: $working_month</h1>"; 
		echo '<h1>diff 1 (working only): ', count($diff_list1), "</h1>";
	
		echo "<pre>";
		print_r($diff_list1);
		echo "</pre>";
		
		echo '<h1>diff 2 (brief only): ', count($diff_list2), "</h1>";
		
		echo "<pre>";
		print_r($diff_list2);
		echo "</pre>";
		echo "<pre>";
		print_r($comp_working_list);
		echo "</pre>";
		echo "<pre>";
		print_r($comp_brief_list);
		echo "</pre>";
		
		echo "<pre>";
		print_r($filtered_working_doc_data);
		echo "</pre>";
		echo "<pre>";
		print_r($brief_recipe_w_virgins);
		echo "</pre>";
		
		echo '<h1>Intersect Row Count: ', count(array_intersect($comp_working_list, $comp_brief_list)), "</h1>";
		die;
	}

	
	load_recipe_request_table($brief_recipe_w_virgins, $working_month);

	if (count($virgin_tier_row)) {
		$virgin_recipe_cnt = load_recipe_request_tier_data($virgin_tier_row, $virgin_tier_recipes, $working_month);
		if ($virgin_recipe_cnt) {
			echo '<h3>Virgin Tier Recipes Listed: ' . count($virgin_tier_recipes) . '</h3>';
			echo "<h3>Virgin Tier Rows Updated: $virgin_recipe_cnt</h3>";
		}
	}
}

function getWorkingDocData($sheets, $sheet_id) {
	try{
			$spreadsheetId = $sheet_id;
			$range = 'Recipe List!K2:O';
			$response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
			$values = $response->getValues();
			return $values;
		}
		catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo 'Message: ' .$e->getMessage();
		}
}

function getBriefData($sheets, $sheet_id, $sheet_name) {
	try{
			$spreadsheetId = $sheet_id;
			$range = "$sheet_name!B4:O";
			$response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
			$values = $response->getValues();
			return $values;
		}
		catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo 'Message: ' .$e->getMessage();
		}
}

function load_recipe_request_table($data, $dt) {
	global $wpdb;

	// before beginning, reset recipe autoinc
	$sql = "ALTER TABLE tc_recipes AUTO_INCREMENT = 1";
	$wpdb->query($sql);

	$recipe_requests_table = "tc_recipe_requests";
	$recipes_table = "tc_recipes";

	$current_vals = reset_current_vals();
	$requests_list = array();
	$recipes_list = array();

	foreach($data as $ndx => $recipe_row) {
		if (!count($recipe_row)) {
			continue;
		}
		if ($recipe_row[RECIPE_COUNT_COL]) {
			if ($current_vals['recipe_count'] && $current_vals['classification']) {
				// store current request vals and start a new request
				$requests_list[] = array(
					'request' => $current_vals,
					'recipes' => $recipes_list
				);
				$current_vals = reset_current_vals();
				$recipes_list = array();
			}
		}

		load_current_vals($current_vals, $recipe_row);
		if (isset($recipe_row[BRIEF_RECIPE_TITLE_COL]) && $recipe_row[BRIEF_RECIPE_TITLE_COL]) {
			$recipe_type = sanitize_text_field($recipe_row[BRIEF_RECIPE_TYPE_COL]);
			$recipe_id = sanitize_text_field($recipe_row[BRIEF_WORKSHEET_ID_COL]);
			if ("WO" === $recipe_type) {
				$client_id = "RGWW" . $recipe_id;
			} else {
				$client_id = recipe_row[BRIEF_VIRGIN_ID_COL] ? sanitize_text_field($recipe_row[BRIEF_VIRGIN_ID_COL]) : null;
			}
			$recipes_list[] = array(
				'root_id' => intval(sanitize_text_field($recipe_row[BRIEF_WORKSHEET_ID_COL])),
				'recipe_id' => $recipe_id,
				'client_id' => $client_id,
				'recipe_title' => sanitize_text_field($recipe_row[BRIEF_RECIPE_TITLE_COL]),
				'recipe_type' => $recipe_type,
				'parent_recipe_id' => sanitize_text_field($recipe_row[BRIEF_PARENT_RECIPE_ID_COL]),
			);}
	
	}
	if ($current_vals['recipe_count'] && $current_vals['classification']) {
		// store current request vals and start a new request
		$requests_list[] = array(
			'request' => $current_vals,
			'recipes' => $recipes_list
		);
		$current_vals = reset_current_vals();
		$recipes_list = array();
	}
	update_request_and_recipes_tables($requests_list, $dt, $recipe_requests_table, $recipes_table);
}

function filter_working_doc_by_month($data, $dt) {
	$test_month = strtolower(date("F", strtotime($dt)));
	$filtered_data = array_filter($data, function($row) use ($test_month) {
		return (isset($row[MONTH_COL]) && strtolower($row[MONTH_COL]) === $test_month);
	});

	$id_col = array_column($filtered_data, WORKSHEET_ID_COL);

	array_multisort($id_col, SORT_ASC, $filtered_data );

	return $filtered_data;
} 

function reset_current_vals() {
		$current_vals = array( 
			'cuisine' => null,
			'meal_type' => null,
			'classification' => null,
			'dietary' => null,
			'prep_time' => null,
			'equipment' => null,
			'recipe_count' => null, 
			'notes' => null,
	);
	return $current_vals;
}

function load_current_vals(&$current_vals, $recipe_row) {
	if ($recipe_row[CUISINE_COL]) {
		$current_vals['cuisine'] = sanitize_text_field($recipe_row[CUISINE_COL]);
	}
	if ($recipe_row[MEAL_TYPE_COL]) {
		$current_vals['meal_type'] = sanitize_text_field($recipe_row[MEAL_TYPE_COL]);
	}
	if ($recipe_row[CLASS_COL]) {
		$current_vals['classification'] = sanitize_text_field($recipe_row[CLASS_COL]);
	}
	if ($recipe_row[DIETARY_COL]) {
		$current_vals['dietary'] = sanitize_text_field($recipe_row[DIETARY_COL]);
	}
	if ($recipe_row[PREP_TIME_COL]) {
		$current_vals['prep_time'] = sanitize_text_field($recipe_row[PREP_TIME_COL]);
	}
	if ($recipe_row[EQUIPMENT_COL]) {
		$current_vals['equipment'] = sanitize_text_field($recipe_row[EQUIPMENT_COL]);
	}
	if ($recipe_row[RECIPE_COUNT_COL]) {
		$current_vals['recipe_count'] = sanitize_text_field($recipe_row[RECIPE_COUNT_COL]);
	}
	if ($recipe_row[NOTES_COL]) {
		$current_vals['notes'] = sanitize_text_field($recipe_row[NOTES_COL]);
	}
}

function update_request_and_recipes_tables($requests_list, $dt, $requests_table, $recipes_table) {
	global $wpdb;

	// echo '<pre>';
	// print_r($requests_list);
	// echo '</pre>';
	// die;

	foreach ($requests_list as $recipe_request_info) {
		$recipe_request = $recipe_request_info['request'];
		$cuisine = isset($recipe_request['cuisine']) ? $recipe_request['cuisine'] : '';
		$meal_type = isset($recipe_request['meal_type']) ? $recipe_request['meal_type'] : '';
		$classification = isset($recipe_request['classification']) ? $recipe_request['classification'] : 'N/A';
		$dietary = isset($recipe_request['dietary']) ? $recipe_request['dietary'] : '';
		$prep_time = isset($recipe_request['prep_time']) ? $recipe_request['prep_time'] : '';
		$equipment = isset($recipe_request['equipment']) ? $recipe_request['equipment'] : '';
		$recipe_count = isset($recipe_request['recipe_count']) ? $recipe_request['recipe_count'] : 0;
		$notes = isset($recipe_request['notes']) ? $recipe_request['notes'] : '';

		$insert_values = '(%d, %s, %s, %s, %s, %s, %s, %d, %s, %s)';
		$insert_parms = array();
		$insert_parms[] = 1;
		$insert_parms[] = $cuisine;
		$insert_parms[] = $meal_type;
		$insert_parms[] = $classification;
		$insert_parms[] = $dietary;
		$insert_parms[] = $prep_time;
		$insert_parms[] = $equipment;
		$insert_parms[] = $recipe_count;
		$insert_parms[] = $notes;
		$insert_parms[] = $dt;

		$sql = "INSERT into $requests_table
					(distributor_id, cuisine, meal_type, classification, dietary, prep_time, equipment, recipe_count, notes, month_year)
				VALUES $insert_values";

		$rows_affected = $wpdb->query(
			$wpdb->prepare($sql, $insert_parms)
		);

		$request_id = $wpdb->insert_id;

		$recipe_insert_result = insert_recipe_rows($recipe_request_info['recipes'], $request_id, $recipes_table);
	}
}
 
function insert_recipe_rows($recipes, $request_id, $recipes_table) {
	global $wpdb;
	$insert_values = '';
	$insert_parms = [];
	
	foreach ($recipes as $recipe_info) {
		$recipe_id = $recipe_info['recipe_id'];
		$root_id = $recipe_info['root_id'];
		$client_id = $recipe_info['client_id'];
		$recipe_title = $recipe_info['recipe_title'];
		$recipe_type = $recipe_info['recipe_type'];
		$parent_recipe_id = $recipe_info['parent_recipe_id'];

		$db_request_id = "WO" === $recipe_type ? $request_id : null;
		$db_client_id = $client_id ? $client_id : 'makenull';

		$insert_values .= '(%s, %s, %s, %s, %d, %s, %d),';
		$insert_parms[] = $recipe_id;
		$insert_parms[] = $root_id;
		$insert_parms[] = $db_client_id;
		$insert_parms[] = $recipe_title;
		$insert_parms[] = $db_request_id;
		$insert_parms[] = $recipe_type;
		$insert_parms[] = $parent_recipe_id;
	}
	
	$insert_values = rtrim($insert_values, ',');
	
	$sql = "INSERT into $recipes_table
		(worksheet_id, root_id, client_id, recipe_title, request_id, recipe_type, parent_recipe_id)
	VALUES $insert_values";

	$prepared_sql = $wpdb->prepare($sql, $insert_parms);
	$prepared_sql = str_replace("makenull", null, $prepared_sql);

	// if ("Catalog" === $recipe_type) {
	// 	echo "<pre>";
	// 	var_dump($prepared_sql);
	// 	print_r($insert_parms);
	// 	echo "</pre>";
	// 	die;
	// }

	$rows_affected = $wpdb->query($prepared_sql);
	return true;
}

function load_recipe_request_tier_data($virgin_tier_row, $virgin_tier_recipes, $working_month) {
	global $wpdb;

	$current_vals = reset_current_vals();
	load_current_vals($current_vals, $virgin_tier_row);
	$current_vals['tier'] = "Virgin";
	$current_vals['distributor_id'] = 1;
	$current_vals['month_year'] = $working_month;
	if (!$current_vals['classification']) {
		$current_vals['classification'] = 'Virgin Recipes from the Catalogue';
	}
	$formats = array(	'%s','%s','%s','%s','%s','%s','%d','%s','%s','%d','%s');

	$wpdb->insert('tc_recipe_requests', $current_vals, $formats);
	$request_id = $wpdb->insert_id;

	$placeholders = array_fill(0, count($virgin_tier_recipes), '%s');
	$placeholders = implode(', ', $placeholders);
	

	$sql = "
		UPDATE tc_recipes
		SET request_id = $request_id
		WHERE worksheet_id in ($placeholders)
	";

	$sql = $wpdb->prepare($sql, $virgin_tier_recipes);
	$db_result = $wpdb->query($sql);

	return $db_result;

}