<?php 
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
            <input type="file" id="image" name="image" accept="image/png, image/jpeg" required>
            
            <img id="imagePreview" alt="Upload an image">
            
            <button type="button" id="cropButton" style="display: none;">Crop & Submit</button>
        </form>

        <div class="progress-container">
            <div class="progress-bar" id="progressBar"></div>
        </div>
        <p class="status" id="status"></p>
    </div>

    <script>
        // JavaScript to handle file input and crop preview
        document.getElementById('image').addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const imagePreview = document.getElementById('imagePreview');
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    document.getElementById('cropButton').style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
    let cropper;
    let croppedCanvas;
    const imageForm = document.getElementById('imageForm');
    const fileInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const cropButton = document.getElementById('cropButton');
    const outputImage = document.getElementById('outputImage');
    const progressBar = document.getElementById('progressBar');
    const status = document.getElementById('status');

    const width   =  '<?php echo $width; ?>';
    const height  =  '<?php echo $height; ?>';
    const startX  =  '<?php echo $startX; ?>';
    const startY  =  '<?php echo $startY; ?>';

    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    // Set preview and initialize Cropper.js
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';

                    if (cropper) {
                        cropper.destroy(); // Destroy existing cropper if any
                    }
                    cropper = new Cropper(imagePreview, {
                        aspectRatio: width / height, // Force specific crop ratio (2:1 here)
                        viewMode: 1,
                        autoCropArea: 1, // Ensures the crop box fills the image initially
                        dragMode: 'move', // Allow moving the image while cropping
                        cropBoxResizable: false, // Disable resizing the crop box
                        cropBoxMovable: false, // Fix the crop box in place
                    });
                    cropButton.style.display = 'inline-block';
                };
                img.src = e.target.result; // Load the image for dimension validation
            };
            reader.readAsDataURL(file);
        }
    });

    cropButton.addEventListener('click', function() {
        croppedCanvas = cropper.getCroppedCanvas({
            width: width, // Fixed width for the cropped image
            height: height, // Fixed height for the cropped image
        });

        croppedCanvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('image', blob, 'cropped_image.png');
            formData.append('startX', startX);
            formData.append('startY', startY);
            formData.append('width', width);
            formData.append('height', height);

            // Reset progress bar
            progressBar.style.width = '0';
            status.textContent = '';

            // Send the cropped image to the PHP script
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'process-image.php', true);
            
            document.querySelector('.progress-container').style.display = 'block';

            xhr.upload.onprogress = (event) => {
                if (event.lengthComputable) {
                    const percentComplete = (event.loaded / event.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                    status.textContent = `Uploading... ${Math.round(percentComplete-1)}%`;
                }
            };

            xhr.onload = () => {
                if (xhr.status === 200) {
                    progressBar.style.width = '100%';
                    status.textContent = 'Upload complete!, you will be redirected to the main page';
                    window.setTimeout(() => {
                        // window.location.reload();
                        window.parent.postMessage('reloadParentPage', '*');
                    }, 2000);
                } else {
                    status.textContent = 'Upload failed. Please try again.';
                }
            };

            xhr.onerror = () => {
                status.textContent = 'An error occurred during the upload.';
            };

            xhr.send(formData);
        }, 'image/png');
    });
</script>

</body>
</html>