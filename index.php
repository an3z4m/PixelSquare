<?php
// Get the requested path
$request_uri = $_SERVER['REQUEST_URI'];

header('HTTP/1.1 200 OK');

// Render the content conditionally
if (strpos($request_uri, '/upload') !== false) {
    include_once 'upload.php'; exit;
}

// Render the content conditionally
if (strpos($request_uri, '/process-image') !== false) {
    include_once 'process-image.php'; exit;
}


// Reset the background and data.json
if (strpos($request_uri, '/reset') !== false) {
    include_once 'reset.php'; exit;
}

// Render the content conditionally
if (strpos($request_uri, '/signup') !== false) {
    include_once 'signup.php'; exit;
}

// Render the content conditionally
if (strpos($request_uri, '/user-profile') !== false) {
    include_once 'user-profile.php'; exit;
}

// Default content (your existing pixel claiming page)

include_once 'pixel-map.php';

?>