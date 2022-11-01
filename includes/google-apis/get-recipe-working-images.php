<?php

defined('ABSPATH') or die('Direct script access disallowed.');

require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/load-working-images-dir.php';

function import_recipe_image_data($working_month, $month_info, $recipe_type) {
	global $wpdb;
	$image_files = get_working_images_dir_info($recipe_type);

	if (!count($image_files)) {
		echo "<h2>No image files found</h2>";
		die;
	}

	$missing_worksheet_id_images = array_filter($image_files, function($image_info) {
		return !isset($image_info['worksheet_id']) || !trim($image_info['worksheet_id']);
	});

	if (count($missing_worksheet_id_images)) {
		echo '<h2>Images with No Worksheet Id (Description): </h2>';
		echo "<pre>";
		print_r($missing_worksheet_id_images);
		echo "</pre>";
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


	if (count($dup_list)) {
		echo '<h2>Duplicates: </h2>';
		echo "<pre>";
		print_r($dup_list);
		echo "</pre>";
	}

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
			WHERE rec.recipe_type = 'Catalog'
		";
		// 	AND rec.submission_batch IS NULL
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

	if (count($missing_images)) {
		echo '<h2>Missing Images - Recipe ids in Submission List: </h2>';
		echo "<pre>";
		print_r($missing_images);
		echo "</pre>";
	}

	if (count($fnd_missing_recipes)) {
		echo '<h2>Images Worksheet Recipe Ids not in Submission List: </h2>';
		echo "<pre>";
		print_r($fnd_missing_recipes);
		echo "</pre>";
	}

	if (count($not_fnd_missing_recipes)) {
		echo '<h2>Image Worksheet Recipe Ids not in System: </h2>';
		echo "<pre>";
		print_r($not_fnd_missing_recipes);
		echo "</pre>";
	}


	die;
	// loop through each and update  recipe file if not already submitted

	$upd_cnt = 0;
	foreach($recipe_rows as $recipe_row) {
		$worksheet_id = $recipe_row['worksheet_id'];
		$image_info = $image_files_by_worksheet_id[$worksheet_id];
		$google_id = $image_info['id'];
		$camera_id = $image_info['name'];
		if ($camera_id != $recipe_row['camera_id']) {
			echo "<h3>Mismatch camera id for worksheet $worksheet_id</h3>";
			echo "<p>Drive file name: $camera_id  --- table camera id: $recipe_row[camera_id]</p>";
		}
		$upd_success = update_recipe_row($recipe_row['id'], $google_id);
		$upd_cnt += $upd_success;
	}

	echo "<h2>$upd_cnt recipes updates</h2>";
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

	return array( 
		'fnd_missing_recipe_rows' => $missing_recipe_rows,
		'not_fnd_missing_recipe_ids' => $not_fnd_missing_recipe_ids,
		'missing_image_worksheet_ids' => $missing_image_list,
	);
}

function update_recipe_row($recipe_id, $google_id) { 
	global $wpdb;

	$image_url = "https://drive.google.com/file/d/$google_id/view?usp=drivesdk";
	$result = $wpdb->update('tc_recipes', array('image_url' => $image_url), array('id' => $recipe_id) );

	if (!$result) {
		echo "<h2>Error Updating Recipe Id: $recipe_id</h2>";
		echo "<p>$wpdb->error</p>";
		return 0;
	}
	return 1;
}