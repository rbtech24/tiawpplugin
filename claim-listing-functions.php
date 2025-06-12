<?php
/**
 * Functions for handling listing claims
 */

// Initialize claim functionality
function init_listing_claims() {
    add_action('init', 'register_claim_post_status');
    add_action('init', 'add_claim_capabilities');
    add_action('admin_menu', 'add_claim_management_menu');
    add_action('wp_ajax_verify_claim_token', 'handle_claim_token_verification');
    add_action('wp_ajax_process_claim_request', 'handle_claim_request');
}
add_action('plugins_loaded', 'init_listing_claims');

// Register custom post status for claims
function register_claim_post_status() {
    register_post_status('claimed', array(
        'label' => _x('Claimed', 'post'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Claimed <span class="count">(%s)</span>',
                                'Claimed <span class="count">(%s)</span>')
    ));
}

// Add capabilities for claim management
function add_claim_capabilities() {
    $roles = array('administrator', 'editor');
    foreach ($roles as $role) {
        $role_obj = get_role($role);
        if ($role_obj) {
            $role_obj->add_cap('manage_listing_claims');
        }
    }
}

// Add claim management menu
function add_claim_management_menu() {
    add_submenu_page(
        'edit.php?post_type=directory_listing',
        'Manage Claims',
        'Manage Claims',
        'manage_listing_claims',
        'manage-claims',
        'render_claim_management_page'
    );
}

// Generate unique claim token
function generate_claim_token($listing_id) {
    $token = wp_generate_password(32, false);
    update_post_meta($listing_id, '_claim_token', $token);
    return $token;
}

// Verify claim token
function verify_claim_token($listing_id, $token) {
    $stored_token = get_post_meta($listing_id, '_claim_token', true);
    return $stored_token && $stored_token === $token;
}

// Process claim request
function process_claim_request($listing_id, $user_id, $verification_data) {
    // Validate listing exists and is not already claimed
    $listing = get_post($listing_id);
    if (!$listing || $listing->post_type !== 'directory_listing') {
        return new WP_Error('invalid_listing', 'Invalid listing ID');
    }

    if (get_post_meta($listing_id, '_claimed', true)) {
        return new WP_Error('already_claimed', 'This listing has already been claimed');
    }

    // Save claim request data
    $claim_data = array(
        'user_id' => $user_id,
        'timestamp' => current_time('mysql'),
        'verification_data' => $verification_data,
        'status' => 'pending'
    );

    update_post_meta($listing_id, '_claim_request', $claim_data);
    update_post_meta($listing_id, '_claim_status', 'pending');

    // Notify admin
    notify_admin_of_claim($listing_id, $claim_data);

    return true;
}

// Notify admin of new claim
function notify_admin_of_claim($listing_id, $claim_data) {
    $admin_email = get_option('admin_email');
    $subject = sprintf(__('New Claim Request for Listing #%d', 'directory-listings'), $listing_id);
    
    $user = get_userdata($claim_data['user_id']);
    $listing = get_post($listing_id);
    
    $message = sprintf(
        __("A new claim request has been submitted:\n\n" .
           "Listing: %s (#%d)\n" .
           "Claimed by: %s (User ID: %d)\n" .
           "Email: %s\n" .
           "Submitted: %s\n\n" .
           "Review the claim here: %s",
           'directory-listings'),
        $listing->post_title,
        $listing_id,
        $user->display_name,
        $user->ID,
        $user->user_email,
        $claim_data['timestamp'],
        admin_url("admin.php?page=manage-claims&listing=" . $listing_id)
    );

    wp_mail($admin_email, $subject, $message);
}

// Approve claim
function approve_claim($listing_id, $admin_id) {
    $claim_data = get_post_meta($listing_id, '_claim_request', true);
    if (!$claim_data) {
        return new WP_Error('no_claim', 'No claim request found for this listing');
    }

    // Update listing ownership
    $user_id = $claim_data['user_id'];
    wp_update_post(array(
        'ID' => $listing_id,
        'post_author' => $user_id,
        'post_status' => 'claimed'
    ));

    // Update claim status
    update_post_meta($listing_id, '_claimed', true);
    update_post_meta($listing_id, '_claim_status', 'approved');
    update_post_meta($listing_id, '_claim_approved_by', $admin_id);
    update_post_meta($listing_id, '_claim_approved_date', current_time('mysql'));

    // Notify user
    notify_user_of_claim_approval($listing_id, $user_id);

    return true;
}

// Reject claim
function reject_claim($listing_id, $admin_id, $reason = '') {
    $claim_data = get_post_meta($listing_id, '_claim_request', true);
    if (!$claim_data) {
        return new WP_Error('no_claim', 'No claim request found for this listing');
    }

    // Update claim status
    update_post_meta($listing_id, '_claim_status', 'rejected');
    update_post_meta($listing_id, '_claim_rejected_by', $admin_id);
    update_post_meta($listing_id, '_claim_rejected_date', current_time('mysql'));
    update_post_meta($listing_id, '_claim_rejection_reason', $reason);

    // Notify user
    notify_user_of_claim_rejection($listing_id, $claim_data['user_id'], $reason);

    return true;
}

// Notify user of claim approval
function notify_user_of_claim_approval($listing_id, $user_id) {
    $user = get_userdata($user_id);
    $listing = get_post($listing_id);
    
    $subject = sprintf(__('Your claim for "%s" has been approved', 'directory-listings'), 
                      $listing->post_title);
    
    $message = sprintf(
        __("Congratulations! Your claim for the following listing has been approved:\n\n" .
           "Listing: %s\n" .
           "You can now edit your listing here: %s\n\n" .
           "Thank you for using our directory!",
           'directory-listings'),
        $listing->post_title,
        get_edit_post_link($listing_id, 'email')
    );

    wp_mail($user->user_email, $subject, $message);
}

// Notify user of claim rejection
function notify_user_of_claim_rejection($listing_id, $user_id, $reason) {
    $user = get_userdata($user_id);
    $listing = get_post($listing_id);
    
    $subject = sprintf(__('Update on your claim for "%s"', 'directory-listings'), 
                      $listing->post_title);
    
    $message = sprintf(
        __("Your claim request for the following listing has been reviewed:\n\n" .
           "Listing: %s\n\n" .
           "Unfortunately, we were unable to verify your claim at this time.\n" .
           "Reason: %s\n\n" .
           "If you believe this is an error, please contact our support team.",
           'directory-listings'),
        $listing->post_title,
        $reason
    );

    wp_mail($user->user_email, $subject, $message);
}

// AJAX handler for claim token verification
function handle_claim_token_verification() {
    check_ajax_referer('verify_claim_token', 'nonce');
    
    $listing_id = isset($_POST['listing_id']) ? intval($_POST['listing_id']) : 0;
    $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
    
    if (verify_claim_token($listing_id, $token)) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Invalid claim token');
    }
}

// AJAX handler for claim request submission
function handle_claim_request() {
    check_ajax_referer('process_claim_request', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('User must be logged in');
    }
    
    $listing_id = isset($_POST['listing_id']) ? intval($_POST['listing_id']) : 0;
    $verification_data = isset($_POST['verification_data']) ? $_POST['verification_data'] : array();
    
    $result = process_claim_request($listing_id, get_current_user_id(), $verification_data);
    
    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    } else {
        wp_send_json_success('Claim request submitted successfully');
    }
}

// Render claim management page in admin
function render_claim_management_page() {
    if (!current_user_can('manage_listing_claims')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    // Handle claim actions
    if (isset($_POST['claim_action']) && isset($_POST['listing_id'])) {
        $listing_id = intval($_POST['listing_id']);
        $action = $_POST['claim_action'];
        
        if ($action === 'approve') {
            approve_claim($listing_id, get_current_user_id());
        } elseif ($action === 'reject') {
            $reason = isset($_POST['rejection_reason']) ? 
                     sanitize_textarea_field($_POST['rejection_reason']) : '';
            reject_claim($listing_id, get_current_user_id(), $reason);
        }
    }
    
    // Get pending claims
    $pending_claims = get_pending_claims();
    ?>
    <div class="wrap">
        <h1><?php _e('Manage Listing Claims', 'directory-listings'); ?></h1>

        <div class="claim-management-tabs">
            <a href="?page=manage-claims&status=pending" class="<?php echo !isset($_GET['status']) || $_GET['status'] === 'pending' ? 'active' : ''; ?>">
                Pending Claims
            </a>
            <a href="?page=manage-claims&status=approved" class="<?php echo isset($_GET['status']) && $_GET['status'] === 'approved' ? 'active' : ''; ?>">
                Approved Claims
            </a>
            <a href="?page=manage-claims&status=rejected" class="<?php echo isset($_GET['status']) && $_GET['status'] === 'rejected' ? 'active' : ''; ?>">
                Rejected Claims
            </a>
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Listing</th>
                    <th>Claimed By</th>
                    <th>Submitted</th>
                    <th>Verification</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pending_claims)): ?>
                    <tr>
                        <td colspan="6">No claims found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pending_claims as $claim): ?>
                        <?php
                        $listing = get_post($claim->listing_id);
                        $user = get_userdata($claim->user_id);
                        $verification_data = get_post_meta($claim->listing_id, '_claim_request', true);
                        ?>
                        <tr>
                            <td>
                                <strong><a href="<?php echo get_permalink($listing->ID); ?>"><?php echo esc_html($listing->post_title); ?></a></strong>
                            </td>
                            <td>
                                <?php echo esc_html($user->display_name); ?><br>
                                <small><?php echo esc_html($user->user_email); ?></small>
                            </td>
                            <td>
                                <?php echo esc_html(human_time_diff(strtotime($claim->submitted), current_time('timestamp'))); ?> ago
                            </td>
                            <td>
                                <?php if (isset($verification_data['document'])): ?>
                                    <a href="<?php echo wp_get_attachment_url($verification_data['document']); ?>" target="_blank">
                                        View Document
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="claim-status status-<?php echo esc_attr($claim->status); ?>">
                                    <?php echo esc_html(ucfirst($claim->status)); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($claim->status === 'pending'): ?>
                                    <form method="post" style="display: inline-block;">
                                        <input type="hidden" name="listing_id" value="<?php echo esc_attr($claim->listing_id); ?>">
                                        <input type="hidden" name="claim_action" value="approve">
                                        <?php wp_nonce_field('claim_action_nonce', 'claim_action_nonce'); ?>
                                        <button type="submit" class="button button-primary">Approve</button>
                                    </form>
                                    
                                    <button type="button" class="button reject-claim-button" 
                                            data-listing-id="<?php echo esc_attr($claim->listing_id); ?>">
                                        Reject
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Rejection Modal -->
    <div id="reject-claim-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Reject Claim</h2>
            <form method="post" id="reject-claim-form">
                <input type="hidden" name="listing_id" id="reject-listing-id">
                <input type="hidden" name="claim_action" value="reject">
                <?php wp_nonce_field('claim_action_nonce', 'claim_action_nonce'); ?>
                
                <p>
                    <label for="rejection_reason">Reason for Rejection:</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" required></textarea>
                </p>
                
                <div class="modal-actions">
                    <button type="button" class="button cancel-reject">Cancel</button>
                    <button type="submit" class="button button-primary">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .claim-management-tabs {
            margin: 20px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        
        .claim-management-tabs a {
            display: inline-block;
            padding: 8px 15px;
            text-decoration: none;
            border: 1px solid transparent;
            margin-bottom: -1px;
        }
        
        .claim-management-tabs a.active {
            border: 1px solid #ccc;
            border-bottom-color: #fff;
            background: #fff;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            max-width: 500px;
        }
        
        .modal-actions {
            text-align: right;
            margin-top: 20px;
        }
        
        .modal-actions button {
            margin-left: 10px;
        }
        
        #rejection_reason {
            width: 100%;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        $('.reject-claim-button').click(function() {
            var listingId = $(this).data('listing-id');
            $('#reject-listing-id').val(listingId);
            $('#reject-claim-modal').show();
        });

        $('.cancel-reject').click(function() {
            $('#reject-claim-modal').hide();
        });

        $(window).click(function(e) {
            if ($(e.target).hasClass('modal')) {
                $('#reject-claim-modal').hide();
            }
        });
    });
    </script>
    <?php
}

// Get pending claims
function get_pending_claims() {
    global $wpdb;
    
    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'pending';
    
    $claims = $wpdb->get_results($wpdb->prepare("
        SELECT p.ID as listing_id, 
               pm1.meta_value as status,
               pm2.meta_value as claim_data
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id
        INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id
        WHERE p.post_type = 'directory_listing'
        AND pm1.meta_key = '_claim_status'
        AND pm1.meta_value = %s
        AND pm2.meta_key = '_claim_request'
        ORDER BY p.post_date DESC
    ", $status));
    
    foreach ($claims as &$claim) {
        $claim_data = maybe_unserialize($claim->claim_data);
        $claim->user_id = $claim_data['user_id'];
        $claim->submitted = $claim_data['timestamp'];
        $claim->verification_data = $claim_data['verification_data'];
    }
    
    return $claims;
}

// Add menu item
function add_claim_listing_menu_item() {
    add_submenu_page(
        'directory-listings-dashboard',
        __('Claim Listings', 'directory-listings'),
        __('Claim Listings', 'directory-listings'),
        'manage_options',
        'claim-listings',
        'render_claim_listing_page'
    );
}