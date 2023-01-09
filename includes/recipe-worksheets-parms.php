<?php

defined('ABSPATH') or die('Direct script access disallowed.');

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
define('BRIEF_SOURCE_ID_COL', 11);
define('BRIEF_WORKSHEET_ID_COL', 12);
define('BRIEF_TIER_COL', 13);
define('BRIEF_VIRGIN_ID_COL', 14);
define('BRIEF_RECIPE_TYPE_COL', 15);
define('BRIEF_PARENT_RECIPE_ID_COL', 16);  // dummy internal column for matching catalog to original recipe

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

define('RECIPE_FIELD_DESC_COL', 7);
define('RECIPE_MEASURE_COL', 8);
define('RECIPE_UNIT_COL', 9);
define('RECIPE_NOTES_COL', 10);
define('RECIPE_GROUP_COL', 11);

// define('RECIPE_FIELD_DESC_COL', 15);
// define('RECIPE_MEASURE_COL', 16);
// define('RECIPE_UNIT_COL', 18);
// define('RECIPE_NOTES_COL', 20);
// define('RECIPE_GROUP_COL', 21);

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

define('UNITS_NAME_COL', 0);
define('UNITS_NORMALIZED_COL', 1);
define('UNITS_PLURALIZED_COL', 2);
define('UNITS_DEPLURALIZE_COL', 3);
define('UNITS_MARK_COL', 4);
define('UNITS_DERIVATIVE_COL', 5);
define('UNITS_CHEESE_COL', 6);

define('MARCH_MAY_WORKING_DOC_ID', '1F3DdkZv7Gq4lu-0MyM68_HBGRg8NK1-sVZbIKBnqP74');
// define('MARCH_MAY_WORKING_DOC_ID', '1rYvA7f6TK2ZlvlRziobsESPGZtULfLb6z7PgwPbGFW8');
define('JUNE_WORKING_DOC_ID', '1XNONqFyWBN5qX-1fSt8zMZ7TMVkEsgPDb_H1OL6fc5Q');
define('JULY_WORKING_DOC_ID', '1mNnL-ltG17fCc9odp0W1sUouyGT88jBYKZKNHAq9kO8');
define('BRIEF_ID', '189HnWpTZDUaYRdsBwc-62sYpLu5cKv7u8Ujq9XiJ19E');
define('RECIPE_CATALOGUE_ID', '1Mc97WIj8HnXDwuUjUookzjnqEJt5Gq2Of9G4kp_JoXs');

define('MAY_IMAGE_FOLDER_ID', '1BE11FX91EJKwZ2AVp8-7amInaY2GuRkS');
define('JUNE_VIRGIN_FOLDER_ID', '1SG7FXcopp9UHxmU__fJfeGelhWo67EOo' );
define('JUNE_IMAGE_ALL_FOLDER_ID', '1JqkrIMVBltuGp8rBD_KEkOwqdBPGCM_e' );
define('JUNE_IMAGE_FOLDER_ID', '1VvcU1CvA4VK36KJkHS_cJn0P8Z4YPZJu' );
define('JUNE_IMAGE_SUB_FOLDER_ID', '1d52zsgu-Ge6Ycg0ptOml7vrgQgYa6G6N' );
define('JULY_IMAGE_FOLDER_ID', '10PVyCCGWzvy5KDfUVP9frru3Rpdial2y' );

define('JUNE_IMAGES_REPORT_ID', '1nqbWsHr9U03sU9eQyQtB8S8Je9zy_K1LZP50wYbRKWs' );
define('JULY_IMAGES_REPORT_ID', '1AFKzozjuDnKFt_ZtdBvC8aBIjDJYzl7mUgFCZZ5ddGY' );
define('MAY_IMAGES_REPORT_ID', '1sR2gV8oBHTCmTi2mcZSDBwnOVzfc6MQLBiZ9NmPlbdY' );
// define('RECIPE_ENTRY_IMPORT_REPORT_ID', '1gscTgNkA9Rb835Lljid_Mtf9EsyQSucv-S4aDGIK4vc' );
define('RECIPE_ENTRY_IMPORT_REPORT_ID', '1aFvzYhBalz1iEEl3V-byats_e2vXC5chpTFmG1nLcHY' );
define('GOOGLE_SHEET_COMPARE_REPORT_ID', '1uXifJ-4RqCK05eZuqRBiJIjhmYftVn93S1TlfETpjr8' );

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
	'2022-06-01' => array( 
		'worksheet_doc_id' => JUNE_WORKING_DOC_ID,
		'brief_doc_id' => BRIEF_ID,
		'brief_sheet_name' => 'June 2022',
		'month_display' => 'June',
	// 	'virgin_columns' => 2,
	),
	'2022-07-01' => array( 
		'worksheet_doc_id' => JULY_WORKING_DOC_ID,
		'brief_doc_id' => BRIEF_ID,
		'brief_sheet_name' => 'July 2022',
		'month_display' => 'July',
		'virgin_columns' => 2,
	),
);

require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/google_sheet_functions.php';
require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'recipe-field-conversions.php';
require_once RECIPE_SHEETS_PLUGIN_INCLUDES . 'report-functions.php';