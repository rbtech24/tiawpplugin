<?php
/**
 * Template Name: Claim Listing
 * Description: Template for handling listing claims
 */

// Exit if accessed directly

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get the listing ID from the URL
$listing_id = isset($_GET['claim']) ? intval($_GET['claim']) : 0;
$claim_token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
$message = '';
$error = '';

// Verify user is logged in
if (!is_user_logged_in()) {
    $login_url = wp_login_url(add_query_arg(array(
        'claim' => $listing_id,
        'token' => $claim_token
    ), get_permalink()));
    wp_redirect($login_url);
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_listing_nonce'])) {
    if (wp_verify_nonce($_POST['claim_listing_nonce'], 'claim_listing_' . $listing_id)) {
        $verification_data = array(
            'business_name' => sanitize_text_field($_POST['business_name']),
            'phone' => sanitize_text_field($_POST['phone']),
            'position' => sanitize_text_field($_POST['position']),
            'proof_type' => sanitize_text_field($_POST['proof_type'])
        );

        // Handle file upload
        if (!empty($_FILES['verification_document']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $attachment_id = media_handle_upload('verification_document', 0);
            if (!is_wp_error($attachment_id)) {
                $verification_data['document'] = $attachment_id;
            }
        }

        // Save claim request
        update_post_meta($listing_id, '_claim_request', $verification_data);
        update_post_meta($listing_id, '_claim_status', 'pending');
        
        // Get listing details for email
        $listing = get_post($listing_id);
        $address = get_field('street_address', $listing_id);
        $city = get_field('city', $listing_id);
        $state = get_field('state', $listing_id);
        
        // Prepare email content with all form fields
        $admin_email = get_option('admin_email');
        $admin_email='praveenxr@gmail.com';
        $subject = 'New Listing Claim Request';
        
        // Build detailed email message
        $message = "A new claim request has been submitted for listing #$listing_id\n\n";
        $message .= "Listing Details:\n";
        $message .= "----------------\n";
        $message .= "Listing Title: " . $listing->post_title . "\n";
        $message .= "Address: " . $address . "\n";
        $message .= "City: " . $city . "\n";
        $message .= "State: " . $state . "\n\n";
        
        $message .= "Claim Request Details:\n";
        $message .= "--------------------\n";
        $message .= "Business Name: " . $verification_data['business_name'] . "\n";
        $message .= "Phone Number: " . $verification_data['phone'] . "\n";
        $message .= "Position at Business: " . $verification_data['position'] . "\n";
        $message .= "Verification Document Type: " . $verification_data['proof_type'] . "\n";
        
        // Add document information if uploaded
        if (isset($verification_data['document'])) {
            $document_url = wp_get_attachment_url($verification_data['document']);
            $message .= "Verification Document: " . $document_url . "\n";
        }
        
        $message .= "\nSubmission Details:\n";
        $message .= "-----------------\n";
        $message .= "Submitted By: " . wp_get_current_user()->display_name . "\n";
        $message .= "Submission Date: " . current_time('mysql') . "\n";
        $message .= "Terms Agreement: Accepted\n";
        
        // Add admin review link
        $admin_url = admin_url('post.php?post=' . $listing_id . '&action=edit');
        $message .= "\nAdmin Actions:\n";
        $message .= "--------------\n";
        $message .= "Review Listing: " . $admin_url . "\n";
        
        wp_mail($admin_email, $subject, $message);
        
        $message = 'Your claim request has been submitted and is pending review.';
    } else {
        $error = 'Invalid nonce verification.';
    }
}


// Get listing data
$listing = get_post($listing_id);

if (!$listing || $listing->post_type !== 'directory_listing') {
    wp_redirect(home_url());
    exit;
}

?>

<div class="claim-listing-container">
    <div class="claim-listing-wrapper">
        <h1>Claim Your Business Listing</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message success"><?php echo esc_html($message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo esc_html($error); ?></div>
        <?php endif; ?>

        <div class="listing-details">
            <h2><?php echo esc_html($listing->post_title); ?></h2>
            <?php
            $address = get_field('street_address', $listing_id);
            $city = get_field('city', $listing_id);
            $state = get_field('state', $listing_id);
            ?>
            <p class="listing-address">
                <?php echo esc_html($address); ?><br>
                <?php echo esc_html($city . ', ' . $state); ?>
            </p>
        </div>

        <form method="post" enctype="multipart/form-data" class="claim-listing-form">
            <?php wp_nonce_field('claim_listing_' . $listing_id, 'claim_listing_nonce'); ?>
            
            <div class="form-group">
                <label for="business_name">Business Name</label>
                <input type="text" id="business_name" name="business_name" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="position">Your Position at the Business</label>
                <input type="text" id="position" name="position" required>
            </div>

            <div class="form-group">
                <label for="proof_type">Verification Document Type</label>
                <select id="proof_type" name="proof_type" required>
                    <option value="">Select Document Type</option>
                    <option value="business_license">Business License</option>
                    <option value="utility_bill">Utility Bill</option>
                    <option value="tax_document">Tax Document</option>
                    <option value="other">Other Business Document</option>
                </select>
            </div>

            <div class="form-group">
                <label for="verification_document">Upload Verification Document</label>
                <input type="file" id="verification_document" name="verification_document" required>
                <p class="help-text">Please upload a document that proves your association with this business.</p>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="terms_agreement" required>
                    I confirm that I am authorized to claim this business listing and that all information provided is accurate.
                </label>
            </div>

            <button type="submit" class="submit-button">Submit Claim Request</button>
        </form>
    </div>
</div>

<style>
.claim-listing-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
}

.claim-listing-wrapper {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.message {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.listing-details {
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 4px;
}

.listing-details h2 {
    margin: 0 0 10px 0;
    color: #333;
}

.listing-address {
    color: #666;
    margin: 0;
}

.claim-listing-form {
    margin-top: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.form-group input[type="text"],
.form-group input[type="tel"],
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

.form-group input[type="file"] {
    padding: 10px 0;
}

.help-text {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.submit-button {
    background: #0073e6;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.submit-button:hover {
    background: #0056b3;
}

@media (max-width: 768px) {
    .claim-listing-container {
        padding: 10px;
    }

    .claim-listing-wrapper {
        padding: 20px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Phone number formatting
    $('#phone').on('input', function() {
        let phone = $(this).val().replace(/\D/g, '');
        if (phone.length >= 10) {
            phone = phone.match(/(\d{3})(\d{3})(\d{4})/);
            $(this).val('(' + phone[1] + ') ' + phone[2] + '-' + phone[3]);
        }
    });

    // File validation
    $('#verification_document').on('change', function() {
        const file = this.files[0];
        const fileType = file.type;
        const maxSize = 5 * 1024 * 1024; // 5MB

        const allowedTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/jpg',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (!allowedTypes.includes(fileType)) {
            alert('Please upload a PDF, image, or Word document.');
            this.value = '';
            return;
        }

        if (file.size > maxSize) {
            alert('File size must be less than 5MB.');
            this.value = '';
            return;
        }
    });

    // Form validation
    $('.claim-listing-form').on('submit', function(e) {
        if (!$('input[name="terms_agreement"]').is(':checked')) {
            e.preventDefault();
            alert('Please agree to the terms before submitting.');
        }
    });
});
</script>

<?php
get_footer();
?>
