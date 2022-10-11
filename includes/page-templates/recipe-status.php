<?php
/*
Template Name: Taste Creative Recipe Status
*/

/**
 * 	Test page for display the status of the recipes *** REACT VERSION
 *  Date:  9/28/2022
 * 	Author: Ron Boutilier
 */
defined('ABSPATH') or die('Direct script access disallowed.');

require_once RECIPE_SHEETS_PLUGIN_INCLUDES.'page-templates/partials/recipe-head.php';

wp_localize_script( 'recipe-sheets-main', 'recipeSheets', array(
	'apiUrl' => get_rest_url(),
	'nonce' => wp_create_nonce('wp_rest'),
) );

?>
<body>
	<div class="container">
		<header>Recipe Status Page</header>
		<div id="react-recipe-status-container"></div>
	</div>
	<?php wp_footer() ?>
</body>