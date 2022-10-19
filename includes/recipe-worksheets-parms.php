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

define('WORKSHEET_ID_COL', 0);
define('VIRGIN_ID_COL', 1);
define('RECIPE_TITLE_COL', 2);
define('RECIPE_TYPE_COL', 3);
define('MONTH_COL', 4);

define('MARCH_MAY_WORKING_DOC_ID', '1F3DdkZv7Gq4lu-0MyM68_HBGRg8NK1-sVZbIKBnqP74');
define('JUNE_WORKING_DOC_ID', '1XNONqFyWBN5qX-1fSt8zMZ7TMVkEsgPDb_H1OL6fc5Q');
define('BRIEF_ID', '189HnWpTZDUaYRdsBwc-62sYpLu5cKv7u8Ujq9XiJ19E');

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
		'virgin_columns' => 2,
	),
);