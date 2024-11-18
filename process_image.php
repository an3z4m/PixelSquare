<?php



// Check if an image was uploaded
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

    include 'save-business-card.php';

    // Create an associative array to represent the row
    $row = [
        'startX' => $_POST['startX'],
        'startY' => $_POST['startY'],
        'width' => $_POST['width'],
        'height' => $_POST['height'],
    ];

    save_business_card($row);

    $uploadedImagePath = $_FILES['image']['tmp_name'];

    // $input_file_path  = 'background-empty.png';
    $output_file_path = 'background.png';

    // Load the background image (1000x1000px)
    $background = imagecreatefrompng($output_file_path);

    // Get dimensions of the background
    $backgroundWidth = imagesx($background);
    $backgroundHeight = imagesy($background);

    // Load the uploaded small image (50x50px)
    $smallImage = imagecreatefrompng($uploadedImagePath);

    // Get dimensions of the small image
    $smallImageWidth = imagesx($smallImage);
    $smallImageHeight = imagesy($smallImage);

    // Calculate the position to center the small image
    $xPos = $_POST['startX']; //($backgroundWidth - $smallImageWidth) / 2;
    $yPos = $_POST['startY']; //($backgroundHeight - $smallImageHeight) / 2;

    // $xPos = rand(0,1000 - $smallImageWidth); //($backgroundWidth - $smallImageWidth) / 2;
    // $yPos = rand(0,1000 - $smallImageHeight); //($backgroundHeight - $smallImageHeight) / 2;

    // Merge the images
    imagecopy($background, $smallImage, $xPos, $yPos, 0, 0, $smallImageWidth, $smallImageHeight);

    // Set header to output image directly
    header('Content-Type: image/png');

    // Output the final image
    imagepng($background, $output_file_path);

    imagepng($background);
    // imagepng($smallImage);

    // Free up memory
    imagedestroy($background);
    imagedestroy($smallImage);

} else {
    echo "Error uploading image.";
}
?>