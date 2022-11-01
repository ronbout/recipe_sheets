<?php

define('CUISINE_COL', 0);
define('MEAL_TYPE_COL', 1);
define('CLASS_COL', 2);
define('DIETARY_COL', 3);
define('PREP_TIME_COL', 4);
define('EQUIPMENT_COL', 5);
define('RECIPE_COUNT_COL', 6);
define('NOTES_COL', 7);
define('BRIEF_RECIPE_TITLE_COL', 8);
define('BRIEF_VIRGIN_TITLE_COL', 9);
define('BRIEF_VIRGIN_TITLE2_COL', 10);
define('BRIEF_WORKSHEET_ID_COL', 11);
define('BRIEF_VIRGIN_ID_COL', 12);
define('BRIEF_RECIPE_TYPE_COL', 13);
define('BRIEF_ORIG_RECIPE_ID_COL', 14);  // dummy internal column for matching catalog to original recipe

define('WORKSHEET_ID_COL', 0);
define('VIRGIN_ID_COL', 1);
define('RECIPE_TITLE_COL', 2);
define('RECIPE_TYPE_COL', 3);
define('MONTH_COL', 4);

define('ENTRY_MONTH_COL', 0);
define('ENTRY_RECIPE_TYPE_COL', 1);
define('ENTRY_VIRGIN_ID_COL', 2);
define('ENTRY_RECIPE_WORKSHEET_ID_COL', 3);
define('RECIPE_FIELD_COL', 4);
define('RECIPE_FIELD_CNT_COL', 5);
define('RECIPE_FIELD_STEP_COL', 6);

// define('RECIPE_FIELD_DESC_COL', 7);
// define('RECIPE_MEASURE_COL', 8);
// define('RECIPE_UNIT_COL', 9);
// define('RECIPE_NOTES_COL', 10);
// define('RECIPE_GROUP_COL', 11);

define('RECIPE_FIELD_DESC_COL', 15);
define('RECIPE_MEASURE_COL', 16);
define('RECIPE_UNIT_COL', 18);
define('RECIPE_NOTES_COL', 20);
define('RECIPE_GROUP_COL', 21);

define('RECIPE_PHOTO_DATE_COL', 24);
define('RECIPE_PRINTED_COL', 25);
define('RECIPE_CAMERA_ID_COL', 26);
define('RECIPE_SUBMITTED_BATCH_COL', 27);

define('SUPPORT_RECIPE_WORKSHEET_ID_COL', 0);
define('SUPPORT_MONTH_COL', 1);
define('SUPPORT_RECIPE_STATUS_COL', 2);
define('SUPPORT_RECIPE_SOURCE_COL', 4);

define('INGRED_NAME_COL', 0);
define('INGRED_NORMALIZED_COL', 1);
define('INGRED_PLURALIZED_COL', 2);
define('INGRED_DEPLURALIZE_COL', 3);
define('INGRED_DERIVATIVE_COL', 4);
define('INGRED_MINCE_COL', 5);

// define('MARCH_MAY_WORKING_DOC_ID', '1F3DdkZv7Gq4lu-0MyM68_HBGRg8NK1-sVZbIKBnqP74');
define('MARCH_MAY_WORKING_DOC_ID', '1rYvA7f6TK2ZlvlRziobsESPGZtULfLb6z7PgwPbGFW8');
define('JUNE_WORKING_DOC_ID', '1XNONqFyWBN5qX-1fSt8zMZ7TMVkEsgPDb_H1OL6fc5Q');
define('JULY_WORKING_DOC_ID', '1mNnL-ltG17fCc9odp0W1sUouyGT88jBYKZKNHAq9kO8');
define('BRIEF_ID', '189HnWpTZDUaYRdsBwc-62sYpLu5cKv7u8Ujq9XiJ19E');

define('IMAGE_FOLDER_ID', '1n7qWQExvik_hoXsnMIBpjaEbnV-IAIsc');
define('VIRGIN_FOLDER_ID', '16JSXrgQCLyUIyD-k6tY3Cj9HR6y2vtmz' );

$recipe_worksheets_parms = array(
	'2022-03-01' => array( 
		'worksheet_doc_id' => MARCH_MAY_WORKING_DOC_ID,
		'brief_doc_id' => BRIEF_ID,
		'brief_sheet_name' => 'March 2022',
		'month_display' => 'March',
		'virgin_columns' => 2 ,
	),
	'2022-04-01' => array( 
		'worksheet_doc_id' => MARCH_MAY_WORKING_DOC_ID,
		'brief_doc_id' => BRIEF_ID,
		'brief_sheet_name' => 'April 2022',
		'month_display' => 'April',
		'virgin_columns' => 2,
	),
	'2022-05-01' => array( 
		'worksheet_doc_id' => MARCH_MAY_WORKING_DOC_ID,
		'brief_doc_id' => BRIEF_ID,
		'brief_sheet_name' => 'May 2022',
		'month_display' => 'May',
		'virgin_columns' => 2,
	),
	// '2022-06-01' => array( 
	// 	'worksheet_doc_id' => JUNE_WORKING_DOC_ID,
	// 	'brief_doc_id' => BRIEF_ID,
	// 	'brief_sheet_name' => 'June 2022',
	// 	'month_display' => 'June',
	// // 	'virgin_columns' => 2,
	// ),
	// '2022-07-01' => array( 
	// 	'worksheet_doc_id' => JULY_WORKING_DOC_ID,
	// 	'brief_doc_id' => BRIEF_ID,
	// 	'brief_sheet_name' => 'July 2022',
	// 	'month_display' => 'July',
	// 	'virgin_columns' => 2,
	// ),
);


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