<?php

defined('ABSPATH') or die('Direct script access disallowed.');

/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return An authorized Analytics Reporting API V4 service object.
 */
function initializeSheets()
{

	// Use the developers console and download your service account
	// credentials in JSON format. Place them in this directory or
	// change the key file location if necessary.
	$KEY_FILE_LOCATION = RECIPE_SHEETS_PLUGIN_INCLUDES. 'google-apis/credentials.json';

	// Create and configure a new client object.
	$client = new Google_Client();
	$client->setApplicationName("Google Sheets");
	$client->setAuthConfig($KEY_FILE_LOCATION);
	$client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);
	$sheets = new Google\Service\Sheets($client);

	return $sheets;
}

function array_values_multi($arr) {
	return array_values(array_map(function ($inner) {
		return array_map(function ($val) {
			return null === $val ? '' : $val;
		},array_values($inner));
	}, $arr));
}

function create_report($sheets=null, $sheet_id, $sheet_name, $data, $overwrite=true) {
	if (!$sheets) {
		$sheets = initializeSheets();
	}

	if ($overwrite) {
		$current_rows = read_cells_from_sheet($sheets, $sheet_id, $sheet_name, 'A2:Z');
		$report_rows_cnt = count($current_rows);
		if ($report_rows_cnt) {
			$range = 'A2:Z' . ($report_rows_cnt + 1);
			clear_cells_in_sheet($sheets, $sheet_id, $sheet_name, $range);
		}
		return write_cells_to_sheet($sheets, $sheet_id, $sheet_name, 'A2', $data);
	}

	// append the data to the end of the rows
	return append_cells_to_sheet($sheets, $sheet_id, $sheet_name, 'A1:Z', $data);

}

function clear_report($sheets=null, $sheet_id, $sheet_name) {
	if (!$sheets) {
		$sheets = initializeSheets();
	}
	$current_rows = read_cells_from_sheet($sheets, $sheet_id, $sheet_name, 'A2:Z');
	$report_rows_cnt = count($current_rows);
	if ($report_rows_cnt) {
		$range = 'A2:Z' . ($report_rows_cnt + 1);
		clear_cells_in_sheet($sheets, $sheet_id, $sheet_name, $range);
	}

}

function read_cells_from_sheet($sheets, $sheet_id, $sheet_name, $sheet_range) {
	try{
		$range = "$sheet_name!$sheet_range";
		$response = $sheets->spreadsheets_values->get($sheet_id, $range);
		$recipe_rows = $response->getValues();

		return $recipe_rows;
		}
		catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo 'Message: ' .$e->getMessage();
		}
}


function write_cells_to_sheet($sheets, $sheet_id, $sheet_name, $sheet_range, $sheet_rows) {
	try{
		$range = "$sheet_name!$sheet_range";
		$body = new Google_Service_Sheets_ValueRange(['values' => $sheet_rows]);

		$params = ['valueInputOption' => 'RAW'];
		$response = $sheets->spreadsheets_values->update($sheet_id, $range,
		$body, $params);
		return $response;
	}
	catch(Exception $e) {
		// TODO(developer) - handle error appropriately
		echo 'Message: ' .$e->getMessage();
		return false;
	}
}

function append_cells_to_sheet($sheets, $sheet_id, $sheet_name, $sheet_range, $sheet_rows) {
	try{
		$range = "$sheet_name!$sheet_range";
		$body = new Google_Service_Sheets_ValueRange(['values' => $sheet_rows]);

		$params = ['valueInputOption' => 'RAW'];
		$response = $sheets->spreadsheets_values->append($sheet_id, $range,
		$body, $params);
		return $response;
	}
	catch(Exception $e) {
		// TODO(developer) - handle error appropriately
		echo 'Message: ' .$e->getMessage();
		return false;
	}
}

function clear_cells_in_sheet($sheets, $sheet_id, $sheet_name, $sheet_range) {
	try{
		$range = "$sheet_name!$sheet_range";
		$body = new Google_Service_Sheets_ClearValuesRequest();

		$params = ['valueInputOption' => 'RAW'];
		$response = $sheets->spreadsheets_values->clear($sheet_id, $range, $body);
		return $response;
	}
	catch(Exception $e) {
		// TODO(developer) - handle error appropriately
		echo 'Message: ' .$e->getMessage();
		return false;
	}
}