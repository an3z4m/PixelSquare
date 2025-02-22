<?php 
    // Get the user ID from the query string
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : get_current_user_id();
    if (!$user_id || !get_userdata($user_id)) {
        wp_die('User not found or invalid user ID. You need to create an account first!');
    }

    // $image_data = get_user_meta($user_id, 'image_data', true);
    // if(!empty($image_data)){
    //     wp_die('You already have an image data:'.json_encode($image_data));
    // } 

    $user = get_user($user_id);
    $twitter_username = $user->user_login;

    // Fetch the profile image URL
    $profile_image_src = 'https://pbs.twimg.com/profile_images/874276197357596672/kUuht00m_normal.jpg';

    $profile_image_src = "https://unavatar.io/twitter/$twitter_username";

    $base64Image = convertImageToBase64($profile_image_src);

    //getTwitterProfileImage($twitter_username);

    $zoom_factor = 10;

    $startX = $_GET['startX'] * $zoom_factor;
    $startY = $_GET['startY'] * $zoom_factor;
    $width = $_GET['width'] * $zoom_factor;
    $height = $_GET['height'] * $zoom_factor;
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload, Crop, and Resize</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }
        .container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 90%;
            width: 400px;
            padding: 20px;
            box-sizing: border-box;
            text-align: center;
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        label {
            font-size: 0.9rem;
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
        }

        input[type="file"] {
            font-size: 0.9rem;
            margin: 10px 0;
        }

        img {
            max-width: 100%;
            border-radius: 10px;
            display: none;
        }

        button {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            color: #fff;
            background: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        .progress-container {
            display: none;
            width: 100%;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 20px;
        }

        .progress-bar {
            height: 10px;
            width: 0;
            background-color: #28a745;
            transition: width 0.4s ease;
        }

        .status {
            margin-top: 10px;
            font-size: 0.85rem;
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.2rem;
            }
            button {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>


    <div class="container">
        <h1>Upload, Crop, and Resize Image</h1>

        <form id="imageForm" enctype="multipart/form-data">
            <label for="image">Choose a <?php echo "$width x $height px"; ?> image:</label>
            <input type="file" id="image" name="image" accept="image/png, image/jpeg">
            
            <!-- Set default src to the profile image -->
            <img id="imagePreview" src="<?php echo $base64Image; ?>" alt="Profile image">
            
            <button type="button" id="cropButton" style="display: inline-block;">Crop & Submit</button>
        </form>

        <div class="progress-container">
            <div class="progress-bar" id="progressBar"></div>
        </div>
        <p class="status" id="status"></p>
    </div>

    <script>
        let cropper;
        const imageForm = document.getElementById('imageForm');
        const fileInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const cropButton = document.getElementById('cropButton');
        const progressBar = document.getElementById('progressBar');
        const status = document.getElementById('status');

        const width = '<?php echo $width; ?>';
        const height = '<?php echo $height; ?>';

        // Initialize Cropper.js on the default profile image
        window.addEventListener('load', () => {
            cropper = new Cropper(imagePreview, {
                aspectRatio: width / height,
                viewMode: 1,
                autoCropArea: 1,
            });
        });

        // Update Cropper.js when a new image is uploaded
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;

                    // Reinitialize Cropper.js with the new image
                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper(imagePreview, {
                        aspectRatio: width / height,
                        viewMode: 1,
                        autoCropArea: 1,
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        cropButton.addEventListener('click', function () {
            const croppedCanvas = cropper.getCroppedCanvas({
                width: width,
                height: height,
            });

            croppedCanvas.toBlob(function (blob) {
                const formData = new FormData();
                formData.append('image', blob, 'cropped_image.png');
                formData.append('startX', <?php echo $startX; ?>);
                formData.append('startY', <?php echo $startY; ?>);
                formData.append('width', <?php echo $width; ?>);
                formData.append('height', <?php echo $height; ?>);
                formData.append('username', '<?php echo $twitter_username; ?>');

                

                // Handle the AJAX request (unchanged)
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo site_url("process-image"); ?>', true);
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        status.textContent = 'Upload complete!';
                        window.setTimeout(() => {
                            window.parent.postMessage('reloadParentPage', '*');
                        }, 2000);
                    } else {
                        status.textContent = 'Upload failed. Please try again.';
                    }
                };
                xhr.send(formData);
            });
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
</body>
</html>