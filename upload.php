<?php 
    $startX = $_GET['startX'];
    $startY = $_GET['startY'];
    $width = $_GET['width'] * 10;
    $height = $_GET['height'] * 10;
    // var_dump($_GET);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload, Crop, and Resize</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        #result {
            margin-top: 20px;
        }
        #croppedImage {
            display: none;
            max-width: 100%;
        }
        img {
            max-width: 100%;
        }
        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Upload, Crop, and Resize Image</h1>

    <form id="imageForm" enctype="multipart/form-data">
        <label for="image">Choose a <?php echo "$width x  $height px"; ?> image:</label>
        <input type="file" id="image" name="image" accept="image/png, image/jpeg" required>
        <br><br>
        <div>
            <img id="imagePreview" alt="Upload an image" style="display:none; max-width:100%;">
        </div>
        <br>
        <button type="button" id="cropButton" style="display:none;">Crop & Submit</button>
    </form>

    <div id="result">
        <h2>Resulting Image:</h2>
        <img id="outputImage" alt="Resulting image will appear here" src="background.png">
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
    let cropper;
    let croppedCanvas;
    const imageForm = document.getElementById('imageForm');
    const fileInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const cropButton = document.getElementById('cropButton');
    const outputImage = document.getElementById('outputImage');

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
                    // Validate image dimensions
                    // if (img.width !== 50 || img.height !== 50) {
                    //     alert('Image must be exactly 50x50px. Please upload a valid image.');
                    //     fileInput.value = ''; // Reset the input
                    //     return;
                    // }

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

            // Send the cropped image to the PHP script
            fetch('process-image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.blob())
            .then(blob => {
                const imageUrl = URL.createObjectURL(blob);
                outputImage.src = imageUrl;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }, 'image/png');
    });
</script>

</body>
</html>