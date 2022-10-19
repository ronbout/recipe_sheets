<?php
/*
Template Name: Load Recipe Sheets Data
*/

/**
 *  Date:  10/18/2022
 * 	Author: Ron Boutilier
 */
defined('ABSPATH') or die('Direct script access disallowed.');

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
	<script src="<?php echo RECIPE_SHEETS_PLUGIN_INCLUDES_URL?>js/load-recipe-sheets-data.js"></script>
	<style>
		* {
			margin: 8px;
			padding: 8px;
		}
		.entry {
			width: 500px;
			margin: auto;
			height: 200px;
			border: 2px solid black;
			display: flex;
		}
		#results {
			width: 500px;
			margin: auto;
			height: 400px;
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
	<header>Launch Page for importing Recipe Data from Google Drive Sheets</header>

	<main>
		<div class="entry">
			<!--<div><input id="start-date" type="date" value="2020-01-01"></div>-->
			<div><button id="run-import-recipe-sheets" type="button">Run</button>	</div>
		</div>
		<div id="results">Results here</div>
	</main>

</body>