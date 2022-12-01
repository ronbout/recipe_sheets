<?php

defined('ABSPATH') or die('Direct script access disallowed.');

// Load the Google API PHP Client Library.
require_once __DIR__ . '/vendor/autoload.php';

function get_working_images_dir_info($recipe_type='WO', $folder_id=null, $month=null) {
	$drive = initializeImageDrive();
	if (!$folder_id) {
		$folder_id = 'WO' === $recipe_type ? IMAGE_FOLDER_ID : VIRGIN_FOLDER_ID;
	}
	return get_image_dir_files($drive, $folder_id, $month);
}

/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return An authorized Analytics Reporting API V4 service object.
 */
function initializeImageDrive()
{

  // Use the developers console and download your service account
  // credentials in JSON format. Place them in this directory or
  // change the key file location if necessary.
  $KEY_FILE_LOCATION = __DIR__ . '/credentials.json';

  // Create and configure a new client object.
  $client = new Google_Client();
  $client->setApplicationName("Google Drive");
  $client->setAuthConfig($KEY_FILE_LOCATION);
  $client->setScopes(['https://www.googleapis.com/auth/drive']);
	// $client->addScope(Drive::DRIVE);
  $drive = new Google\Service\Drive($client);

  return $drive;
}


function get_image_dir_files($drive, $folder_id, $month) {

	try {
		$files = array();

		do {
				$response = $drive->files->listFiles(array(
					'q' => "mimeType='image/jpeg' and '$folder_id' in parents",
					'spaces' => 'drive',
					'includeItemsFromAllDrives' => true,
					'supportsAllDrives' => true,
					'orderBy' => 'name',
					'pageToken' => $pageToken,
					'fields' => 'nextPageToken, files(id, name, description)',
				));

				foreach ($response->files as $file) {
						// printf("Found file: %s (%s)\n", $file->name, $file->id);
						// echo "<p>name: $file->name - id: $file->id => $file->description</p>";
						$image_url = "https://drive.google.com/file/d/$file->id/view?usp=drivesdk";
						$recipe_identifier = $file->description;
						if (!trim($recipe_identifier)) {
							$files[] = array( 
								'name' => $file->name,
								'worksheet_id' => '',
								'support_cnt' => '',
								'image_url' => $image_url,
							);
						}
						$recipe_info = get_image_recipe_info($recipe_identifier, $month);

						$files[] = array( 
							'name' => $file->name,
							'worksheet_id' => $recipe_info['worksheet_id'],
							'support_cnt' => $recipe_info['support_cnt'],
							'image_url' => $image_url,
						);
				}
				// array_push($files, $response->files);

				$pageToken = $response->nextPageToken;
		} while ($pageToken != null);

		return $files;
	}
	catch(Exception $e) {
			// TODO(developer) - handle error appropriately
			echo '<pre>';
			echo 'Message: ' .$e->getMessage();
			echo '</pre>';
	}
}

function get_image_recipe_info($recipe_identifier, $month) {
	global $wpdb;

	$parms = array($recipe_identifier);

	if (id_is_worksheet_id($recipe_identifier)) {
		$sql = "
			SELECT id, worksheet_id, support_data_cnt
			FROM tc_recipes
			WHERE worksheet_id = %s OR orig_child_id = %s
		";
		$parms[] = $recipe_identifier;
	} else {
		if ($recipe_identifier < 138) {
			$srch_month = $month;
		} else {
			$srch_month = '2022-06-01';
		}
		$sql = "
			SELECT rec.id, rec.worksheet_id, rec.support_data_cnt
			FROM tc_recipes rec
			LEFT JOIN tc_recipe_requests req1 ON req1.id = rec.request_id
			LEFT JOIN tc_recipes parent ON parent.worksheet_id = rec.parent_recipe_id
			LEFT JOIN tc_recipe_requests req2 ON req2.id = parent.request_id
			WHERE rec.support_data_cnt = %d 
			AND COALESCE(req2.month_year, req1.month_year) = %s
		";
		$parms[] = $srch_month;
	}

	$sql = $wpdb->prepare($sql, $parms);
	$recipe_row = $wpdb->get_row($sql, ARRAY_A);

	if (!$recipe_row) {
		return array( 
			'recipe_id' => 'N/A',
			'worksheet_id' => 'N/A',
			'support_cnt' => 'N/A'
		);
	} else {
		return array( 
			'recipe_id' => $recipe_row['id'],
			'worksheet_id' => $recipe_row['worksheet_id'],
			'support_cnt' => $recipe_row['support_data_cnt'] ? $recipe_row['support_data_cnt'] : 'N/A',
		);
	}
}

function id_is_worksheet_id($id) {
	return intval($id) > 300000;
}