<?php

defined('ABSPATH') or die('Direct script access disallowed.');

require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/load-working-images-dir.php';

function import_recipe_image_data($working_month, $month_info) {
	global $wpdb;
	$image_files = get_working_images_dir_info();

	if (!count($image_files)) {
		echo "<h2>No image files found</h2>";
		die;
	}
	
	$image_files_by_worksheet_id = array_column($image_files, null, 'worksheet_id');
	// echo "<pre>";
	// print_r($image_files_by_worksheet_id);
	// echo "</pre>";


	$worksheet_id_list = array_column($image_files, 'worksheet_id');
	$placeholders = array_fill(0, count($worksheet_id_list), '%s');
	$placeholders = implode(', ', $placeholders);

	$sql = "
		SELECT rec.* FROM tc_recipes rec
		WHERE rec.worksheet_id 	IN ($placeholders)
			AND rec.submission_batch IS NULL
	";

	$sql = $wpdb->prepare($sql, $worksheet_id_list);
	$recipe_rows = $wpdb->get_results($sql, ARRAY_A);

	if (!$recipe_rows) {
		echo "<h2>Error retreiving Recipe Table Rows</h2>";
		die;
	}
	
	// echo "<pre>";
	// print_r($recipe_rows);
	// echo "</pre>";
	
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