<?php
/*
Template Name: Taste Creative Build Worksheet
*/

/**
 * 	Test page building the Working Doc Worksheet
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
				include RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/build-worksheet-from-brief.php';
			?>
		</div>
	</div>
	<?php wp_footer() ?>
</body>