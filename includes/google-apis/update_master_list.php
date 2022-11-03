<?php

defined('ABSPATH') or die('Direct script access disallowed.');
// Load the Google API PHP Client Library.
// and access <<MONTH>>  Working Document
require_once __DIR__ . '/vendor/autoload.php';

require RECIPE_SHEETS_PLUGIN_INCLUDES . 'recipe-worksheets-parms.php';

define('MASTER_RECIPE_CNT_COL', 0);
define('MASTER_WORKING_MONTH_COL', 1);
define('MASTER_WORKSHEET_ID_COL', 2);
define('MASTER_VIRGIN_ID_COL', 2);
define('MASTER_RECIPE_TITLE_COL', 3);
define('MASTER_RECIPE_TYPE_COL', 5);
define('MASTER_WORKING_MONTH_COL', 6);

// load in June and July recipes from db



// load in master list

// get last recipe row number (col 1) and spreadsheet row number

// calc row location to insert 

// add recipes in correct order
// DONE