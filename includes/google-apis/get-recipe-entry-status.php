<?php

defined('ABSPATH') or die('Direct script access disallowed.');

// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

function import_recipe_entry_status($working_month, $month_info) {
	global $wpdb; 
	
	$report_id = RECIPE_ENTRY_IMPORT_REPORT_ID;
	$sheets = initializeSheets();

	$sql = "
		SELECT ingred.* 
		FROM tc_ingredients ingred
		ORDER BY ingred.name ASC
	";

	$ingreds = $wpdb->get_results($sql, ARRAY_A);

	array_walk($ingreds, function(&$row) {
		$row['name'] = strtolower($row['name']);
	});
	
	$sql = "
		SELECT units.* 
		FROM tc_measure_units units
		ORDER BY units.name ASC
	";

	$units = $wpdb->get_results($sql, ARRAY_A);

	$sheets = initializeSheets();
	$recipe_data = getEntryData($sheets, $month_info['worksheet_doc_id']);

	$test_month = strtolower(date("F", strtotime($working_month)));
	echo "<h2>Recipes Entered Status Month: ", $test_month, "</h2>";
	$recipe_data = array_filter($recipe_data, function($row) use ($test_month) {
		return (isset($row[ENTRY_MONTH_COL]) && trim(strtolower($row[ENTRY_MONTH_COL]) === $test_month));
	});

	/*
	**  this was for testing that all ingredients exist in the db
	**  will still write code to insert ingredient if not found, but want
	** 	to run test first to see if it is a mistake that can be corrected
	** 	w/o adding new ingredient (misspelling, for instance....or missing plural entry)


	$recipe_ingreds_rows = array_filter($recipe_data, function($recipe_row) {
		$fieldname = strtolower($recipe_row[RECIPE_FIELD_COL]);
		$fielddesc = strtolower($recipe_row[RECIPE_FIELD_DESC_COL]);
		return ('ingredient' === $fieldname && $fielddesc) ;
	});

	$recipe_ingred_names = array_column($recipe_ingreds_rows, RECIPE_FIELD_DESC_COL);


	foreach($recipe_ingred_names as $ingred_name) {
		$fnd = binary_search($ingred_name, $ingred_db_names);
		if (-1 === $fnd) {
				$fnd = array_search($fielddesc, $ingred_db_plurals);
			if (-1 === $fnd) {
				echo "<h2>*** $ingred_name NOT FOUND ***</h2>";
				die;
			} else {
				// echo "<p>Ingred Id Plural: $fnd  Ingred: $ingred_name </p>";
			}
		} else {
			// echo "<p>Ingred Id: $fnd  Ingred: $ingred_name</p>";
		}
	}
	// die;

	echo '<h1>Recipe Ingredients Count: ', count($recipe_ingred_names), "</h1>";
	echo '<pre>';
	print_r($recipe_ingred_names);
	echo '</pre>';
	die;
*/

	$update_cnt = update_recipe_table_entry($recipe_data, $ingreds, $units, $sheets, $report_id, $test_month);
	echo "<h1>Recipe Entries Updated: $update_cnt</h1>";
}

function getEntryData($sheets, $sheet_id) {
	try{

		$spreadsheetId = $sheet_id;
		$range = 'Recipe Entry!B2:AC';
		$response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
		$recipe_rows = $response->getValues();

		return $recipe_rows;
		}
		catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo 'Message: ' .$e->getMessage();
		}
}

function update_recipe_table_entry($recipe_rows, $ingreds, $units, $sheets, $report_id, $month) {
	
	// echo "<pre>";
	// print_r($recipe_rows);
	// echo "</pre>";
	// die;

	$ingred_db_names = array_column($ingreds, 'name');
	$ingred_db_plurals = array_column($ingreds, 'pluralized');
	$ingred_db_plurals = array_filter($ingred_db_plurals, function($row) {
		return $row;
	});
	array_walk($ingred_db_names, function(&$name) {
		$name = strtolower($name);
	});
	array_walk($ingred_db_plurals, function(&$name) {
		$name = strtolower($name);
	});

	$unit_db_names = array_column($units, 'name');
	$unit_db_plurals = array_column($units, 'pluralized');
	$unit_db_plurals = array_filter($unit_db_plurals, function($row) {
		return $row;
	});
	array_walk($unit_db_names, function(&$name) {
		$name = strtolower($name);
	});
	array_walk($unit_db_plurals, function(&$name) {
		$name = strtolower($name);
	});

// 	echo "<pre>";
// 	print_r($ingred_db_names);
// 	echo "</pre>";
// die;

	$prev_worksheet_id = -1;
	$update_cnt = 0;
	$not_found_ingreds = array();
	$not_found_units = array();
	$degree_converts = array();
	$degree_converts_reviews = array();
	foreach($recipe_rows as $row) {
		$worksheet_id = $row[ENTRY_RECIPE_WORKSHEET_ID_COL];
		if ($worksheet_id !== $prev_worksheet_id) {
			if (-1 !== $prev_worksheet_id) {
				update_recipe_table_info($recipe_info, $prev_worksheet_id);
				$update_cnt++;
			}
			$recipe_info = new_recipe_info();
			$prev_worksheet_id = $worksheet_id;
		}
		$fieldname = strtolower($row[RECIPE_FIELD_COL]);
		$fielddesc = $row[RECIPE_FIELD_DESC_COL];
		if ('create date' === $fieldname) {
			continue;
		}
		if ('name' === $fieldname ) {
			$photo_date = $row[RECIPE_PHOTO_DATE_COL];
			if ($dt = strtotime($photo_date)) {
				$photo_date = date("Y-m-d", $dt);
			} else {
				$photo_date = null;
			}
			$camera_id = $row[RECIPE_CAMERA_ID_COL];
			if ('#N/A' == trim($camera_id) ) {
				$camera_id = null;
			} elseif (strpos($camera_id, '-')) {
				$camera_id = explode('-', $camera_id)[1];
			}

			if ($row[RECIPE_SUBMITTED_BATCH_COL]) {
				$recipe_status = "submitted";
			} elseif ($camera_id) {
				$recipe_status = "image";
			} elseif ($row[RECIPE_PRINTED_COL] ) {
				$recipe_status = "printed";
			} else {
				$recipe_status = 'entered';
			}
			$recipe_title = convert_recipe_title($fielddesc);
			$recipe_info['recipe_title'] = $recipe_title;
			$recipe_info['photo_date'] = $photo_date;
			$recipe_info['recipe_status'] = $recipe_status;
			$recipe_info['camera_id'] = $camera_id;
			$recipe_info['submission_batch'] = $row[RECIPE_SUBMITTED_BATCH_COL];
			continue;
		}
		if ('method' === $fieldname) {
			if ('' == trim($fielddesc)) {
				continue;
			}
			$instruct_info = convert_recipe_instructions($fielddesc);
			$instructs = $instruct_info['instructs'];
			$instruct_status = $instruct_info['status'];
			// $instructs = $fielddesc;
			// $instruct_status = 0;
			if ($instruct_status > 0) {
				$tmp = array( 
					'month' => $month,
					'recipe_id' => $worksheet_id,
					'recipe_title' => $recipe_info['recipe_title'],
					'recipe_instruct_cnt' => $row[RECIPE_FIELD_STEP_COL],
					'orig_instruct' => $fielddesc,
					'new_instruct' => $instructs,
				);
				$degree_converts[] = $tmp;
			}
			if ($instruct_status < 0) {
				$issue = -1 == $instruct_status ? "Had both 'fan-forced' and 'degree'" : "Temp outside range or not found";
				$tmp = array( 
					'month' => $month,
					'recipe_id' => $worksheet_id,
					'recipe_title' => $recipe_info['recipe_title'],
					'recipe_instruct_cnt' => $row[RECIPE_FIELD_STEP_COL],
					'orig_instruct' => $fielddesc,
					'new_instruct' => $instructs,
					'issue' => $issue,
				);
				$degree_converts_reviews[] = $tmp;
			}
			$group = convert_recipe_group($row[RECIPE_GROUP_COL]);
			$recipe_info['methods'][] = array( 
				'instruction' => $instructs,
				'recipe_group' => $group,
			);
		} elseif ('ingredient' === $fieldname) {
			if ('' == trim($fielddesc)) {
				continue;
			}
			$ingred_search_name = strtolower($fielddesc);
			$plural = 0;
			$fnd = array_search($ingred_search_name, $ingred_db_names);
			if (false === $fnd) {
				// $fnd = binary_search($fielddesc, $ingred_db_plurals);
				// not all ingredients have plurals so there are either gaps
				// in the keys or the nulls are included and it is not sequential
				// workarounds are too annoying to just do array search as there are 
				// far fewer plural ingredients
				$fnd = array_search($ingred_search_name, $ingred_db_plurals);
				if (false === $fnd) {
					echo "<h4>** $fielddesc NOT FOUND - Worksheet id: $worksheet_id **</h4>";
					$new_ingred_data = insert_new_ingredient($fielddesc, $ingreds, $ingred_db_names, $ingred_db_plurals);
					$new_ingred_row = $new_ingred_data['new_ingred_row'];
					$new_ingred_row['recipe_worksheet_id' ] = $worksheet_id;
					$new_ingred_row['brief_month' ] = $month;
					$not_found_ingreds[] = $new_ingred_row;
					$ingreds = $new_ingred_data['ingreds'];
					$ingred_db_names = $new_ingred_data['names'];
					$ingred_db_plurals = $new_ingred_data['plurals'];
					$fnd = $new_ingred_data['fnd'];
					// echo "<pre>";
					// print_r($new_ingred_data);
					// echo "</pre>";
					// die;
				} else {
					$plural = true;
				}
			}
			$ingred_id = $ingreds[$fnd]['id'];
			// check unit name, creating new unit if needed
			$unit_name = trim($row[RECIPE_UNIT_COL]);
			if ('' === $unit_name) {
				$unit_id = null;
				$unit_plural = null;
			} else {
				$unit_data = check_unit_data($units, $unit_db_names, $unit_db_plurals, $unit_name);
				if ($unit_data['new_unit_row']) {
					echo "<h4>Unit $unit_name NOT FOUND - Worksheet id: $worksheet_id Ingred: $fielddesc**</h4>";
					$new_unit_row = $unit_data['new_unit_row'];
					$new_unit_row['recipe_worksheet_id' ] = $worksheet_id;
					$new_unit_row['brief_month' ] = $month;
					$not_found_units[] = $new_unit_row;
				}
				$units = $unit_data['units'];
				$unit_db_names = $unit_data['unit_names'];
				$unit_db_plurals = $unit_data['unit_plurals'];
				$unit_fnd = $unit_data['unit_fnd'];
				$unit_plural = $unit_data['plural'];
				$unit_id = $units[$unit_fnd]['id'];
				// echo '<pre>';
				// print_r($unit_data);
				// echo '</pre>';
				// echo '<h2>unit id', $unit_id, '</h2>';
				// die;
			}
			$ingred_notes = convert_recipe_ingred_notes($row[RECIPE_NOTES_COL]);
			$group = convert_recipe_group($row[RECIPE_GROUP_COL]);
			$recipe_info['ingredients'][] = array( 
				'ingred_cnt' => $row[RECIPE_FIELD_CNT_COL],
				'ingred_id' => $ingred_id,
				'measure' => $row[RECIPE_MEASURE_COL],
				'unit' => $unit_id,
				'unit_plural' => $unit_plural,
				'notes' => $ingred_notes,
				'plural' => $plural,
				'recipe_group' => $group,
			);
		} elseif ('description' === $fieldname) {
			$recipe_desc = convert_recipe_desc($fielddesc);
			$recipe_info['description'] = $recipe_desc;
		} elseif (in_array($fieldname, array('prep_time', 'cook_time')))	{
			$time_desc = convert_recipe_times($fielddesc);
			$recipe_name[$fieldname] = $time_desc;
		}
		else {
			if ('type' === $fieldname) {
				$db_name = 'meal_type';
			} else {
				$db_name = str_replace(' ', '_', $fieldname);
			}
			$recipe_info[$db_name] = $fielddesc;
		}
	}

	if (isset($worksheet_id) ) {
		update_recipe_table_info($recipe_info, $worksheet_id);
		$update_cnt++;
	}

	if (count($not_found_ingreds)) {
		$new_names = array_column($not_found_ingreds, 'name');
		array_multisort($new_names, SORT_ASC, $not_found_ingreds );
		$report_data = array_values_multi($not_found_ingreds);
		create_report($sheets, $report_id, 'Ingredients', $report_data, false);	
	}

	if (count($not_found_units)) {
		$new_names = array_column($not_found_units, 'name');
		array_multisort($new_names, SORT_ASC, $not_found_units );
		$report_data = array_values_multi($not_found_units);
		create_report($sheets, $report_id, 'Units', $report_data, false);	
	}

	if (count($degree_converts)) {
		// $tmp = array( 
		// 	'Instructions with converted degrees and no issues detected',
		// 	'',
		// 	'',
		// 	'',
		// );
		// $report_data = array_merge($tmp, array_values_multi($degree_converts));
		$report_data = array_values_multi($degree_converts);
		create_report($sheets, $report_id, 'Degrees', $report_data, false);	
	}

	if (count($degree_converts_reviews)) {
		// $tmp = array( 
		// 	'Instructions with converted degrees and and possible issues',
		// 	'',
		// 	'',
		// 	'',
		// 	'',
		// );
		// $report_data = array_merge($tmp, array_values_multi($degree_converts_reviews));
		$report_data = array_values_multi($degree_converts_reviews);
		create_report($sheets, $report_id, 'Degrees', $report_data, false);	
	}

	return $update_cnt;
}

function new_recipe_info()  {
	return array(
		'recipe_title' => null,
		'description' => null,
		'servings' => null,
		'prep_time' => null,
		'cook_time' => null,
		'meal_type' => null,
		'cuisine' => null,
		'diet' => null,
		'recipe_tip' => null,
		'recipe_status' => 'entered',
		'ingredient_tip' => null,
		'source' => null,
		'photo_date' => null,
		'recipe_status' => null,
		'camera_id' => null,
		'submission_batch' => null,
		'ingredients' => array(),
		'methods' => array(),
	);
}

function insert_new_ingredient($ingred_name, $ingreds, $ingred_db_names, $ingred_db_plurals) {
	global $wpdb;

	$normalized = strtolower($ingred_name);
	$wpdb->insert('tc_ingredients', ['name' => $ingred_name, 'normalized' => $normalized]);
	$ingred_id = $wpdb->insert_id;
	$new_ingred_row = array( 
		'id' => $ingred_id,
		'name' => $ingred_name,
		'normalized' => $normalized,
		'pluralized' => '',
		'depluralize' => '',
		'derivative' => '',
		'mince' => '',
	);
	$new_ingred_search_row = array( 
		'id' => $ingred_id,
		'name' => $normalized,
		'normalized' => $normalized,
		'pluralized' => '',
		'depluralize' => '',
		'derivative' => '',
		'mince' => '',
	);
	$ingreds[] = $new_ingred_search_row;

	$names = array_column($ingreds, 'name');
	array_multisort($names, SORT_ASC, $ingreds );

	$ingred_db_names = array_column($ingreds, 'name');
	$ingred_db_plurals = array_column($ingreds, 'pluralized');
	$ingred_db_plurals = array_filter($ingred_db_plurals, function($row) {
		return $row;
	});
	array_walk($ingred_db_names, function(&$name) {
		$name = strtolower($name);
	});
	array_walk($ingred_db_plurals, function(&$name) {
		$name = strtolower($name);
	});
	
	$fnd = array_search($normalized, $ingred_db_names);

	return array( 
		'ingreds' => $ingreds,
		'names' => $ingred_db_names,
		'plurals' => $ingred_db_plurals,
		'fnd' => $fnd,
		'new_ingred_row' => $new_ingred_row,
	);

}
 
function update_recipe_table_info($recipe_info, $worksheet_id) {
	global $wpdb;

	// if ('339986' == $worksheet_id) {
	// 	echo "<pre>";
	// 	print_r($recipe_info);
	// 	echo "</pre>";
	// 	die;
	// }


	$ingredients = $recipe_info['ingredients'];
	$methods = $recipe_info['methods'];

	unset($recipe_info['ingredients']);
	unset($recipe_info['methods']);

	$update_result = $wpdb->update('tc_recipes', $recipe_info, ['worksheet_id' => $worksheet_id], null, ['%s']);

	if (false === $update_result) {
		echo "<h1>Could not update $worksheet_id</h1>";
		die;
	}

	$sql = "SELECT id FROM tc_recipes WHERE worksheet_id = %s";

	$recipe_id = $wpdb->get_var($wpdb->prepare($sql, $worksheet_id));

	if (count($ingredients)) {
		update_recipe_ingredients_table($ingredients, $recipe_id);
	}

	if (count($methods)) {
		update_recipe_instructions_table($methods, $recipe_id);
	}
	
}

function update_recipe_ingredients_table($ingredients, $recipe_id) {
	global $wpdb;

	$wpdb->delete('tc_recipe_ingredients', ['recipe_id' => $recipe_id], ['%d']);

	$insert_values = '';
	$insert_parms = [];

	foreach($ingredients as $cnt => $ingredient) {

		$insert_values .= '(%d, %d, %d, %s, %d, %d, %s, %d, %s),';
		$insert_parms[] = $recipe_id;
		$insert_parms[] = $cnt+1;
		$insert_parms[] = $ingredient['ingred_id'];
		$insert_parms[] = $ingredient['measure'];
		$insert_parms[] = $ingredient['unit'];
		$insert_parms[] = $ingredient['unit_plural'];
		$insert_parms[] = $ingredient['notes'];
		$insert_parms[] = $ingredient['plural'];
		$insert_parms[] = $ingredient['recipe_group'];

	}

	$insert_values = rtrim($insert_values, ',');

	$sql = "INSERT into tc_recipe_ingredients
		(recipe_id, ingred_cnt, ingred_id, measure, unit, unit_plural, notes, plural, recipe_group)
	VALUES $insert_values";

	$prepared_sql = $wpdb->prepare($sql, $insert_parms);

	$rows_affected = $wpdb->query($prepared_sql);
	 
}

function update_recipe_instructions_table($instructions, $recipe_id) {
	global $wpdb;

	$wpdb->delete('tc_recipe_instructions', ['recipe_id' => $recipe_id], ['%d']);

	$insert_values = '';
	$insert_parms = [];

	foreach($instructions as $cnt => $instruction) {

		$insert_values .= '(%d, %d, %s, %s),';
		$insert_parms[] = $recipe_id;
		$insert_parms[] = $cnt+1;
		$insert_parms[] = $instruction['instruction'];
		$insert_parms[] = $instruction['recipe_group'];

	}

	$insert_values = rtrim($insert_values, ',');

	$sql = "INSERT into tc_recipe_instructions
		(recipe_id, instruction_cnt, instruction, recipe_group)
	VALUES $insert_values";

	$prepared_sql = $wpdb->prepare($sql, $insert_parms);

	$rows_affected = $wpdb->query($prepared_sql);
	 
}

function check_unit_data($units, $unit_db_names, $unit_db_plurals, $unit_name) {
	$unit_search_name = strtolower($unit_name);
	$plural = 0;
	$new_unit_row = false;
	$fnd = array_search($unit_search_name, $unit_db_names);
	if (false === $fnd) {
		$fnd = array_search($unit_search_name, $unit_db_plurals);
		if (false === $fnd) {
			$new_unit_data = insert_new_measure_unit($unit_name, $units, $unit_db_names, $unit_db_plurals);
			$new_unit_row = $new_unit_data['new_unit_row'];
			$units = $new_unit_data['units'];
			$unit_db_names = $new_unit_data['names'];
			$unit_db_plurals = $new_unit_data['plurals'];
			$fnd = $new_unit_data['fnd'];
		} else {
			$plural = true;
		}
	}
	return array( 
		'new_unit_row' => $new_unit_row,
		'units' => $units,
		'unit_names' => $unit_db_names,
		'unit_plurals' => $unit_db_plurals,
		'unit_fnd' => $fnd,
		'plural' => $plural,
	);
}


function insert_new_measure_unit($unit_name, $units, $unit_db_names, $unit_db_plurals) {
	global $wpdb;

	$normalized = strtolower($unit_name);
	$wpdb->insert('tc_measure_units', ['name' => $unit_name, 'normalized' => $normalized]);
	$unit_id = $wpdb->insert_id;
	$new_unit_row = array( 
		'id' => $unit_id,
		'name' => $unit_name,
		'normalized' => $normalized,
		'pluralized' => '',
		'depluralize' => '',
		'mark' => '',
		'derivative' => '',
		'cheese' => '',
	);
	$new_unit_search_row = array( 
		'id' => $unit_id,
		'name' => $normalized,
		'normalized' => $normalized,
		'pluralized' => '',
		'depluralize' => '',
		'mark' => '',
		'derivative' => '',
		'cheese' => '',
	);
	$units[] = $new_unit_search_row;

	$names = array_column($units, 'name');
	array_multisort($names, SORT_ASC, $units );

	$unit_db_names = array_column($units, 'name');
	$unit_db_plurals = array_column($units, 'pluralized');
	$unit_db_plurals = array_filter($unit_db_plurals, function($row) {
		return $row;
	});
	array_walk($unit_db_names, function(&$name) {
		$name = strtolower($name);
	});
	array_walk($unit_db_plurals, function(&$name) {
		$name = strtolower($name);
	});
	
	$fnd = array_search($normalized, $unit_db_names);

	return array( 
		'units' => $units,
		'names' => $unit_db_names,
		'plurals' => $unit_db_plurals,
		'fnd' => $fnd,
		'new_unit_row' => $new_unit_row,
	);

}
 