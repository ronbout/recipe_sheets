<?php
/*
Template Name: Compare Google Sheets
*/

/**
 *  Date:  11/14/2022
 * 	Author: Ron Boutilier
 */
defined('ABSPATH') or die('Direct script access disallowed.');

if (!is_user_logged_in(  )) {
	die("Must be logged in as admin to use this page");
}

$user = wp_get_current_user(  );
if ('administrator' != strtolower($user->roles[0])) {
	die("Must be logged in as admin to use this page");
}

?>
<!DOCTYPE HTML>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"
		integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
		crossorigin="anonymous">
	</script>
	<script src="<?php echo RECIPE_SHEETS_PLUGIN_INCLUDES_URL?>js/compare-google-sheets.js"></script>
	<style>
		* {
			margin: 8px;
			padding: 8px;
		}
		.entry {
			width: 900px;
			margin: auto;
			height: 700px;
			border: 2px solid black;
			display: flex;
			flex-direction: column;
		}
		.sheet-container input {
			width: 100%;
			padding: 12px;
		}
		.report-sheet-container {
			width: 500px;
			margin: 0 auto;
		}
		.button-container {
			text-align: center;
		}
		#results {
			width: 900px;
			margin: auto;
			min-height: 400px;
			border: 2px solid black;
		}
	</style>
</head>
<body>
	<?php 
		echo "
					<script>
						let recipeSheets = {}
						recipeSheets.ajaxurl = '". admin_url( 'admin-ajax.php' ) . "'
						recipeSheets.security = '" . wp_create_nonce('recipe_sheets-ajax-nonce') . "'
					</script>
				";
		?>
	<header>Compare Two Google Drive Sheets (same or different Workbooks)</header>

	<main>
		<div class="entry">
			<div style="display: flex;">
				<div class="sheet-container">
					<h3>Sheet 1 Information</h3>
					<div>
						<label for="sheet1-google-id">Google Sheet ID:</label>
						<input type="text" class="sheet-input" id="sheet1-google-id" value="1gygJeDkW3VG4Q2K6MdICHxYcMreSESQklsgzYwr66oQ">
					</div>
					<div>
						<label for="sheet1-name">Sheet Name:</label>
						<input type="text" class="sheet-input" id="sheet1-name" value="Recipe">
					</div>
				</div>
				<div class="sheet-container">
					<h3>Sheet 2 Information</h3>
					<div>
						<label for="sheet2-google-id">Google Sheet ID:</label>
						<input type="text" class="sheet-input" id="sheet2-google-id" value="1L7WMOZ2_idMfqnb-O5xXFReUJoRVPiaqxI67Viu7Hzk">
					</div>
					<div>
						<label for="sheet2-name">Sheet Name:</label>
						<input type="text" class="sheet-input" id="sheet2-name" value="Recipe">
					</div>
				</div>
			</div>
			<div>
				
			<div class="report-sheet-container sheet-container">
					<h3>Report Sheet Information</h3>
					<div>
						<label for="sheet2-google-id">Report Sheet ID:</label>
						<input type="text" class="sheet-input" id="report-google-id" value="1uXifJ-4RqCK05eZuqRBiJIjhmYftVn93S1TlfETpjr8">
					</div>
					<div>
						<label for="sheet2-name">Sheet Name:</label>
						<input type="text" class="sheet-input" id="report-name" value="Recipe">
					</div>
				</div>
			</div>
			<div class="button-container"><button id="run-compare-google-sheets" type="button">Compare</button>	</div>
		</div>
		<div id="results">Results here</div>
	</main>

</body>