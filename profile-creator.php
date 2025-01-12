<?php

// Add a custom rewrite rule for /profile-creator
function add_profile_creator_rewrite_rule() {
    add_rewrite_rule('^profile-creator/?$', 'index.php?profile_creator=1', 'top');
}
add_action('init', 'add_profile_creator_rewrite_rule');

// Register the query variable
function add_profile_creator_query_var($vars) {
    $vars[] = 'profile_creator';
    return $vars;
}
add_filter('query_vars', 'add_profile_creator_query_var');

// Intercept requests to /profile-creator and display the form
function handle_profile_creator_request() {
    if (get_query_var('profile_creator')) {
        echo '<!DOCTYPE html><html><head><title>Profile Creator</title></head><body>';
        echo '<h1>Create Your Profile</h1>';
        echo render_profile_form(); // Call the function that renders the form
        echo '</body></html>';
        exit;
    }
}
add_action('template_redirect', 'handle_profile_creator_request');

// Function to render the profile creation form
function render_profile_form() {
    ob_start();
    ?>
    <form method="post" action="">
        <p><label>Email: <input type="email" name="email" required></label></p>
        <p><label>Website URL: <input type="url" name="website"></label></p>
        <p><label>Twitter URL: <input type="url" name="twitter"></label></p>
        <p><label>LinkedIn URL: <input type="url" name="linkedin"></label></p>
        <p><label>Instagram URL: <input type="url" name="instagram"></label></p>
        <p><input type="hidden" name="linktree_nonce" value="<?php echo wp_create_nonce('linktree_form'); ?>"></p>
        <p><input type="submit" name="submit_profile" value="Create Profile"></p>
    </form>
    <?php
    return ob_get_clean();
}

// Handle form submission
function handle_form_submission() {
    if (isset($_POST['submit_profile']) && wp_verify_nonce($_POST['linktree_nonce'], 'linktree_form')) {
        $email = sanitize_email($_POST['email']);
        $website = esc_url_raw($_POST['website']);
        $twitter = esc_url_raw($_POST['twitter']);
        $linkedin = esc_url_raw($_POST['linkedin']);
        $instagram = esc_url_raw($_POST['instagram']);

        if (!email_exists($email)) {
            $password = wp_generate_password();
            $user_id = wp_create_user($email, $password, $email);

            if (!is_wp_error($user_id)) {
                update_user_meta($user_id, 'website', $website);
                update_user_meta($user_id, 'twitter', $twitter);
                update_user_meta($user_id, 'linkedin', $linkedin);
                update_user_meta($user_id, 'instagram', $instagram);

                wp_redirect(add_query_arg(['profile_created' => 1], home_url('/profile-creator')));
                exit;
            }
        }
    }
}
add_action('init', 'handle_form_submission');