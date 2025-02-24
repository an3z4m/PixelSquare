<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <title>Claim Your Pixels</title>
    <?php include_once ROOT_THEME_DIR.'/assets/styles.php'; ?>
</head>
<body>


<?php
// Check if the user is logged in
if (is_user_logged_in()) {
    // Get the logged-in user's info
    $current_user = wp_get_current_user();
    $username = $current_user->user_login;
    $profile_url = home_url('/user-profile'); // Profile page URL
    $logout_url = wp_logout_url(home_url()); // Logout URL
} else {
    $login_url = home_url('/signup');
}
?>

    <!-- <h1>Claim Your Pixels</h1> -->
    <div class="main">

        <ul class="menu">
            <?php if (is_user_logged_in()) : ?>
                <li><span class="username">Hello, <?php echo esc_html($username); ?>!</span></li>
                <li><a id="show-profile">Profile</a></li>
                <li><a href="<?php echo esc_url($logout_url); ?>" class="logout-link">Logout</a></li>
            <?php else : ?>
                <!-- <li><a href="<?php //echo esc_url($login_url); ?>" class="login-link">Sign In</a></li> -->
                <!-- <li><a id="auth-button" class="login-link">Sign In</a></li> -->
                <li>
            <a href="https://millionpixelsquare.com/wp-login.php?loginSocial=twitter" 
            data-plugin="nsl" data-action="connect" 
            data-redirect="current" data-provider="twitter" 
            data-popupwidth="600" data-popupheight="600">
                Sign in
            </a>
        </li>
            <?php endif; ?>
                <li><a id="mode-toggle">Switch to Editing Mode</a></li>
        </ul>

        <canvas id="grid-canvas"  width="1000" height="1000"></canvas>
        <div id="info">
            <p id="mode-info">Editing Mode: Select a rectangular area to claim.</p>
        </div>
    </div>
    <div id="bottom-bar">
        <p id="price-info"></p>
        <button id="upload-image">Upload Image</button>
    </div>

    


    <div class="popup-overlay" id="business-info-popup">
        <div class="popup-content">
            <div class="twitter-image">
            </div>            
            <div class="profile-links">
                <!-- <p id="business-email">Email: example@example.com</p> -->
                <a href="" target="_blank" id="business-twitter"></a>
            </div>
            <button class="close-popup">X</button>
        </div>
    </div>

    <div class="popup-overlay wide" id="image-upload-popup">
        <div class="popup-content">
            <iframe src=""></iframe>
            <button class="close-popup">X</button>
        </div>
    </div>

    <div class="popup-overlay wide" id="show-profile-popup">
        <div class="popup-content">
            <iframe src=""></iframe>
            <button class="close-popup">X</button>
        </div>
    </div>


<div class="mode-container viewing" id="modeContainer">
    <div class="mode-switcher" id="switcher">
        <span class="mode-text viewing"><span class="icon">&#128065;</span></span>
      <span class="mode-text editing"><span class="icon">&#128394;</span></span>
      <div class="switcher-button"></div>
    </div>
  </div>


  <?php include_once ROOT_THEME_DIR.'/assets/scripts.php'; ?>
  
<script>
    window.addEventListener('message', function(event) {
      // Validate the message
      if (event.data === 'reloadParentPage') {
        window.location.reload();
      }
    });
  </script>

  <script>
    const modeContainer = document.getElementById('modeContainer');
    const switcher = document.getElementById('switcher');

    switcher.addEventListener('click', () => {
      if (modeContainer.classList.contains('viewing')) {
        modeContainer.classList.remove('viewing');
        modeContainer.classList.add('editing');
      } else {
        modeContainer.classList.remove('editing');
        modeContainer.classList.add('viewing');
      }
      toggleMode();
    });
  </script>

