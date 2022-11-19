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
						$recipe_info = get_image_recipe_info($recipe_identifier, $month);

						$files[] = array( 
							'name' => $file->name,
						//	'recipe_id' => $recipe_info['recipe_id'],
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

	if (id_is_worksheet_id($recipe_identifier)) {
		$sql = "
			SELECT id, worksheet_id, support_data_cnt
			FROM tc_recipes
			WHERE worksheet_id = %s
		";
	} else {
		/****
		 * 
		 * right now, the only support data cnt records are for June,
		 * which is what we are processing.  Support data is only unqiue per month.
		 * 
		 * *** TODO:  use the parent id or request id to get the worksheet month
		 * which will need to be passed into here
		 */
		$sql = "
			SELECT id, worksheet_id, support_data_cnt
			FROM tc_recipes
			WHERE support_data_cnt = %d LIMIT 1
		";
	}

	$sql = $wpdb->prepare($sql, $recipe_identifier);
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