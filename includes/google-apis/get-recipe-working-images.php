<?php

defined('ABSPATH') or die('Direct script access disallowed.');

require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/load-working-images-dir.php';

function import_recipe_image_data($working_month, $month_info, $recipe_type) {
	global $wpdb;

	$report_id = JUNE_VIRGIN_IMAGES_REPORT_ID;
	$image_files = get_working_images_dir_info($recipe_type);

	if (!count($image_files)) {
		echo "<h2>No image files found</h2>";
		die;
	}
	
	$sheets = initializeSheets();

	// echo '<pre>';
	// print_r($image_files);
	// echo '</pre>';
	// die;

	$process_images_flg = true;

	$missing_worksheet_id_images = array_filter($image_files, function($image_info) {
		return !isset($image_info['worksheet_id']) || !trim($image_info['worksheet_id']);
	});

	if (count($missing_worksheet_id_images)) {
		echo '<h2>Images with No Worksheet Id (Description): </h2>';
		echo "<pre>";
		print_r($missing_worksheet_id_images);
		echo "</pre>";
		$report_data = array_values_multi($missing_worksheet_id_images);
		create_report($sheets, $report_id, 'Missing Ids', $report_data);		
	} else {
		clear_report($sheets, $report_id, 'Missing Ids');	
	}

	$image_files = array_filter($image_files, function($image_info) {
		return isset($image_info['worksheet_id']) && trim($image_info['worksheet_id']);
	});
	
	$image_files_by_worksheet_id = array_column($image_files, null, 'worksheet_id');

	$tmp_list = array();

	foreach($image_files as $ndx => $img_info) {
		$worksheet_id = $img_info['worksheet_id'];
		if (!isset($tmp_list[$worksheet_id])) {
			$tmp_list[$worksheet_id] = array($img_info);
		} else {
			$tmp_list[$worksheet_id][] = $img_info; 
		}
	}

	$dup_list = array_filter($tmp_list, function($worksheet_info) {
		return count($worksheet_info) > 1;
	});


	// echo "<pre>";
	// print_r($image_files);
	// print_r($image_files_by_worksheet_id);
	// echo "</pre>";

	if ('WO' === $recipe_type) {
		$sql = "
			SELECT rec.*
			FROM tc_recipes rec
			JOIN tc_recipe_requests req ON req.id = rec.request_id
			WHERE req.month_year = %s
			AND rec.recipe_type = 'WO'
			AND rec.submission_batch IS NULL
		";
	
		$sql = $wpdb->prepare($sql, $working_month);
		$recipe_rows = $wpdb->get_results($sql, ARRAY_A);
	} else {
		$sql = "
		SELECT rec.*
		FROM tc_recipes rec
		JOIN tc_recipe_requests req ON req.id = rec.request_id
		WHERE rec.recipe_type = 'Catalog'
			AND req.tier = 'Virgin'
			AND req.month_year = '2022-06-01'
			";
		// 	AND rec.submission_batch IS null
		// ";
	
		$recipe_rows = $wpdb->get_results($sql, ARRAY_A);
	}

	if (!$recipe_rows) {
		echo "<h2>Error retreiving Recipe Table Rows</h2>";
		die;
	}

	$recipes_by_worksheet_id = array_column($recipe_rows, null, 'worksheet_id');

	$missing_info = check_missing_info($image_files_by_worksheet_id, $recipes_by_worksheet_id);

	$missing_images = $missing_info['missing_image_worksheet_ids'];
	$fnd_missing_recipes = $missing_info['fnd_missing_recipe_rows'];
	$not_fnd_missing_recipes = $missing_info['not_fnd_missing_recipe_ids'];
	$fnd_missing_recipes_by_worksheet_id = $missing_info['fnd_missing_recipes_by_worksheet_id'];
	$matching_recipe_images = $missing_info['matching_recipe_images'];
	
	
	if (count($matching_recipe_images)) {
		$report_data = array_values_multi($matching_recipe_images);
		create_report($sheets, $report_id, 'Matched Images', $report_data);	
	} else {
		clear_report($sheets, $report_id, 'Matched Images');	
	}

	if (count($dup_list)) {
		// get recipe titles
		$dup_names = array_reduce(array_keys($dup_list), function ($lst, $worksheet_id) use ($recipes_by_worksheet_id, $fnd_missing_recipes_by_worksheet_id) {
			if (isset($recipes_by_worksheet_id[$worksheet_id])) {
				$name =  $recipes_by_worksheet_id[$worksheet_id]['recipe_title'];
			} elseif (isset($fnd_missing_recipes_by_worksheet_id[$worksheet_id])) {
				$name =  $fnd_missing_recipes_by_worksheet_id[$worksheet_id]['recipe_title'];
			} else {
				$name =  'N/A';
			}
			$lst[$worksheet_id] = $name;
			return $lst;
		}, array_keys());

		$process_images_flg = false;
		$report_data = array_reduce($dup_list, function($rpt, $dup_arr) use ($dup_names) {
			$dup_arr = array_map(function($arr) use ($dup_names) {
				$worksheet_id = $arr['worksheet_id'];
				$tmp = array( 
					$arr['name'],
					$arr['worksheet_id'],
					$dup_names[$worksheet_id],
					$arr['image_url'],
				);
				return $tmp;
			}, $dup_arr);

			echo '<pre>';
			print_r($dup_arr);
			echo '</pre>';


			return array_merge($rpt,  $dup_arr);
		}, array());
		echo '<h2>Duplicates (correct before processing): </h2>';
		echo "<pre>";
		print_r($report_data);
		echo "</pre>";
		create_report($sheets, $report_id, 'Duplicate Recipe Ids', $report_data);		
	} else {
		clear_report($sheets, $report_id, 'Duplicate Recipe Ids');	
	}

	if (count($missing_images)) {
		echo '<h2>Missing Images - Recipe ids in Submission List but no Image: </h2>';
		echo "<pre>";
		print_r($missing_images);
		echo "</pre>";
		$report_data = array_values_multi($missing_images);
		create_report($sheets, $report_id, 'Missing Images', $report_data);		
	} else {
		clear_report($sheets, $report_id, 'Missing Images');	
	}

	if (count($not_fnd_missing_recipes)) {
		echo '<h2>Image Worksheet Recipe Ids, but recipes not in System: </h2>';
		echo "<pre>";
		print_r($not_fnd_missing_recipes);
		echo "</pre>";
		$report_data = array_values_multi($not_fnd_missing_recipes);
		create_report($sheets, $report_id, 'Unknown Recipes', $report_data);	
	} else {
		clear_report($sheets, $report_id, 'Unknown Recipes');	
	}

	if (count($fnd_missing_recipes)) {
		echo '<h2>Images Worksheet Recipe Ids exist, but recipes not in Submission List: </h2>';
		echo "<pre>";
		print_r($fnd_missing_recipes);
		echo "</pre>";
		$report_data = array_values_multi($fnd_missing_recipes);
		create_report($sheets, $report_id, 'Recipes Not in Submission List', $report_data);	
	} else {
		clear_report($sheets, $report_id, 'Recipes Not in Submission List');	
	}

	// if (!$process_images_flg) {
	// 	echo '<h2>Processing will not occur due to Duplicate Images</h2>';
	// 	die;
	// }

	$upd_cnt = process_images($recipe_rows, $image_files_by_worksheet_id, $sheets, $report_id);

	echo "<h2>$upd_cnt recipes updated</h2>";
}

function check_missing_info($images, $recipes) {
	global $wpdb;

	$image_id_list = array_keys($images);
	$recipe_id_list = array_keys($recipes);
	

	sort($image_id_list);
	sort($recipe_id_list);

	// echo "<pre>";
	// print_r($image_id_list);
	// print_r($recipe_id_list);
	// echo "</pre>";
	// die;

	$missing_recipe_list = array_values(array_diff($image_id_list, $recipe_id_list));
	$missing_image_list = array_values(array_diff($recipe_id_list, $image_id_list));
	$matching_image_list = array_values(array_intersect($image_id_list, $recipe_id_list));


	if (count($missing_recipe_list)) {	
		$placeholders = array_fill(0, count($missing_recipe_list), '%s');
		$placeholders = implode(', ', $placeholders);
	
		$sql = "
			SELECT rec.* FROM tc_recipes rec
			WHERE rec.worksheet_id 	IN ($placeholders)
		";
	
		$sql = $wpdb->prepare($sql, $missing_recipe_list);
		$missing_recipe_rows = $wpdb->get_results($sql, ARRAY_A);
	} else {
		$missing_recipe_rows = array();
	}

	$fnd_missing_recipe_ids = array_column($missing_recipe_rows, 'worksheet_id');
	$not_fnd_missing_recipe_ids = array_values(array_diff($missing_recipe_list, $fnd_missing_recipe_ids));

	$fnd_missing_recipes_by_worksheet_id = array_column($missing_recipe_rows, null, 'worksheet_id');
	$fnd_missing_recipe_images = array_reduce($images, function($lst, $img) use ($fnd_missing_recipes_by_worksheet_id) {
		$worksheet_id = $img['worksheet_id'];
		// if (in_array($worksheet_id, $fnd_missing_recipe_ids) ) {
		if (isset($fnd_missing_recipes_by_worksheet_id[$worksheet_id]) ) {
			$tmp = array( 
				'name' => $img['name'],
				'worksheet_id' => $img['worksheet_id'],
				'title' => $fnd_missing_recipes_by_worksheet_id[$worksheet_id]['recipe_title'],
				'image_url' => $img['image_url'],
			);
			$lst[] = $tmp;
		}
		return $lst;
	}, array());
	
	$matching_recipe_images = array_reduce($images, function($lst, $img) use ($matching_image_list, $recipes) {
		$worksheet_id = $img['worksheet_id'];
		// if (in_array($worksheet_id, $fnd_missing_recipe_ids) ) {
		if (in_array($worksheet_id, $matching_image_list )) {
			$tmp = array( 
				'name' => $img['name'],
				'worksheet_id' => $img['worksheet_id'],
				'title' => $recipes[$worksheet_id]['recipe_title'],
				'image_url' => $img['image_url'],
			);
			$lst[] = $tmp;
		}
		return $lst;
	}, array());

	$not_fnd_missing_recipe_images = array_reduce($images, function($lst, $img) use ($not_fnd_missing_recipe_ids) {
		$worksheet_id = $img['worksheet_id'];
		if (in_array($worksheet_id, $not_fnd_missing_recipe_ids) ) {
			$lst[] = $img;
		}
		return $lst;
	}, array());

	$missing_images_recipe_info = array_reduce($recipes, function($lst, $recipe) use ($missing_image_list) {
		$worksheet_id = $recipe['worksheet_id'];
		if (in_array($worksheet_id, $missing_image_list) ) {
			$lst[] = array('worksheet_id' => $worksheet_id, 'recipe_title' => $recipe['recipe_title']);
		}
		return $lst;
	}, array());

	return array( 
		'fnd_missing_recipe_rows' => $fnd_missing_recipe_images,
		'fnd_missing_recipes_by_worksheet_id' => $fnd_missing_recipes_by_worksheet_id,
		'not_fnd_missing_recipe_ids' => $not_fnd_missing_recipe_images,
		'missing_image_worksheet_ids' => $missing_images_recipe_info,
		'matching_recipe_images' => $matching_recipe_images,
	);
}

function process_images($recipe_rows, $image_files_by_worksheet_id, $sheets, $report_id) {	
	$upd_cnt = 0;
	clear_report($sheets, $report_id, 'Mismatch Camera ID');	
	foreach($recipe_rows as $recipe_row) {
		$worksheet_id = $recipe_row['worksheet_id'];
		if (!isset($image_files_by_worksheet_id[$worksheet_id])) {
			continue;
		}
		$image_info = $image_files_by_worksheet_id[$worksheet_id];
		$google_id = $image_info['id'];
		$camera_info = explode('-',$image_info['name']);
		$photo_date = $camera_info[0];
		$camera_id = $camera_info[1];
		if ($recipe_row['camera_id'] && $camera_id != $recipe_row['camera_id']) {
			echo "<h3>Mismatch camera id for worksheet $worksheet_id</h3>";
			echo "<p>Drive file name: $camera_id  --- table camera id: $recipe_row[camera_id]</p>";
			$report_data = array(array($worksheet_id, $recipe_row['recipe_title'], $recipe_row['camera_id'], $camera_id));
			create_report($sheets, $report_id, 'Mismatch Camera ID', $report_data, false);		
		}
		$upd_success = update_recipe_row($recipe_row['id'], $google_id, $camera_id, $photo_date);
		$upd_cnt += $upd_success;
	}

	return $upd_cnt;
}

function update_recipe_row($recipe_id, $google_id, $camera_id, $photo_date) { 
	global $wpdb;

	$image_url = "https://drive.google.com/file/d/$google_id/view?usp=drivesdk";
	$result = $wpdb->update('tc_recipes', 
		array('image_url' => $image_url, 'camera_id' => $camera_id, 'photo_date' => $photo_date), 
		array('id' => $recipe_id) );

	if (false === $result) {
		echo "<h2>Error Updating Recipe Id: $recipe_id</h2>";
		echo "<p>$wpdb->error</p>";
		return 0;
	}
	return 1;
}

function get_recipe_titles($worksheet_ids) {

}