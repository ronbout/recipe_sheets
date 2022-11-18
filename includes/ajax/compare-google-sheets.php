<?php 

defined('ABSPATH') or die('Direct script access disallowed.');

function compare_google_sheets($sheets_info) {
	require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'recipe-worksheets-parms.php';

	$report_id = GOOGLE_SHEET_COMPARE_REPORT_ID;
	$sheets = initializeSheets();

	$id1 = $sheets_info['id1'];
	$id2 = $sheets_info['id2'];
	$name1 = $sheets_info['name1'];
	$name2 = $sheets_info['name2'];
	$report_id = $sheets_info['reportId'];
	$report_name = $sheets_info['reportName'];

	$sheet1 = get_comparison_sheet($sheets, $id1, $name1);
	$sheet1 = array_map(function($row) {
		array_unshift($row, trim(strtolower($row[0])) . '-' . strtolower(trim($row[1])));
		return $row;
	}, $sheet1);
	$sheet2 = get_comparison_sheet($sheets, $id2, $name2);
	$sheet2 = array_map(function($row) {
		array_unshift($row, trim(strtolower($row[0])) . '-' . strtolower(trim($row[1])));
		return $row;
	}, $sheet2);

	$sheet1_by_key = array_column($sheet1, null, 0);
	$sheet2_by_key = array_column($sheet2, null, 0);

	// echo '<pre>';
	// print_r($sheet1_by_key);
	// print_r($sheet2_by_key);
	// echo '</pre>';

	$keys1 = array_keys($sheet1_by_key);
	$keys2 = array_keys($sheet2_by_key);

	$all_keys = array_values(array_unique(array_merge($keys1, $keys2)));
	sort($all_keys);

	$comp_array = array_reduce($all_keys, function ($comp, $key) use ($sheet1_by_key, $sheet2_by_key) {
		$tmp = array( 
			isset($sheet1_by_key[$key]) ? $sheet1_by_key[$key] : null,
			isset($sheet2_by_key[$key]) ? $sheet2_by_key[$key] : null,
		);
		$comp[$key] = $tmp;
		return $comp;
	}, array());

	// echo '<pre>';
	// print_r($comp_array);
	// echo '</pre>';

	$comp_results = array_filter($comp_array, function($comp_rows) {
		return compare_diff_row($comp_rows[0], $comp_rows[1]);
	});

	echo "<h3>diff results</h3>";
	echo '<pre>';
	print_r(array_values($comp_results));
	echo '</pre>';
	echo '<h3>End of diff</h3>';

	
	clear_report($sheets, $report_id, $report_name);	
	if (count($comp_results)) {
		$report_data = create_compare_report_data($comp_results);
		create_report($sheets, $report_id, $report_name, $report_data, false);	
	}

}


function get_comparison_sheet($sheets, $sheet_id, $sheet_name) {
	try{
		$range = "$sheet_name!A2:AZ";
		$response = $sheets->spreadsheets_values->get($sheet_id, $range);
		$recipe_rows = $response->getValues();

		return $recipe_rows;
		}
		catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo 'Message: ' .$e->getMessage();
		}
}

function compare_diff_row($row1, $row2) {
	if (count($row1) !== count($row2) ) {
		return true;
	}

	foreach ($row1 as $ndx => $val) {
		$cmp1 = $val;
		$cmp2 = $row2[$ndx];
		$cmp1 = is_string($cmp1) ? trim(strtolower($cmp1)) : $cmp1;
		$cmp2 = is_string($cmp1) ? trim(strtolower($cmp2)) : $cmp2;
		if ($cmp1 != $cmp2)  {
			return true;
		}
	}
	return false;
}

function create_compare_report_data($diff_rows) {
	$report_data = array_reduce($diff_rows, function($report_data, $diff_info) {
		$line1 = $diff_info[0];
		$line1 = is_array($line1) ? $line1 : array();
		array_shift($line1);
		$line1 = array_pad($line1, 50, '');
		$line2 = $diff_info[1];
		$line2 = is_array($line2) ? $line2 : array();
		array_shift($line2);
		$line2 = array_pad($line2, 50, '');
		$report_data[] = $line1;
		$report_data[] = $line2;
		$report_data[] = array_fill(0,50,'');
		return $report_data;

	}, array());

	return $report_data;
}
 