<?php

function create_user_profile_ajax() {
    // Check if all necessary fields are present
    if (!isset($_POST['email'], $_POST['password']) || empty($_POST['email']) || empty($_POST['password'])) {
        wp_send_json_error('Email and password are required.');
    }

    $email = sanitize_email($_POST['email']);
    $password = sanitize_text_field($_POST['password']);
    $website = isset($_POST['website']) ? esc_url_raw($_POST['website']) : '';
    $twitter = isset($_POST['twitter']) ? esc_url_raw($_POST['twitter']) : '';
    $linkedin = isset($_POST['linkedin']) ? esc_url_raw($_POST['linkedin']) : '';
    $instagram = isset($_POST['instagram']) ? esc_url_raw($_POST['instagram']) : '';

    // Check if the email already exists
    if (email_exists($email)) {
        wp_send_json_error('Email already exists.');
    }

    // Create the user
    $user_id = wp_insert_user([
        'user_login' => $email,
        'user_email' => $email,
        'user_pass'  => $password,
    ]);

    if (is_wp_error($user_id)) {
        wp_send_json_error($user_id->get_error_message());
    }

    // Save additional meta data
    if ($website) {
        update_user_meta($user_id, 'website', $website);
    }
    if ($twitter) {
        update_user_meta($user_id, 'twitter', $twitter);
    }
    if ($linkedin) {
        update_user_meta($user_id, 'linkedin', $linkedin);
    }
    if ($instagram) {
        update_user_meta($user_id, 'instagram', $instagram);
    }

    // Return success with user_id
    wp_send_json_success(['user_id' => $user_id]);
}

add_action('wp_ajax_create_user_profile', 'create_user_profile_ajax'); // For logged-in users
add_action('wp_ajax_nopriv_create_user_profile', 'create_user_profile_ajax'); // For guests
