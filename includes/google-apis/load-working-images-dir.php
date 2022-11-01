<?php

defined('ABSPATH') or die('Direct script access disallowed.');

// Load the Google API PHP Client Library.
require_once __DIR__ . '/vendor/autoload.php';

function get_working_images_dir_info($recipe_type='WO') {
	$drive = initializeImageDrive();
	$folder_id = 'WO' === $recipe_type ? IMAGE_FOLDER_ID : VIRGIN_FOLDER_ID;
	return get_image_dir_files($drive, $folder_id);
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


function get_image_dir_files($drive, $folder_id) {

	try{
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
						$files[] = array( 
							'id' => $file->id,
							'name' => $file->name,
							'worksheet_id' => $file->description
						);
				}
				// array_push($files, $response->files);

				$pageToken = $response->nextPageToken;
		} while ($pageToken != null);

	return $files;
}
catch(Exception $e) {
    // TODO(developer) - handle error appropriately
    echo 'Message: ' .$e->getMessage();
}
}
