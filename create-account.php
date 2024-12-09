<?php
/*
Plugin Name: Linktree Profile Creator
Description: Allows visitors to create a minimalistic Linktree-style profile by submitting their details.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Profile_Creator {

    public function __construct() {
        add_action('init', [$this, 'add_custom_rewrite_rule']);
        add_action('template_redirect', [$this, 'handle_custom_url']);
        add_shortcode('create_profile_form', [$this, 'render_profile_form']);
        add_action('init', [$this, 'handle_form_submission']);
        add_action('template_redirect', [$this, 'display_profile_page']);
    }

    // Add a custom rewrite rule for /profile-creator
    public function add_custom_rewrite_rule() {
        add_rewrite_rule('^profile-creator/?$', 'index.php?profile_creator=1', 'top');
    }

    // Register the query variable
    public function query_vars($vars) {
        $vars[] = 'profile_creator';
        return $vars;
    }

    // Handle custom URL logic
    public function handle_custom_url() {
        if (get_query_var('profile_creator')) {
            //echo do_shortcode('[create_profile_form]');
            echo $this->render_profile_form();
            exit;
        }
    }
    
    public function render_profile_form() {
        ob_start();
        ?>
        <form method="post">
            <p><label>Email: <input type="email" name="email" required></label></p>
            <p><label>Website URL: <input type="url" name="website"></label></p>
            <p><label>Twitter URL: <input type="url" name="twitter"></label></p>
            <p><label>LinkedIn URL: <input type="url" name="linkedin"></label></p>
            <p><label>Instagram URL: <input type="url" name="instagram"></label></p>
            <p><input type="hidden" name="profile_nonce" value="<?php echo wp_create_nonce('profile_form'); ?>"></p>
            <p><input type="submit" name="submit_profile" value="Create Profile"></p>
        </form>
        <?php
        return ob_get_clean();
    }

    public function handle_form_submission() {
        if (isset($_POST['submit_profile']) && wp_verify_nonce($_POST['profile_nonce'], 'profile_form')) {
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

                    $this->create_profile_post($user_id, $email);

                    wp_redirect(add_query_arg(['profile_created' => 1], home_url()));
                    exit;
                }
            }
        }
    }

    private function create_profile_post($user_id, $email) {
        $post_id = wp_insert_post([
            'post_title'   => "Profile of User #$user_id",
            'post_content' => "A new profile has been created for $email. Visit /profile/$user_id to see the profile.",
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);
    }

    public function display_profile_page() {
        if (preg_match('/^\/profile\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $user_id = intval($matches[1]);

            if ($user_id && get_userdata($user_id)) {
                $website = get_user_meta($user_id, 'website', true);
                $twitter = get_user_meta($user_id, 'twitter', true);
                $linkedin = get_user_meta($user_id, 'linkedin', true);
                $instagram = get_user_meta($user_id, 'instagram', true);

                $output = '<h1>Profile</h1>';
                $output .= '<ul>';
                if ($website) $output .= "<li><a href='$website'>Website</a></li>";
                if ($twitter) $output .= "<li><a href='$twitter'>Twitter</a></li>";
                if ($linkedin) $output .= "<li><a href='$linkedin'>LinkedIn</a></li>";
                if ($instagram) $output .= "<li><a href='$instagram'>Instagram</a></li>";
                $output .= '</ul>';

                echo $output;
                exit;
            }
        }
    }
}

$profile_creator = new Profile_Creator();