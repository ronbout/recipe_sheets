<?php
/*
Template Name: Taste Creative Load Recipe Entry
*/

/**
 * 	Test page for loading the recipe names per request from the Working Doc
 *  Date:  9/28/2022
 * 	Author: Ron Boutilier
 */
defined('ABSPATH') or die('Direct script access disallowed.');

require_once RECIPE_SHEETS_PLUGIN_INCLUDES.'page-templates/partials/recipe-head.php';

?>
<body>
	<div class="container">
		<header>Recipe Requests:</header>
		<div>
			<?php
				include RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/get-recipe-entry-info.php';
			?>
		</div>
	</div>
	<?php wp_footer() ?>
</body>