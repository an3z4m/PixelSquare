<?php

// Check if an image was uploaded
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $uploadedImagePath = $_FILES['image']['tmp_name'];

    // Path to the high-resolution background image
    $inputFilePath = ROOT_THEME_DIR.'/background.webp';
    $outputFilePath = ROOT_THEME_DIR.'/background.webp';//'output.webp';

    // $inputFilePath = 'background-tmp.webp';
    // copy($outputFilePath, $inputFilePath);

    // Load the high-resolution background image
    $background = imagecreatefromwebp($inputFilePath);

    // Get dimensions of the background
    $backgroundWidth = imagesx($background);
    $backgroundHeight = imagesy($background);

    // Load the uploaded image
    $uploadedImage = imagecreatefrompng($uploadedImagePath); // Assuming input is PNG

    // Get dimensions of the uploaded image
    $uploadedImageWidth = imagesx($uploadedImage);
    $uploadedImageHeight = imagesy($uploadedImage);

    // Target size for the image in the background
    $targetWidth = $_POST['width']; // Width in the background
    $targetHeight = $_POST['height']; // Height in the background

    // Position in the background
    $xPos = $_POST['startX'];
    $yPos = $_POST['startY'];

    // Create a true color image for resizing
    $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);
    imagesavealpha($resizedImage, true); // Preserve alpha transparency
    $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
    imagefill($resizedImage, 0, 0, $transparent);

    // Resample the uploaded image into the smaller target size
    imagecopyresampled(
        $resizedImage,      // Destination image
        $uploadedImage,     // Source image
        0, 0,               // Destination x, y
        0, 0,               // Source x, y
        $targetWidth,       // Destination width
        $targetHeight,      // Destination height
        $uploadedImageWidth, // Source width
        $uploadedImageHeight // Source height
    );

    // Merge the resized image onto the background
    imagecopy(
        $background,
        $resizedImage,
        $xPos,
        $yPos,
        0, 0,
        $targetWidth,
        $targetHeight
    );

    header("Content-type: application/json; charset=utf-8");


    // Save the high-quality WebP output
     // Quality set to 90
    if(imagewebp($background, $outputFilePath, 90)){
        // Free up memory
        imagedestroy($background);
        imagedestroy($uploadedImage);
        imagedestroy($resizedImage);

        include_once ROOT_THEME_DIR.'/save-business-card.php';
        $zoom_factor = 10;
        // Create an associative array to represent the row
        $image_data = [
            'username'=> $_POST['username'] ,
            'startX' => $_POST['startX'] / $zoom_factor ,
            'startY' => $_POST['startY'] / $zoom_factor ,
            'width' => $_POST['width'] / $zoom_factor , // Target width in the background image
            'height' => $_POST['height'] / $zoom_factor , // Target height in the background image
        ];
        save_business_card($image_data);

        // $startX = $_POST['startX'] / $zoom_factor;
        // $startY = $_POST['startY'] / $zoom_factor;
        // $width  = $_POST['width'] / $zoom_factor; // Target width in the background image
        // $height = $_POST['height'] / $zoom_factor; // Target height in the background image

        // save_business_card($startX, $startY, $width, $height);

        echo json_encode(array('status'=>'200', 'message' => 'saved succesfully!'));
    }else{
        echo json_encode(array('status'=>'400', 'message' => 'error detected!'));
    }


    // // Set header to output image directly
    // header('Content-Type: image/webp');

    // // Output the final image (optional for debugging)
    // imagewebp($background);

    // // Free up memory
    // imagedestroy($background);
    // imagedestroy($uploadedImage);
    // imagedestroy($resizedImage);

} else {
    echo "Error uploading image.";
}
