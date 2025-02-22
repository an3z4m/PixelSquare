<?php

    function save_business_card($image_data){
        $user_id = get_current_user_id();
        if(empty($user_id)) return;

        update_user_meta($user_id, 'image_data', $image_data);
        
        // File path to save the JSON data
        $jsonFilePath = ROOT_THEME_DIR.'/data.json';

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
        $dataArray[] = $image_data;

        // Save the updated data back to the JSON file
        return (file_put_contents($jsonFilePath, json_encode($dataArray, JSON_PRETTY_PRINT)));
    }   
?>