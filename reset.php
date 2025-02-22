<?php

// Destination file
$sourceFile = ROOT_THEME_DIR.'/background-empty.webp';
$destinationFile = ROOT_THEME_DIR.'/background.webp';

// Check if the source file exists
if (file_exists($sourceFile)) {
    // Copy the file
    if (copy($sourceFile, $destinationFile)) {
        echo "File copied successfully to $destinationFile";
    } else {
        echo "Failed to copy file.";
    }
} else {
    echo "Source file does not exist.";
}


// Destination file
$sourceFile = ROOT_THEME_DIR.'/data-empty.json';
$destinationFile = ROOT_THEME_DIR.'/data.json';

// Check if the source file exists
if (file_exists($sourceFile)) {
    // Copy the file
    if (copy($sourceFile, $destinationFile)) {
        echo "File copied successfully to $destinationFile";
    } else {
        echo "Failed to copy file.";
    }
} else {
    echo "Source file does not exist.";
}
