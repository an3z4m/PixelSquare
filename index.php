<?php
// Get the requested path
$request_uri = $_SERVER['REQUEST_URI'];

// Render the content conditionally
if (strpos($request_uri, '/signup')) {
    include_once 'pages/create-profile.php'; exit;
}

// Render the content conditionally
if (strpos($request_uri, '/user-profile')) {
    include_once 'pages/user-profile.php'; exit;
}


// Default content (your existing pixel claiming page)

include_once 'pages/pixel-map.php';

?>
