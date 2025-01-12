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