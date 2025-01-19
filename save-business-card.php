<?php

        function save_business_card($row){
        // File path to save the JSON data
        $jsonFilePath = 'data.json';

        // Variables to save
        $startX = 100; // Example value
        $startY = 150; // Example value
        $width = 300;  // Example value
        $height = 200; // Example value

        // Check if the JSON file exists
        if (file_exists($jsonFilePath)) {
            // Read the existing data from the file
            $jsonData = file_get_contents($jsonFilePath);
            $dataArray = json_decode($jsonData, true);

            // If the file is empty or invalid, initialize as an empty array
            if (!is_array($dataArray)) {
                $dataArray = [];
            }
        } else {
            // If the file doesn't exist, initialize as an empty array
            $dataArray = [];
        }

        // Append the new row to the data array
        $dataArray[] = $row;

        // Save the updated data back to the JSON file
        return (file_put_contents($jsonFilePath, json_encode($dataArray, JSON_PRETTY_PRINT)));
    }   
?>