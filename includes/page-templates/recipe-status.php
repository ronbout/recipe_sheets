<?php
/*
Template Name: Venue React Portal
*/

/**
 * 	Test page for display the status of the recipes *** REACT VERSION
 *  Date:  9/28/2022
 * 	Author: Ron Boutilier
 */
defined('ABSPATH') or die('Direct script access disallowed.');

require_once RECIPE_SHEETS_PLUGIN_INCLUDES.'page-templates/partials/recipe-head.php';

?>
<body>
	<div class="container">
		<header>Recipe Status Page</header>
		<div>
			<?php
				include RECIPE_SHEETS_PLUGIN_INCLUDES . 'google-apis/get-recipe-requests.php';
			?>
		</div>
		<div id="react-recipe-status-container"></div>
	</div>
	<?php wp_footer() ?>
</body>