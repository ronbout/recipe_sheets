<?php

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'recipe-worksheets-parms.php';

define('MAY_SUBMISSION_SHEET_ID', '1BJ5fMNXsv6Le8AofW9ylpRkT_L6hzo8fE8egL7h2reQ');
define('JUNE_VIRGIN_SUBMISSION_SHEET_ID', '1L7WMOZ2_idMfqnb-O5xXFReUJoRVPiaqxI67Viu7Hzk');

define('SUBMISSION_SHEET_ID', JUNE_VIRGIN_SUBMISSION_SHEET_ID);

$working_month = '2022-06-01';
$month_name = date("F", strtotime($working_month));

$sheets = initializeSheets();
clear_submission_sheet($sheets, SUBMISSION_SHEET_ID);

$recipe_rows = get_recipes_to_submit($working_month, 'Virgin');

process_submission_recipe_sheet($sheets, $working_month, $recipe_rows);
process_submission_ingreds_sheet($sheets, $recipe_rows);
process_submission_images_sheet($sheets, $recipe_rows);
process_submission_instructs_sheet($sheets, $recipe_rows);
process_submission_tags_sheet($sheets, $recipe_rows);
process_submission_tips_sheet($sheets, $recipe_rows);

function get_recipes_to_submit($month, $recipe_type='WO') {
	global $wpdb;

	if ('WO' === $recipe_type) {
		$sql = "
		SELECT rec.* FROM tc_recipes rec
		JOIN tc_recipe_requests req ON req.id = rec.request_id
		WHERE rec.submission_batch IS NULL 
		AND rec.image_url IS NOT NULL 
		AND req.month_year = '%s'
		AND rec.recipe_type = 'WO'
		";
	} else {
		/*****
		 * 
		 * **** TEMP CHANGE TO PULL IN RECIPES W/O image_url DUE TO DATA ISSUE
		 * ****  MUST BE CHANGED BACK AFTER IMAGE ACCESS IS RESTORED!!!	
		 * 
		 */
		$sql = "
			SELECT rec.* FROM tc_recipes rec
			JOIN tc_recipe_requests req ON req.id = rec.request_id
			WHERE req.month_year = '%s'
			AND (rec.image_url IS NOT NULL OR rec.image_url IS NULL)
			AND req.tier = 'Virgin'
			ORDER BY rec.request_id ASC 
		";

	}

	$sql = $wpdb->prepare($sql, $month);
	$recipe_rows = $wpdb->get_results($sql, ARRAY_A);

	if ($wpdb->error) {
		echo "<h2>Error retrieving Recipes</h2>";
		echo "<p>$wpdb->error</p>";
		die;
	}

	if (!$recipe_rows || !count($recipe_rows)) {
		echo "<h2>No Recipes Found to Submit</h2>";
		die;
	}

	return $recipe_rows;
}

function process_submission_recipe_sheet($sheets, $working_month, $recipe_rows) {
	$recipe_submit_rows = get_recipe_submit_rows($recipe_rows);
	
	create_submission_sheet('Recipe', $sheets, $recipe_submit_rows);

	echo "<h2>Recipe Sheet Completed</h2>";

}

function get_recipe_submit_rows($recipe_rows) {
	$recipe_submit_rows = array_map(function ($row) {
		$recipe_id = get_submission_sheet_recipe_id($row);
		$prep = $row['prep_time'];
		$cook = $row['cook_time'];
		$total_time = intval($prep) + intval($cook);
		return array( 
			$recipe_id,
			$row['recipe_title'],
			$row['description'],
			'Recipe Guru',
			'',
			$prep,
			$cook,
			$total_time,
			'',
			'',
			'',
			'',
			'',
			$row['servings'],
			'',
			'People',
		);
	}, $recipe_rows);

	return $recipe_submit_rows;
}

function process_submission_ingreds_sheet($sheets, $recipe_rows) {
	$recipe_ids = array_column($recipe_rows, 'id');
	$recipe_ingreds = get_recipe_ingreds($recipe_ids);
	
	$recipe_ingreds_submit_rows = get_recipe_ingreds_submit_rows($recipe_ingreds);
	
	create_submission_sheet('Ingredients', $sheets, $recipe_ingreds_submit_rows);

	echo "<h2>Ingredients Sheet Completed</h2>";
	
}

function get_recipe_ingreds($recipe_ids) {
	global $wpdb;
	
	$placeholders = array_fill(0, count($recipe_ids), '%s');
	$placeholders = implode(', ', $placeholders);

	$sql = "
		SELECT ingred.name, ingred.pluralized, ingred.normalized, ingreds.*, rec.worksheet_id, rec.client_id, rec.recipe_type, 
			units.normalized AS unit_name, units.pluralized AS unit_plural_name
		FROM tc_recipe_ingredients ingreds
			JOIN tc_ingredients ingred ON ingred.id = ingreds.ingred_id
			JOIN tc_recipes rec ON rec.id = ingreds.recipe_id
			JOIN tc_recipe_requests req ON req.id = rec.request_id
			LEFT JOIN tc_measure_units units ON units.id = ingreds.unit
		WHERE rec.id in ($placeholders)
		ORDER BY ingreds.recipe_id, ingreds.ingred_cnt ASC 
	";

	$sql = $wpdb->prepare($sql, $recipe_ids);
	$recipe_ingred_rows = $wpdb->get_results($sql, ARRAY_A);

	if (!$recipe_ingred_rows) {
		echo "<h2>Error retrieving recipe ingredient rows: ", $wpdb->error, "</h2>";
		die;
	}
	return $recipe_ingred_rows;
}

function get_recipe_ingreds_submit_rows($ingred_rows) {
	$ingred_submit_rows = array_map(function ($row) {
		$recipe_id = get_submission_sheet_recipe_id($row);
		$ingred_id = $recipe_id . '-' . $row['ingred_cnt'];
		$ingred_group = $row['plural'] == '1' ? ucfirst(strtolower($row['pluralized'])) : ucfirst(strtolower($row['name']));
		$ingred = $row['plural'] == '1' ? ucfirst(strtolower($row['pluralized'])) : strtolower($row['normalized']);
		$unit_name = $row['unit_plural'] == '1' ? $row['unit_plural_name'] : $row['unit_name'];
		return array( 
			$recipe_id,
			$ingred_id,
			$ingred_group,
			$row['measure'],
			'',
			$unit_name ? ('n/a' == $unit_name) ? '' : $unit_name : '',
			$ingred,
			$row['notes'],
			$row['recipe_group']
		);
	}, $ingred_rows);

	return $ingred_submit_rows;
}

function process_submission_images_sheet($sheets, $recipe_rows) {
	$recipe_images_submit_rows = get_recipe_images_submit_rows($recipe_rows);
	
	create_submission_sheet('Images', $sheets, $recipe_images_submit_rows);

	echo "<h2>Images Sheet Completed</h2>";
	
}

function get_recipe_images_submit_rows($recipe_rows) {
	$recipe_submit_images_rows = array_map(function ($row) {
		$recipe_id = get_submission_sheet_recipe_id($row);
		return array( 
			$recipe_id,
			$row['image_url'] ? $row['image_url'] : 'N/A' ,
			$row['recipe_title'],
		);
	}, $recipe_rows);

	return $recipe_submit_images_rows;
}

function process_submission_instructs_sheet($sheets, $recipe_rows) {
	$recipe_ids = array_column($recipe_rows, 'id');
	$recipe_instructs = get_recipe_instructs($recipe_ids);
	
	$recipe_instructs_submit_rows = get_recipe_instructs_submit_rows($recipe_instructs);
	
	create_submission_sheet('Instructions', $sheets, $recipe_instructs_submit_rows);

	echo "<h2>Instructions Sheet Completed</h2>";
	
}

function get_recipe_instructs($recipe_ids) {
	global $wpdb;
	
	$placeholders = array_fill(0, count($recipe_ids), '%s');
	$placeholders = implode(', ', $placeholders);

	$sql = "
		SELECT instructs.*, rec.worksheet_id, rec.client_id, rec.recipe_type
		FROM tc_recipe_instructions instructs
			JOIN tc_recipes rec ON rec.id = instructs.recipe_id
		WHERE rec.id in ($placeholders)
		ORDER BY instructs.recipe_id, instructs.instruction_cnt ASC 
	";

	$sql = $wpdb->prepare($sql, $recipe_ids);
	$recipe_instruct_rows = $wpdb->get_results($sql, ARRAY_A);

	if (!$recipe_instruct_rows) {
		echo "<h2>Error retrieving recipe instruction rows: ", $wpdb->error, "</h2>";
		die;
	}
	return $recipe_instruct_rows;
}

function get_recipe_instructs_submit_rows($instruct_rows) {
	$instruct_submit_rows = array_map(function ($row) {
		$recipe_id = get_submission_sheet_recipe_id($row);
		$instruct_id = $recipe_id . '-' . $row['instruction_cnt'];
		return array( 
			$recipe_id,
			$instruct_id,
			$row['instruction'],
			'',
			'',
			$row['recipe_group'],
		);
	}, $instruct_rows);

	return $instruct_submit_rows;
}

function process_submission_tags_sheet($sheets, $recipe_rows) {
	$recipe_tags_submit_rows = get_recipe_tags_submit_rows($recipe_rows);
	
	create_submission_sheet('Tags', $sheets, $recipe_tags_submit_rows);

	echo "<h2>Tags Sheet Completed</h2>";
	
}

function get_recipe_tags_submit_rows($recipe_rows) {
	$recipe_submit_tags_rows = array();
	foreach ($recipe_rows as $row) {
		$recipe_id = get_submission_sheet_recipe_id($row);
		if (trim($row['meal_type'])) {
			$recipe_submit_tags_rows[] = array( 
				$recipe_id,
				'Meal Type',
				$row['meal_type']
			);
		}
		if (trim($row['cuisine'])) {
			$recipe_submit_tags_rows[] = array( 
				$recipe_id,
				'Cuisine',
				$row['cuisine']
			);
		}
		if (trim($row['diet'])) {
			$recipe_submit_tags_rows[] = array( 
				$recipe_id,
				'Diet',
				$row['diet']
			);
		}
	}
	
	return $recipe_submit_tags_rows;
}

function process_submission_tips_sheet($sheets, $recipe_rows) {
	$recipe_tips_submit_rows = get_recipe_tips_submit_rows($recipe_rows);

	create_submission_sheet('Tips', $sheets, $recipe_tips_submit_rows);

	echo "<h2>Tips Sheet Completed</h2>";
	
}

function get_recipe_tips_submit_rows($recipe_rows) {
	$recipe_submit_tips_rows = array();
	foreach ($recipe_rows as $row) {
		$recipe_id = get_submission_sheet_recipe_id($row);
		$db_recipe_id = $row['id'];
		$ingred_tips = get_ingred_tips($db_recipe_id);

		if (trim($row['recipe_tip'])) {
			$recipe_submit_tips_rows[] = array( 
				$recipe_id,
				'',
				'',
				'',
				$row['recipe_tip'],
				'',
				'',
				'Recipe tip',
			);
		}
		if (is_array($ingred_tips) && count($ingred_tips)) {
			foreach($ingred_tips as $tip_info) {
				$tip = $tip_info['ingred_tip'];
				if (trim($tip)) {
					$ingred_cnt = $recipe_id . '-' . $tip_info['ingred_cnt'];
					$recipe_submit_tips_rows[] = array( 
						$recipe_id,
						$ingred_cnt,
						'',
						'',
						$tip,
						'',
						'',
						'Ingredient tip',
					);
				}
			}
		}
	}
	
	return $recipe_submit_tips_rows;
}

function get_ingred_tips($id) {
	global $wpdb;

	$sql = "
		SELECT ingred_cnt, ingred_tip
		FROM tc_recipe_ingredients
		WHERE recipe_id = %d
			AND ingred_tip IS NOT NULL 
			AND ingred_tip <> ''
	";

	$sql = $wpdb->prepare($sql, $id);
	return $wpdb->get_results($sql, ARRAY_A);
}

function create_submission_sheet($sheet_name, $sheets, $submit_rows) {
	$spreadsheetId = SUBMISSION_SHEET_ID;
	try{
		$range = "$sheet_name!A2";
		$body = new Google_Service_Sheets_ValueRange(['values' => $submit_rows]);

		$params = ['valueInputOption' => 'RAW'];
		$response = $sheets->spreadsheets_values->update($spreadsheetId, $range,
		$body, $params);
		return $response;
	}
	catch(Exception $e) {
		// TODO(developer) - handle error appropriately
		echo 'Message: ' .$e->getMessage();
	}
}

function get_submission_sheet_recipe_id ($row) {
	if ('WO' === $row['recipe_type']) {
		$id_prefix = "RGWW";
		$recipe_id = $id_prefix . $row['worksheet_id'];
	} else {
		$recipe_id = $row['client_id'];
	}
	return $recipe_id;
}

function clear_submission_sheet($sheets, $sheet_id) {
	$sheet_names = array( 
		'Recipe',
		'Images',
		'Ingredients',
		'Instructions',
		'Tags',
		'Tips',
	);

	foreach($sheet_names as $sheet_name) {
		clear_report($sheets, $sheet_id, $sheet_name);
	}
}