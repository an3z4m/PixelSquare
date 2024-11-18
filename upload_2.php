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
    </style>
</head>
<body>

<div class="container">
    <h1>Upload, Crop, and Resize Image</h1>

    <form id="imageForm" enctype="multipart/form-data">
        <label for="image">Choose a 50x50px image:</label>
        <input type="file" id="image" name="image" accept="image/png" required>
        <br><br>
        <div>
            <img id="imagePreview" alt="Upload an image" style="display:none; max-width:100%;">
        </div>
        <br>
        <button type="button" id="cropButton" style="display:none;">Crop & Submit</button>
    </form>

    <div id="result">
        <h2>Resulting Image:</h2>
        <img id="outputImage" alt="Resulting image will appear here">
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

    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';

                // Initialize cropper on the image
                if (cropper) {
                    cropper.destroy(); // Destroy existing cropper if any
                }
                cropper = new Cropper(imagePreview, {
                    // aspectRatio: 1, // 1:1 for square cropping
                    viewMode: 1,
                    minContainerWidth: 400,
                    minContainerHeight: 100,
                });
                cropButton.style.display = 'inline-block';
            };
            reader.readAsDataURL(file);
        }
    });

    cropButton.addEventListener('click', function() {
        // Get the cropped canvas
        croppedCanvas = cropper.getCroppedCanvas({
            width: 200,
            height: 100,
        });

        // Convert the canvas to a Blob (image format) and submit it to the server
        croppedCanvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('image', blob, 'cropped_image.png');

            // Send the cropped image to the PHP script
            fetch('process_image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.blob())
            .then(blob => {
                // Display the resulting image
                const imageUrl = URL.createObjectURL(blob);
                document.getElementById('outputImage').src = imageUrl;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }, 'image/png');
    });
</script>

</body>
</html>