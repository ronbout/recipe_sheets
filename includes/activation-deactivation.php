<?php 

defined('ABSPATH') or die('Direct script access disallowed.');

/**
 *  ACTIVATION CODE 
 *  Add recipe distributor role and chef role, as well as recipe tables
 */
function taste_add_recipe_roles() {
	add_role( 'chef', __('Chef'), get_role( 'author' )->capabilities );
	add_role( 'recipe_distributor', __('Recipe Distributor'), get_role( 'author' )->capabilities );
}

function taste_add_recipes_table() {
	global $wpdb;
	$venue_table = $wpdb->prefix.'taste_venue';
	$user_table = $wpdb->prefix.'users';

	$sql = "
	CREATE TABLE IF NOT EXISTS `rg_recipes` (
		`id` CHAR(12) NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
		`root_id` CHAR(12) NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
		`virgin_id` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
		`author_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '2',
		`recipe_title` VARCHAR(80) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`request_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
		`recipe_type` ENUM('WO','Virgin','Generic') NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`recipe_status` ENUM('proposed','accepted','entered','printed','photographed','exported') NOT NULL DEFAULT 'accepted' COLLATE 'utf8mb4_0900_ai_ci',
		`source` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`image_url` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		PRIMARY KEY (`id`) USING BTREE,
		INDEX `root_id` (`root_id`) USING BTREE,
		INDEX `recipe_title` (`recipe_title`) USING BTREE,
		INDEX `recipe_type` (`recipe_type`) USING BTREE
	)
	COLLATE='utf8mb4_0900_ai_ci'
	ENGINE=MyISAM
	;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}

function taste_add_recipe_requests_table() {
	global $wpdb;

	$sql = "
	CREATE TABLE IF NOT EXISTS `rg_recipe_guru_requests_working` (
		`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		`cuisine` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`meal_type` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`classification` VARCHAR(120) NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
		`dietary` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`prep_time` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`equipment` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`recipe_count` TINYINT(3) UNSIGNED NOT NULL,
		`notes` VARCHAR(255) NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
		`month_year` DATE NOT NULL,
		PRIMARY KEY (`id`) USING BTREE,
		INDEX `classification` (`classification`) USING BTREE
	)
	COLLATE='utf8mb4_0900_ai_ci'
	ENGINE=MyISAM
	AUTO_INCREMENT=338
	;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}


function taste_venue_activation() {

	taste_add_recipe_roles();
	
	taste_add_recipes_table();

	taste_add_recipe_requests_table();
}
/**** END OF ACTIVATION CODE ****/

/**
 *  Remove venue role upon plugin de-activation
 */
function taste_remove_recipe_roles() {
	remove_role( 'chef' );
	remove_role( 'recipe_distributor' );
}

/**
 * DEACTIVATION CODE
 */
function taste_venue_deactivation() {
	taste_remove_recipe_roles();

}

/**** END OF DEACTIVATION CODE ****/