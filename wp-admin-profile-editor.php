<?php
// Add custom fields to the user profile page
function add_custom_user_fields($user) {
    ?>
    <h3>Additional Profile Links</h3>
    <table class="form-table">
        <tr>
            <th><label for="website">Website</label></th>
            <td>
                <input type="url" name="website" id="website" value="<?php echo esc_attr(get_user_meta($user->ID, 'website', true)); ?>" class="regular-text" />
                <p class="description">Enter the user's website URL.</p>
            </td>
        </tr>
        <tr>
            <th><label for="twitter">Twitter</label></th>
            <td>
                <input type="url" name="twitter" id="twitter" value="<?php echo esc_attr(get_user_meta($user->ID, 'twitter', true)); ?>" class="regular-text" />
                <p class="description">Enter the user's Twitter profile URL.</p>
            </td>
        </tr>
        <tr>
            <th><label for="linkedin">LinkedIn</label></th>
            <td>
                <input type="url" name="linkedin" id="linkedin" value="<?php echo esc_attr(get_user_meta($user->ID, 'linkedin', true)); ?>" class="regular-text" />
                <p class="description">Enter the user's LinkedIn profile URL.</p>
            </td>
        </tr>
        <tr>
            <th><label for="instagram">Instagram</label></th>
            <td>
                <input type="url" name="instagram" id="instagram" value="<?php echo esc_attr(get_user_meta($user->ID, 'instagram', true)); ?>" class="regular-text" />
                <p class="description">Enter the user's Instagram profile URL.</p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'add_custom_user_fields'); // For the user editing their own profile
add_action('edit_user_profile', 'add_custom_user_fields'); // For an admin editing another user's profile

// Save the custom fields when the profile is updated
function save_custom_user_fields($user_id) {
    // Check user capabilities
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Save custom fields
    update_user_meta($user_id, 'website', sanitize_text_field($_POST['website']));
    update_user_meta($user_id, 'twitter', sanitize_text_field($_POST['twitter']));
    update_user_meta($user_id, 'linkedin', sanitize_text_field($_POST['linkedin']));
    update_user_meta($user_id, 'instagram', sanitize_text_field($_POST['instagram']));
}
add_action('personal_options_update', 'save_custom_user_fields'); // For the user updating their own profile
add_action('edit_user_profile_update', 'save_custom_user_fields'); // For an admin updating another user's profile
