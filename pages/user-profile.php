<?php
// Template Name: Minimal Linktree

// Get the user ID from the query string
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if (!$user_id || !get_userdata($user_id)) {
    wp_die('User not found or invalid user ID.');
}

// Fetch user meta
$email = get_the_author_meta('user_email', $user_id);
$website = get_user_meta($user_id, 'website', true);
$twitter = get_user_meta($user_id, 'twitter', true);
$linkedin = get_user_meta($user_id, 'linkedin', true);
$instagram = get_user_meta($user_id, 'instagram', true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linktree Profile</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
            margin: 0;
        }
        .profile-container {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .profile-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .profile-links a {
            display: block;
            margin: 10px 0;
            padding: 10px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .profile-links a:hover {
            background-color: #0056b3;
        }
        .twitter-image {
            margin: 20px auto;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
        }
        .twitter-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .email {
            margin-top: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>User Profile</h1>

        <?php if ($twitter): ?>
            <?php 
            // Extract Twitter username
            $twitter_username = '';
            if (preg_match('/twitter\.com\/([^\/]+)/', $twitter, $matches)) {
                $twitter_username = $matches[1];
            }
            ?>
            <?php if ($twitter_username): ?>
                <div class="twitter-image">
                    <img src="https://unavatar.io/twitter/<?php echo esc_attr($twitter_username); ?>" alt="Twitter Profile Image">
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="profile-links">
            <?php if ($website): ?>
                <a href="<?php echo esc_url($website); ?>" target="_blank">Website</a>
            <?php endif; ?>
            <?php if ($twitter): ?>
                <a href="<?php echo esc_url($twitter); ?>" target="_blank">Twitter</a>
            <?php endif; ?>
            <?php if ($linkedin): ?>
                <a href="<?php echo esc_url($linkedin); ?>" target="_blank">LinkedIn</a>
            <?php endif; ?>
            <?php if ($instagram): ?>
                <a href="<?php echo esc_url($instagram); ?>" target="_blank">Instagram</a>
            <?php endif; ?>
        </div>

        <?php if ($email): ?>
            <p class="email">Contact: <?php echo esc_html($email); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>