<?php

// Load the Google API PHP Client Library.
require_once __DIR__ . '/vendor/autoload.php';

$sheets = initializeSheets();
getReport($sheets);


/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return An authorized Analytics Reporting API V4 service object.
 */
function initializeSheets()
{

  // Use the developers console and download your service account
  // credentials in JSON format. Place them in this directory or
  // change the key file location if necessary.
  $KEY_FILE_LOCATION = __DIR__ . '/credentials.json';

  // Create and configure a new client object.
  $client = new Google_Client();
  $client->setApplicationName("Google Sheets");
  $client->setAuthConfig($KEY_FILE_LOCATION);
  $client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);
  $sheets = new Google\Service\Sheets($client);

  return $sheets;
}


function getReport($sheets) {
	try{

    $spreadsheetId = '1XNONqFyWBN5qX-1fSt8zMZ7TMVkEsgPDb_H1OL6fc5Q';
    $range = 'Recipe List!B2:O40';
    $renderOption = array('valueRenderOption' => 'FORMULA');
//     $response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
//     $values = $response->getValues();
//     echo "<pre>";
// print_r($values);
// echo "</pre>";
    $response = $sheets->spreadsheets_values->get($spreadsheetId, 'Recipe Entry!E4766:E4767', $renderOption);
    $values = $response->getValues();
    echo "<pre>";
print_r($values);
echo "</pre>";
die;
    if (empty($values)) {
        echo  "No data found.\n";
    } else {
        
        foreach ($values as $row) {
            // Print columns A and E, which correspond to indices 0 and 4.
            // printf("%s, %s\n", $row[0], $row[4]);
            echo  "<p>", $row[0], $row[4], "</p>";
        }
    }
}
catch(Exception $e) {
    // TODO(developer) - handle error appropriately
    echo 'Message: ' .$e->getMessage();
}
}
