<?php

define('ROOT_THEME_DIR', get_template_directory());
define('ROOT_THEME_URL', get_template_directory_uri());


include_once 'wp-admin-profile-editor.php';

include_once 'controllers/profile-controller.php';

add_action( 'init', 'no_cache_headers' ); 
function no_cache_headers() {
  header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
  header("Pragma: no-cache"); // HTTP 1.0
  header("Expires: 0"); // Proxies
}


function getTwitterProfileImage($username) {

  $curl = curl_init();
  
  curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.x.com/2/users/by/username/realdonaldtrump?user.fields=profile_image_url",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
      "Authorization: Bearer ".TWITTER_BEARER_TOKEN
    ],
  ]);
  
    // Execute cURL and fetch the response
    $response = curl_exec($curl);

    // Check for errors
    if (curl_errno($curl)) {
        echo "cURL error: " . curl_error($curl);
        curl_close($curl);
        return null;
    }

    // Close cURL session
    curl_close($curl);

    // Decode the JSON response
    $data = json_decode($response, true);
  
    // Check if the profile image URL is present in the response
    if (isset($data['data']['profile_image_url'])) {
      return $data['data']['profile_image_url'];
  } else {
      echo "Error fetching profile image: " . json_encode($data);
      return null;
  }
}

// Replace with your Bearer Token from Twitter Developer Portal

define('TWITTER_BEARER_TOKEN' , 
"AAAAAAAAAAAAAAAAAAAAANP6yQEAAAAA2dr2mwxp1q286v%2FW6Azk75cekhw%3DgS0A60Ve3ogdEjJYHmuTcVLE4Oh1R0avRbFP93ks3yZmIm00LJ");




function convertImageToBase64($imageUrl)
{
    // Get the image content from the URL
    $imageData = file_get_contents($imageUrl);

    if ($imageData === false) {
        return 'Error: Unable to fetch the image.';
    }

    // Determine the MIME type of the image
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($imageData);

    // Ensure it's a valid image MIME type
    if (!str_starts_with($mimeType, 'image/')) {
        return 'Error: The provided URL does not point to an image.';
    }

    // Convert the image data to base64
    $base64 = base64_encode($imageData);

    // Return the base64-encoded image with the correct MIME type
    return "data:$mimeType;base64,$base64";
}


