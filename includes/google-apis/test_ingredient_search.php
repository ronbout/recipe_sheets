<?php

defined('ABSPATH') or die('Direct script access disallowed.');

global $wpdb; 

$sql = "
	SELECT ingred.* 
	FROM tc_ingredients ingred
	ORDER BY ingred.name ASC
";

$ingreds = $wpdb->get_results($sql, ARRAY_A);

// echo "<pre>";
// print_r($ingreds);
// echo "</pre>";

$names = array_column($ingreds, 'name');

// echo "<pre>";
// print_r($names);
// echo "</pre>";

$result = binary_search('strong coffee', $names);

echo "result: ", $result;

die;
