<?php 

defined('ABSPATH') or die('Direct script access disallowed.');

/**
 *  ACTIVATION CODE 
 *  Add recipe distributor role and chef role, as well as recipe tables
 */
function recipe_add_recipe_roles() {
	add_role( 'chef', __('Chef'), get_role( 'author' )->capabilities );
	add_role( 'recipe_distributor', __('Recipe Distributor'), get_role( 'author' )->capabilities );
}

function recipe_add_recipe_distributors_table() {
	global $wpdb;

	$sql = "
	CREATE TABLE IF NOT EXISTS `tc_distributors` (
		`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		`Name` TINYTEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
		PRIMARY KEY (`id`) USING BTREE
	)
	COLLATE='utf8mb4_0900_ai_ci'
	ENGINE=MyISAM
	AUTO_INCREMENT=2
	;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}

function recipe_add_recipe_types_table() {
	global $wpdb;

	$sql = "
	CREATE TABLE IF NOT EXISTS `tc_recipe_types` (
		`id` CHAR(50) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`description` CHAR(120) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`next_recipe_id` BIGINT(19) NULL DEFAULT NULL,
		PRIMARY KEY (`id`) USING BTREE
	)
	COLLATE='utf8mb4_0900_ai_ci'
	ENGINE=MyISAM
	;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}

function recipe_add_ingredients_table() {
	global $wpdb;

	$sql = "
	CREATE TABLE IF NOT EXISTS `tc_ingredients` (
		`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(60) NOT NULL DEFAULT '0' COLLATE 'utf8mb4_0900_ai_ci',
		`normalized` VARCHAR(60) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`pluralized` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`depluralize` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`derivative` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`mince` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		PRIMARY KEY (`id`) USING BTREE,
		UNIQUE INDEX `name` (`name`) USING BTREE,
		INDEX `normalized` (`normalized`) USING BTREE
	)
	COLLATE='utf8mb4_0900_ai_ci'
	ENGINE=MyISAM
	AUTO_INCREMENT=2265
	;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}

function recipe_add_measure_units_table() {
	global $wpdb;

	$sql = "
	CREATE TABLE IF NOT EXISTS `tc_measure_units` (
		`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(60) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`normalized` VARCHAR(60) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`pluralized` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`depluralize` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`mark` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`derivative` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`cheese` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		PRIMARY KEY (`id`) USING BTREE,
		UNIQUE INDEX `name` (`name`) USING BTREE,
		INDEX `normalized` (`normalized`) USING BTREE
	)
	COLLATE='utf8mb4_0900_ai_ci'
	ENGINE=MyISAM
	;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}

function recipe_add_recipe_requests_table() {
	global $wpdb;

	$sql = "
	CREATE TABLE IF NOT EXISTS `tc_recipe_requests` (
		`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		`tier` ENUM('WO','Virgin') NOT NULL DEFAULT 'WO' COLLATE 'utf8mb4_0900_ai_ci',
		`distributor_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
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

function recipe_add_recipes_table() {
	global $wpdb;

	$sql = "
	CREATE TABLE IF NOT EXISTS  `tc_recipes` (
		`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		`recipe_title` VARCHAR(80) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`description` VARCHAR(500) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`servings` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`prep_time` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`cook_time` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`meal_type` CHAR(60) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`cuisine` CHAR(60) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`diet` CHAR(60) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`recipe_tip` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`worksheet_id` CHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8mb4_0900_ai_ci',
		`root_id` CHAR(12) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`client_id` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`author_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '1',
		`request_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
		`recipe_type` ENUM('WO','Catalog','Virgin') NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`recipe_status` ENUM('proposed','accepted','entered','printed','image','submitted') NOT NULL DEFAULT 'accepted' COLLATE 'utf8mb4_0900_ai_ci',
		`parent_recipe_id` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`orig_child_id` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`source` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`image_url` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`support_data_cnt` SMALLINT(5) UNSIGNED NULL DEFAULT NULL,
		`photo_date` DATE NULL DEFAULT NULL,
		`camera_id` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`submission_batch` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
		`submission_month_year` DATE NULL DEFAULT NULL,
		PRIMARY KEY (`id`) USING BTREE,
		INDEX `root_id` (`root_id`) USING BTREE,
		INDEX `recipe_title` (`recipe_title`) USING BTREE,
		INDEX `recipe_type` (`recipe_type`) USING BTREE,
		INDEX `worksheet_id` (`worksheet_id`) USING BTREE,
		INDEX `meal_type` (`meal_type`) USING BTREE,
		INDEX `cuisine` (`cuisine`) USING BTREE,
		INDEX `diet` (`diet`) USING BTREE,
		INDEX `submission_month_year` (`submission_month_year`)
	)
	COLLATE='utf8mb4_0900_ai_ci'
	ENGINE=MyISAM
	AUTO_INCREMENT=85517
	;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}

function recipe_add_recipe_ingredients_table() {
	global $wpdb;

	$sql = "
	CREATE TABLE IF NOT EXISTS `tc_recipe_ingredients` (
		`recipe_id` BIGINT(20) UNSIGNED NOT NULL,
		`ingred_cnt` TINYINT(3) UNSIGNED NOT NULL,
		`ingred_id` BIGINT(20) UNSIGNED NOT NULL,
		`measure` TINYTEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`unit` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
		`unit_plural` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
		`notes` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`plural` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
		`recipe_group` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		`ingred_tip` VARCHAR(500) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		PRIMARY KEY (`recipe_id`, `ingred_cnt`) USING BTREE,
		INDEX `ingred_id` (`ingred_id`) USING BTREE
	)
	COLLATE='utf8mb4_0900_ai_ci'
	ENGINE=MyISAM
	;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}

function recipe_add_recipe_instructions_table() {
	global $wpdb;

	$sql = "
	CREATE TABLE IF NOT EXISTS  `tc_recipe_instructions` (
		`recipe_id` BIGINT(20) UNSIGNED NOT NULL,
		`instruction_cnt` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
		`instruction` VARCHAR(800) NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
		`recipe_group` TINYTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
		PRIMARY KEY (`recipe_id`, `instruction_cnt`) USING BTREE
	)
	COLLATE='utf8mb4_0900_ai_ci'
	ENGINE=MyISAM
	;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}

function recipe_add_recipe_names_table() {
	global $wpdb;

	$sql = "
	CREATE TABLE IF NOT EXISTS  `tc_recipe_names` (
		`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		`recipe_id` BIGINT(20) UNSIGNED NOT NULL,
		`recipe_name` VARCHAR(80) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
		PRIMARY KEY (`id`) USING BTREE,
		UNIQUE INDEX `recipe_name` (`recipe_name`) USING BTREE,
		INDEX `recipe_id` (`recipe_id`) USING BTREE
	)
	COLLATE='utf8mb4_0900_ai_ci'
	ENGINE=MyISAM
	;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}

function recipe_add_ingredient_names_table() {
	global $wpdb;

	$sql = "
	CREATE TABLE IF NOT EXISTS  `tc_ingredient_names` (
		`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		`ingredient_id` BIGINT(20) UNSIGNED NOT NULL,
		`ingredient_name` VARCHAR(120) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
		PRIMARY KEY (`id`) USING BTREE,
		UNIQUE INDEX `ingredient_name` (`ingredient_name`) USING BTREE,
		INDEX `ingredient_id` (`ingredient_id`) USING BTREE
	)
	COLLATE='utf8mb4_0900_ai_ci'
	ENGINE=MyISAM
	;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}

/*
function recipe_add_recipe_names_trigger() {
	global $wpdb;

	$sql = "
		DROP TRIGGER if EXISTS tr_au_recipes;
		delimiter $$
		CREATE TRIGGER tr_au_recipes
		AFTER UPDATE ON tc_recipes FOR EACH ROW 
		BEGIN
			IF OLD.recipe_title <> NEW.recipe_title THEN
				INSERT INTO tc_recipe_names (recipe_id, recipe_name)
					SELECT * FROM 
						(SELECT NEW.id AS recipe_id, NEW.recipe_title AS recipe_name) AS new_value
					WHERE NOT EXISTS (
						SELECT id FROM tc_recipe_names WHERE recipe_name = NEW.recipe_title) LIMIT 1;
			
			END IF;
		END; $$
		delimiter ;
		DROP TRIGGER if EXISTS tr_ai_recipes;
		delimiter $$
		CREATE TRIGGER tr_ai_recipes
		AFTER INSERT ON tc_recipes FOR EACH ROW 
		BEGIN
			INSERT INTO tc_recipe_names (recipe_id, recipe_name)
				SELECT * FROM 
					(SELECT NEW.id AS recipe_id, NEW.recipe_title AS recipe_name) AS new_value
				WHERE NOT EXISTS (
					SELECT id FROM tc_recipe_names WHERE recipe_name = NEW.recipe_title) LIMIT 1;
			
		END; $$
		delimiter ;
	";
	mysqli_multi_query($wpdb->dbh, $sql);
}
*/

function recipe_insert_distributor() {
	global $wpdb;

	$sql = "
		INSERT INTO tc_distributors
		(id, name)
		VALUES (1, 'Recipe Guru')
	";

	$wpdb->query($sql);
}

function recipe_insert_types() {
	global $wpdb;

	$sql = "
		INSERT INTO tc_recipe_types
		(id, description)
		VALUES 
			('WO', 'Wholly Owned'),
			('Catalog', 'Catalog'),
			('Virgin', 'Virgin')
	";

	$wpdb->query($sql);
}

function recipe_sheets_activation() {

	recipe_add_recipe_roles();

	recipe_add_recipe_distributors_table();
	
	recipe_insert_distributor();

	recipe_add_recipe_types_table();

	recipe_insert_types();

	recipe_add_ingredients_table();

	recipe_add_measure_units_table();
	
	recipe_add_recipe_requests_table();
	
	recipe_add_recipes_table();

	recipe_add_recipe_ingredients_table();

	recipe_add_recipe_instructions_table();

	recipe_add_recipe_names_table();

	recipe_add_ingredient_names_table();

	// recipe_add_recipe_names_trigger();

}
/**** END OF ACTIVATION CODE ****/

/**
 *  Remove recipes roles upon plugin de-activation
 */
function recipe_remove_recipe_roles() {
	remove_role( 'chef' );
	remove_role( 'recipe_distributor' );
}

/**
 * DEACTIVATION CODE
 */
function recipe_sheets_deactivation() {
	recipe_remove_recipe_roles();

}

/**** END OF DEACTIVATION CODE ****/