<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=3, user-scalable=yes"> -->

    <title>Claim Your Pixels</title>
    <?php include_once ROOT_THEME_DIR.'/assets/styles.php'; ?>

    <style>
        body {
            touch-action: none;
            overflow: hidden;
        }
        canvas {
            touch-action: auto;
        }
    </style>

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
                
<a href="https://millionpixelsquare.com/wp-login.php?loginSocial=twitter" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="twitter" data-popupwidth="600" data-popupheight="600">
	Sign in
</a>
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

    <?php if (!is_user_logged_in()) : ?>
        <div class="popup-overlay" id="auth-form">
            <div class="popup-content">
                <!-- <iframe src="<?php //echo $login_url; ?>"></iframe> -->
                <?php include_once ROOT_THEME_DIR.'/signup.php'; ?>
                <button class="close-popup">X</button>
            </div>
        </div>
    <?php endif; ?>


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


    <script>
        // const canvas = document.getElementById('grid-canvas');
        // const ctx = canvas.getContext('2d');
        var squareSize = 10;
        // Load the background image
        const backgroundSrc = '<?php echo ROOT_THEME_URL; ?>/background.webp?nocache=<?php echo time(); ?>';
        <?php include_once 'custom-canvas.js'; ?>
    </script>


    <script>
        let authButton = document.querySelector('#auth-button');
        if(authButton) authButton.addEventListener('click', ()=>{
            document.querySelector('#auth-form').style.display = 'block';  
        });


        // Path to the JSON file
const jsonFilePath = "<?php echo ROOT_THEME_URL.'/data.json?nocache='.time(); ?>";

var reservedAreas;
// Function to load and process the JSON file
async function loadJsonData() {
    try {
        // Fetch the JSON file
        const response = await fetch(jsonFilePath);

        // Check if the fetch was successful
        if (!response.ok) {
            console.log(`HTTP error! Status: ${response.status}`);
            return [];
        }

        // Parse the JSON data
        const data = await response.json();

        // Process or display the data
        console.log("Loaded Data:", data);
        reservedAreas = data;

        // Example: Iterating through rows
        data.forEach((row, index) => {
            console.log(`Row ${index + 1}:`, row);
        });
        reservedAreas = data;
        //drawReservedAreas();


    } catch (error) {
        console.error("Error loading JSON file:", error);
        reservedAreas = [];
    }
}

loadJsonData();

console.log("reservedAreas:"+reservedAreas);
if(!reservedAreas || reservedAreas.length == undefined) reservedAreas = [];

        const modeToggleBtn = document.getElementById('mode-toggle');
        const modeInfo = document.getElementById('mode-info');
        let isEditingMode = false;


function drawReservedAreas() {
    return; 
    reservedAreas.forEach(area => {
        // Adjust for zooming and panning
        const adjustedX = (area.startX - offsetX) / scale;
        const adjustedY = (area.startY - offsetY) / scale;
        const adjustedWidth = area.width / scale;
        const adjustedHeight = area.height / scale;

        // Clear the area before drawing
        ctx.clearRect(adjustedX, adjustedY, adjustedWidth, adjustedHeight);

        // Draw the reserved area rectangle
        ctx.strokeStyle = 'rgba(255,0, 0, 0.5)';
        ctx.lineWidth = 0.5;
        ctx.strokeRect(adjustedX, adjustedY, adjustedWidth, adjustedHeight);
    });
}

function isOverReservedArea(x, y) {
    // Adjust the click position according to the current scale and offset
    const adjustedX = (x - offsetX) / scale;
    const adjustedY = (y - offsetY) / scale;

    return reservedAreas.find(area =>
        adjustedX >= area.startX && adjustedX < area.startX + area.width &&
        adjustedY >= area.startY && adjustedY < area.startY + area.height
    );
}

    var selectedX = -1;
    var selectedY = -1;

    function handleCanvasClick(event) {
    const rect = canvas.getBoundingClientRect();

    // Calculate scaling factors
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;

    // Adjust coordinates based on scaling factors
    selectedX = Math.floor(((event.clientX - rect.left) * scaleX) / squareSize) * squareSize;
    selectedY = Math.floor(((event.clientY - rect.top) * scaleY) / squareSize) * squareSize;

    if (isEditingMode) {
        if (!isOverReservedArea(selectedX, selectedY)) {
            drawGrid();
            ctx.fillStyle = 'rgba(255, 0, 0, 0.5)';
            ctx.fillRect(selectedX, selectedY, squareSize, squareSize);
            document.getElementById('bottom-bar').style.display = 'block';

        } else {
            alert('Cannot edit reserved area.');
        }
    } else {
        const area = isOverReservedArea(selectedX, selectedY);
        if (area) {
            showBusinessCard(area);
        }
    }
}

        function toggleMode() {
            isEditingMode = !isEditingMode;
            drawGrid();
            if(isEditingMode){ canvas.classList.add('editing'); }else{ canvas.classList.remove('editing');}

            modeToggleBtn.textContent = isEditingMode ? 'Switch to Viewing Mode' : 'Switch to Editing Mode';
            modeInfo.textContent = isEditingMode ? 'Editing Mode: Select a rectangular area to claim.' : 'Viewing Mode: Click on reserved areas for details.';
        }

        document.querySelectorAll('.close-popup').forEach((button)=>button.addEventListener('click', () => {
            button.closest('.popup-overlay').style.display = 'none';
        }));

        document.getElementById('upload-image').addEventListener('click', () => {
            // Show upload popup
            const iframe = document.querySelector('#image-upload-popup iframe');
            iframe.src = `<?php echo site_url('upload'); ?>?startX=${selectedX}&startY=${selectedY}&width=${squareSize}&height=${squareSize}`;
        
            document.querySelector('#image-upload-popup').style.display = 'block';
        });
        
        showProfileButton = document.getElementById('show-profile');
        if (showProfileButton) showProfileButton.addEventListener('click', () => {
            // Show upload popup
            const iframe = document.querySelector('#show-profile-popup iframe');
            iframe.src = `<?php echo $profile_url; ?>`;
        
            document.querySelector('#show-profile-popup').style.display = 'block';
        });
        
        canvas.addEventListener('click', handleCanvasClick);
        modeToggleBtn.addEventListener('click', toggleMode);


        
        function showBusinessCard(area) {
            document.querySelector('#business-info-popup').style.display = 'block';
            document.getElementById('business-twitter').textContent = `@${area.username}`;
            document.getElementById('business-twitter').href = `https://x.com/${area.username}`;

            twitter_image = document.createElement('img');
            twitter_image.src = `https://unavatar.io/twitter/${area.username}`;
            
            twitterImageContainer = document.querySelector('#business-info-popup .twitter-image');
            twitterImageContainer.innerHTML = '';
            twitterImageContainer.appendChild(twitter_image);

        }
        drawGrid();
    </script>

<script>
    window.addEventListener('message', function(event) {
      // Validate the message
      if (event.data === 'reloadParentPage') {
        window.location.reload();
      }
    });
  </script>

<div class="mode-container viewing" id="modeContainer">
    <div class="mode-switcher" id="switcher">
        <span class="mode-text viewing"><span class="icon">&#128065;</span></span>
      <span class="mode-text editing"><span class="icon">&#128394;</span></span>
      <div class="switcher-button"></div>
    </div>
  </div>

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

