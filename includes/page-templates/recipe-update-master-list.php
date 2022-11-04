<?php
/*
Template Name: Taste Creative Update Recipe Master List
*/

/**
 * 	Test page building the Working Doc Worksheet
 *  Date:  11/03/2022
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
				include RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/update-master-list.php';
			?>
		</div>
	</div>
	<?php wp_footer() ?>
</body>