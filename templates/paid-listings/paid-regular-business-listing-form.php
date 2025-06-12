<?php
/*
Template Name: Paid Business Directory Form
Description: Complete template including SEO expert service
*/

// First, handle WordPress backend functions
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/*
 // Basic email test
    $to = 'webcruiserin@gmail.com'; // Replace with your email
    $subject = 'Test Email';
    $message = 'This is a test email from WordPress';
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Send test email
    $result = wp_mail($to, $subject, $message, $headers);
    
    // Log result
    echo 'Test email result: ' . ($result ? 'Success' : 'Failed');
    
    // Check mail configuration
    $admin_email = get_option('admin_email');
    error_log('Admin email: ' . $admin_email);
    
    // Check if mail is disabled
    if (!function_exists('mail')) {
        echo 'PHP mail function is disabled';
    }



// Add this temporarily to test email sending
//add_action('init', function() {
  //  if (isset($_GET['test_seo_email'])) {
        $test_data = array(
            'email' => 'praveenxr@gmail.com',
            'business_name' => 'Test Business',
            'first_name' => 'Test'
        );
        send_business_owner_seo_confirmation(1, $test_data);
    //    exit('Email test completed');
    //}
//});
*/
// SEO Expert Service Functions
function handle_seo_expert_service($listing_id, $form_data) {
    if (!empty($form_data['seo_expert_service'])) {
        // Send email to business owner
        send_business_owner_seo_confirmation($listing_id, $form_data);
        
        // Send notification to SEO team
        notify_seo_team($listing_id, $form_data);
        
        // Update listing meta
        update_post_meta($listing_id, '_needs_seo_setup', true);
        update_post_meta($listing_id, '_seo_setup_requested_date', current_time('mysql'));
    }
}

function send_business_owner_seo_confirmation($listing_id, $form_data) {
    //echo 'hi';
    $business_email = sanitize_email($form_data['email']);
    $business_name = sanitize_text_field($form_data['business_name']);
    $first_name = sanitize_text_field($form_data['first_name']);
    
    $subject = 'Your SEO Expert Service Confirmation - ' . $business_name;
    
    $message = "Hello $first_name,\n\n";
    $message .= "Thank you for choosing our SEO Expert Service for your business listing.\n\n";
    $message .= "What happens next:\n";
    $message .= "1. One of our SEO experts will contact you within 24 hours at this email address\n";
    $message .= "2. They will work with you to gather any additional information needed\n";
    $message .= "3. Your listing will be professionally optimized for maximum visibility\n\n";
    $message .= "If you have any questions in the meantime, please reply to this email.\n\n";
    $message .= "Best regards,\nYour Business Directory Team";
    
    $headers = array('Content-Type: text/html; charset=UTF-8');
     //$message='hi';
    //echo $business_email;
    wp_mail($business_email, $subject, nl2br($message), $headers);
}

function notify_seo_team($listing_id, $form_data) {
    $seo_team_email = get_option('seo_team_email', 'seo@yourdomain.com');
    $business_name = sanitize_text_field($form_data['business_name']);
    
    $subject = 'New SEO Setup Request - ' . $business_name;
    
    $message = "<h2>New SEO Expert Service Request</h2>";
    $message .= "<p><strong>Business Details:</strong></p>";
    $message .= "<ul>";
    $message .= "<li>Business Name: " . esc_html($form_data['business_name']) . "</li>";
    $message .= "<li>Contact Name: " . esc_html($form_data['first_name']) . " " . esc_html($form_data['last_name']) . "</li>";
    $message .= "<li>Email: " . esc_html($form_data['email']) . "</li>";
    $message .= "<li>Phone: " . esc_html($form_data['phone']) . "</li>";
    $message .= "<li>Address: " . esc_html($form_data['address']) . "</li>";
    $message .= "</ul>";
    
    $message .= "<p><strong>Quick Actions:</strong></p>";
    $message .= "<ul>";
    $message .= "<li><a href='" . esc_url(get_edit_post_link($listing_id)) . "'>View Listing in Admin</a></li>";
    $message .= "<li><a href='" . esc_url(get_permalink($listing_id)) . "'>View Public Listing</a></li>";
    $message .= "</ul>";
    
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    wp_mail($seo_team_email, $subject, $message, $headers);
}

// Hook into listing submission
add_action('after_directory_listing_submission', 'handle_seo_expert_service', 10, 2);

// Add admin column for SEO setup status
add_filter('manage_business_listing_posts_columns', 'add_seo_status_column');
function add_seo_status_column($columns) {
    $columns['seo_setup'] = 'SEO Setup Status';
    return $columns;
}

add_action('manage_business_listing_posts_custom_column', 'display_seo_status_column', 10, 2);
function display_seo_status_column($column, $post_id) {
    if ($column === 'seo_setup') {
        $needs_setup = get_post_meta($post_id, '_needs_seo_setup', true);
        if ($needs_setup) {
            $requested_date = get_post_meta($post_id, '_seo_setup_requested_date', true);
            echo '<span style="color: #e67e22;">⏳ Pending SEO Setup<br>';
            echo 'Requested: ' . date('M j, Y', strtotime($requested_date)) . '</span>';
        } else {
            echo '✓ No Setup Needed';
        }
    }
}

// Start the template
get_header();
?>

<!-- External Dependencies -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAK1WEBfkwgWn6y2rid33ZDWHXOCPcuuOw&libraries=places&fields=formatted_address,geometry,name,address_components,website,formatted_phone_number"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://js.stripe.com/v3/"></script>

<!-- JavaScript Variables -->
<script>
    var ajax_object = {
        ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>'
    };
</script>

<!-- Main Form Container -->
<div class="directory-listing-form-container">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Add Your Business Details</h1>
            <p class="hero-text">Create your premium business listing</p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <!-- Progress Steps -->
        <div class="form-steps">
            <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <span>Basic Info</span>
            </div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <span>Contact</span>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <span>Media</span>
            </div>
            <div class="step" data-step="4">
                <div class="step-number">4</div>
                <span>Hours</span>
            </div>
            <div class="step" data-step="5">
                <div class="step-number">5</div>
                <span>Payment</span>
            </div>
            <div class="step" data-step="6">
                <div class="step-number">6</div>
                <span>Review</span>
            </div>
        </div>

        <!-- Main Form -->
        <form id="directory-listing-form" method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('directory_listing_nonce', 'directory_listing_nonce'); ?>
            <input type="hidden" name="action" value="submit_directory_listing">
            <input type="hidden" name="listing_type" value="paid">

            <!-- Hidden Address Fields -->
            <input type="hidden" id="street_number" name="street_number">
            <input type="hidden" id="route" name="route">
            <input type="hidden" id="locality" name="locality">
            <input type="hidden" id="administrative_area_level_1" name="administrative_area_level_1">
            <input type="hidden" id="postal_code" name="postal_code">
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            <input type="hidden" id="place_id" name="place_id">

            <!-- Step 1: Basic Info -->
            <div class="form-section active" data-step="1">
                <h2>Basic Information</h2>
                <div class="form-grid">
                    <div class="form-field">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>

                    <div class="form-field">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>

                    <div class="form-field">
                        <label for="business-name">Business Name <span class="required">*</span></label>
                        <input type="text" id="business-name" name="business_name" required 
                               placeholder="Start typing your business name...">
                    </div>

                    <div class="form-field">
                        <label for="address">Business Address <span class="required">*</span></label>
                        <input type="text" id="address" name="address" required 
                               placeholder="Enter business address">
                    </div>

                    <div class="form-field">
                        <label for="category">Business Category <span class="required">*</span></label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <?php
                            $categories = get_terms(array(
                                'taxonomy' => 'business_category',
                                'hide_empty' => false,
                                'parent' => 0
                            ));
                            foreach ($categories as $category) :
                            ?>
                                <option value="<?php echo esc_attr($category->term_id); ?>">
                                    <?php echo esc_html($category->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 2: Contact Info -->
            <div class="form-section" data-step="2">
                <h2>Contact Information</h2>
                <div class="form-grid">
                    <div class="form-field">
                        <label for="phone">Phone Number <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" required>
                        <span class="field-hint">Format: (XXX) XXX-XXXX</span>
                    </div>

                    <div class="form-field">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required>
                        <span class="field-hint">This will be used for account verification</span>
                    </div>

                    <div class="form-field">
                        <label for="website">Website URL</label>
                        <input type="url" id="website" name="website" placeholder="https://">
                    </div>

                    <div class="form-field">
                        <label for="description">Business Description <span class="required">*</span></label>
                        <textarea id="description" name="description" rows="5" required></textarea>
                        <span class="character-count">0/500 characters</span>
                    </div>
                </div>
            </div>

            <!-- Step 3: Media -->
            <div class="form-section" data-step="3">
                <h2>Media & Additional Information</h2>
                <div class="form-grid">
                    <div class="form-field">
                        <label for="logo">Business Logo</label>
                        <div class="file-upload-container" id="logo-dropzone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag & drop your logo here or click to browse</p>
                            <input type="file" id="logo" name="logo" accept="image/*" class="file-input">
                        </div>
                        <div id="logo-preview" class="file-preview"></div>
                        <span class="field-hint">Maximum file size: 2MB</span>
                    </div>

                    <div class="form-field">
                        <label for="gallery">Photo Gallery</label>
                        <div class="file-upload-container" id="gallery-dropzone">
                            <i class="fas fa-images"></i>
                            <p>Drag & drop up to 5 images or click to browse</p>
                            <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple class="file-input">
                        </div>
                        <div id="gallery-preview" class="file-preview"></div>
                        <span class="field-hint">Maximum 5 images, 5MB each</span>
                    </div>
                </div>
                
                
                <div class="seo-expert-option">
                            <label class="checkbox-wrapper">
                                <input type="checkbox" id="seo-expert-service" name="seo_expert_service">
                                <span class="checkbox-label">Add SEO Expert Setup Service (+$60)</span>
                            </label>
                            <div class="seo-expert-details">
                                <ul class="package-features">
                                    <li>Professional profile setup by our SEO expert</li>
                                    <li>Optimized business description and keywords</li>
                                    <li>Professional image selection and optimization</li>
                                    <li>Your expert will contact you within 24 hours</li>
                                </ul>
                            </div>
                        </div>
                        
                        
                
            </div>

            <!-- Step 4: Business Hours -->
            <div class="form-section" data-step="4">
                <h2>Business Hours</h2>
                <div class="business-hours-section">
                    <div class="hours-header">
                        <div class="hours-presets">
                            <button type="button" class="btn-secondary preset-btn" data-preset="24-7">
                                <i class="fas fa-clock"></i> Set as 24/7
                            </button>
                            <button type="button" class="btn-secondary preset-btn" data-preset="weekdays">
                                <i class="fas fa-business-time"></i> Standard Weekdays
                            </button>
                        </div>
                        <label class="auto-copy-hours">
                            <input type="checkbox" id="autoCopyHours">
                            Copy Monday's hours to weekdays
                        </label>
                    </div>
                    
                    <div id="business-hours-container">
                        <?php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        foreach ($days as $day) :
                            $day_lower = strtolower($day);
                        ?>
                            <div class="business-hours-day" data-day="<?php echo $day_lower; ?>">
                                <div class="day-header">
                                    <label class="day-toggle">
                                        <input type="checkbox" name="business_hours[<?php echo $day_lower; ?>][open]" value="1">
                                        <span class="day-name"><?php echo $day; ?></span>
                                    </label>
                                </div>
                                <div class="hours-inputs">
                                    <input type="time" name="business_hours[<?php echo $day_lower; ?>][open_time]" 
                                           class="time-input">
                                    <span class="time-separator">to</span>
                                    <input type="time" name="business_hours[<?php echo $day_lower; ?>][close_time]" 
                                           class="time-input">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Step 5: Payment -->
            <div class="form-section" data-step="5">
                <h2>Payment Information</h2>
                <div class="payment-section">
                    <div class="form-field">
                        <label>Listing Package</label>
                        <div class="package-info">
                            <h3>Premium Business Listing</h3>
                            <p class="package-price">$99.99/year</p>
                            <ul class="package-features">
                                <li>Priority Placement in Search Results</li>
                                <li>Photo Gallery (up to 5 images)</li>
                                <li>Business Hours Display</li>
                                <li>Website Link</li>
                                <li>Extended Business Description</li>
                            </ul>
                        </div>
                        
                        
                        
                        <div class="total-price">
                            Total: $99.99
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="card-element">Credit or Debit Card <span class="required">*</span></label>
                        <div id="card-element" class="stripe-input"></div>
                        <div id="card-errors" class="error-message" role="alert"></div>
                    </div>
                </div>
            </div>

            <!-- Step 6: Review -->
            <div class="form-section" data-step="6">
                <h2>Review Your Listing</h2>
                <div id="listing-preview" class="listing-preview"></div>

                <!-- Terms and Conditions -->
                <div class="form-field terms-conditions">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="terms-and-conditions" name="terms_and_conditions" required>
                        <label for="terms-and-conditions">
                            I agree to the <a href="#" target="_blank">Terms and Conditions</a>
                        </label>
                    </div>
                </div>
                <div id="payment_error" style="color:red;"></div>
                <button type="submit" class="" id="pay_now">Pay Now</button>
            </div>

            <!-- Form Navigation -->
            <div class="form-navigation">
                <button type="button" id="prev-step" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <button type="button" id="next-step" class="btn-primary">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" id="submit-listing" class="btn-primary" style="display:none">
                    Submit Listing <i class="fas fa-check"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Root Variables */
:root {
    --primary: #4F46E5;
    --primary-dark: #4338CA;
    --secondary: #6B7280;
    --success: #10B981;
    --danger: #EF4444;
    --warning: #F59E0B;
    --navy: #26387E;
    --red: #CD1339;
    --white: #FFFFFF;
    --gray-50: #F9FAFB;
    --gray-100: #F3F4F6;
    --gray-200: #E5E7EB;
    --gray-300: #D1D5DB;
    --gray-400: #9CA3AF;
    --gray-500: #6B7280;
    --gray-600: #4B5563;
    --gray-700: #374151;
    --gray-800: #1F2937;
    --gray-900: #111827;
    
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    
    --rounded-sm: 0.125rem;
    --rounded: 0.25rem;
    --rounded-md: 0.375rem;
    --rounded-lg: 0.5rem;
    --rounded-xl: 0.75rem;
    --rounded-2xl: 1rem;
    --rounded-3xl: 1.5rem;
    --rounded-full: 9999px;
}

/* Base Container */
.directory-listing-form-container {
    width: 100%;
    max-width: none;
    margin: 0;
    padding: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

/* Hero Section */
.hero-section {
    position: relative;
    width: 100vw;
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
    background-color: var(--navy);
    padding: 6rem 2rem;
    text-align: center;
    color: var(--white);
    margin-bottom: -2rem;
    z-index: 1;
    margin-top: -125px;
    padding-top: 125px;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(38, 56, 126, 0.95), rgba(38, 56, 126, 0.8));
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
    margin: 0 auto;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--white);
}

.hero-text {
    font-size: 1.25rem;
    opacity: 0.9;
    margin: 0;
}

/* Form Container */
.form-container {
    max-width: 800px;
    margin: -30px auto 60px;
    padding: 0 20px;
    position: relative;
    z-index: 10;
}

/* Progress Steps */
.form-steps {
    background: var(--white);
    padding: 30px;
    border-radius: var(--rounded-lg);
    box-shadow: var(--shadow-md);
    display: flex;
    justify-content: space-between;
    margin-bottom: 24px;
    border: 1px solid var(--gray-200);
}

.step {
    flex: 1;
    text-align: center;
    position: relative;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    right: -50%;
    width: 100%;
    height: 2px;
    background-color: var(--gray-200);
    z-index: 1;
}

.step.active:not(:last-child)::after {
    background-color: var(--primary);
}

.step-number {
    width: 40px;
    height: 40px;
    background: var(--gray-200);
    border-radius: var(--rounded-full);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-weight: 600;
    color: var(--gray-600);
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: var(--primary);
    color: var(--white);
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2);
}

/* Form Sections */
.form-section {
    background: var(--white);
    padding: 40px;
    border-radius: var(--rounded-lg);
    box-shadow: var(--shadow);
    margin-bottom: 24px;
    display: none;
    border: 1px solid var(--gray-200);
}

.form-section.active {
    display: block;
}

/* Form Fields */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
}

.form-field {
    margin-bottom: 20px;
}

.form-field label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--gray-700);
}

.form-field input[type="text"],
.form-field input[type="email"],
.form-field input[type="tel"],
.form-field input[type="url"],
.form-field select,
.form-field textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--gray-300);
    border-radius: var(--rounded);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-field input:focus,
.form-field select:focus,
.form-field textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* File Upload */
.file-upload-container {
    border: 2px dashed var(--gray-300);
    padding: 30px;
    text-align: center;
    border-radius: var(--rounded-lg);
    background: var(--gray-50);
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-container:hover,
.file-upload-container.dragover {
    border-color: var(--primary);
    background: var(--gray-100);
}

.file-preview {
    margin-top: 16px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 16px;
}

.file-preview-item {
    position: relative;
    padding: 8px;
    background: var(--white);
    border-radius: var(--rounded);
    border: 1px solid var(--gray-200);
}

.file-preview-item img {
    width: 100%;
    height: auto;
    border-radius: var(--rounded-sm);
}

/* Business Hours */
.business-hours-section {
    background: var(--white);
    padding: 24px;
    border-radius: var(--rounded-lg);
    border: 1px solid var(--gray-200);
}

.hours-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.hours-presets {
    display: flex;
    gap: 12px;
}

.business-hours-day {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--gray-200);
}

.hours-inputs {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Payment Section */
.payment-section {
    background: var(--white);
    padding: 30px;
    border-radius: var(--rounded-lg);
    border: 1px solid var(--gray-200);
}

.package-info {
    background: var(--gray-50);
    padding: 24px;
    border-radius: var(--rounded);
    margin-bottom: 24px;
    transition: all 0.3s ease;
}

.package-info:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.package-price {
    font-size: 24px;
    font-weight: 600;
    color: var(--navy);
    margin: 12px 0;
}

.package-features {
    list-style: none;
    padding: 0;
    margin: 16px 0;
}

.package-features li {
    padding: 8px 0;
    color: var(--gray-700);
    display: flex;
    align-items: center;
    gap: 8px;
}

.package-features li::before {
    content: '✓';
    color: var(--success);
    font-weight: bold;
}

/* SEO Expert Option */
.seo-expert-option {
    margin-top: 20px;
    padding: 20px;
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--rounded);
    transition: all 0.3s ease;
}

.seo-expert-option:hover {
    background: var(--gray-100);
    border-color: var(--primary);
}

.seo-expert-option .checkbox-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.seo-expert-option .checkbox-label {
    font-weight: 600;
    color: var(--gray-800);
}

.seo-expert-details {
    padding-left: 30px;
    margin-top: 10px;
}

.total-price {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid var(--gray-200);
    font-size: 1.2em;
    font-weight: 600;
    text-align: right;
    color: var(--navy);
}

/* Form Navigation */
.form-navigation {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
    gap: 20px;
}

.btn-primary,
.btn-secondary {
    padding: 12px 24px;
    border-radius: var(--rounded);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
    border: none;
}

.btn-primary {
    background: var(--primary);
    color: var(--white);
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-secondary {
    background: var(--gray-100);
    color: var(--gray-700);
}

.btn-secondary:hover {
    background: var(--gray-200);
}

/* Error States */
.error-message {
    color: var(--danger);
    font-size: 0.875rem;
    margin-top: 4px;
}

.form-field.error input,
.form-field.error select,
.form-field.error textarea {
    border-color: var(--danger);
}

/* Loading States */
.loading {
    opacity: 0.7;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid var(--white);
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-section {
        padding: 8rem 1rem 4rem;
        margin-top: -80px;
    }

    .hero-title {
        font-size: 2rem;
    }

    .form-container {
        padding: 0 16px;
        margin-top: -20px;
    }

    .form-steps {
        padding: 20px 10px;
    }

    .step span {
        display: none;
    }

    .form-section {
        padding: 20px;
    }

    .form-navigation {
        flex-direction: column;
    }

    .btn-primary,
    .btn-secondary {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const DEBUG = true;
    
    function debugLog(message, data = null) {
        if (DEBUG) {
            console.log(`[Directory Form] ${message}`, data || '');
        }
    }

    // Initialize Stripe
    const stripe = Stripe('pk_live_51Q1IJKABx6OzSP6kA2eNndSD5luY9WJPP6HSuQ9QFZOFGIlTQaT0YeHAQCIuTlHXEZ0eV04wBl3WdjBtCf4gXi2W00jdezk2mo');
    //const stripe = Stripe('pk_test_gaeFXMw78Ee7fDWbXhwUJfbY');
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#1F2937',
                '::placeholder': {
                    color: '#6B7280',
                },
            },
            invalid: {
                color: '#EF4444',
            },
        },
    });
    cardElement.mount('#card-element');

    // Initialize form elements
    const form = document.getElementById('directory-listing-form');
    const sections = document.querySelectorAll('.form-section');
    const progressSteps = document.querySelectorAll('.step');
    const prevBtn = document.getElementById('prev-step');
    const nextBtn = document.getElementById('next-step');
    const submitBtn = document.getElementById('submit-listing');
    const seoExpertCheckbox = document.getElementById('seo-expert-service');
    let currentStep = 0;

    // Initialize Google Places Autocomplete
    initializeGooglePlaces();

    // Initialize form navigation
    initializeFormNavigation();

    // Initialize file uploads
    initializeFileUploads();

    // Initialize business hours
    initializeBusinessHours();

    function initializeGooglePlaces() {
        const businessNameInput = document.getElementById('business-name');
        const businessAutocomplete = new google.maps.places.Autocomplete(businessNameInput, {
            types: ['establishment'],
            componentRestrictions: { country: ['us'] },
            fields: ['formatted_address', 'geometry', 'name', 'address_components', 'website', 'formatted_phone_number']
        });

        businessAutocomplete.addListener('place_changed', function() {
            const place = businessAutocomplete.getPlace();
            if (!place.geometry) {
                debugLog('Place details not found');
                return;
            }
            fillFormFromPlace(place);
        });

        const addressInput = document.getElementById('address');
        const addressAutocomplete = new google.maps.places.Autocomplete(addressInput, {
            types: ['address'],
            componentRestrictions: { country: ['us'] }
        });

        addressAutocomplete.addListener('place_changed', function() {
            const place = addressAutocomplete.getPlace();
            if (!place.geometry) {
                debugLog('Address details not found');
                return;
            }
            fillAddressFields(place);
        });
    }

    function fillFormFromPlace(place) {
        debugLog('Filling form from place:', place);

        if (place.formatted_address) {
            document.getElementById('address').value = place.formatted_address;
        }

        fillAddressFields(place);

        if (place.website) {
            document.getElementById('website').value = place.website;
        }

        if (place.formatted_phone_number) {
            document.getElementById('phone').value = place.formatted_phone_number;
        }
    }

    function fillAddressFields(place) {
        debugLog('Filling address fields:', place);

        const fields = {
            street_number: '',
            route: '',
            locality: '',
            administrative_area_level_1: '',
            postal_code: '',
            latitude: '',
            longitude: ''
        };

        place.address_components.forEach(component => {
            const type = component.types[0];
            if (fields.hasOwnProperty(type)) {
                fields[type] = type === 'administrative_area_level_1' ? 
                    component.short_name : component.long_name;
            }
        });

        if (place.geometry) {
            fields.latitude = place.geometry.location.lat();
            fields.longitude = place.geometry.location.lng();
        }

        Object.keys(fields).forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                element.value = fields[field];
            }
        });
    }

    function calculateTotal() {
        const basePrice = 99.99;
        const seoExpertPrice = seoExpertCheckbox.checked ? 60 : 0;
        const total = basePrice + seoExpertPrice;
        
        const totalElement = document.querySelector('.total-price');
        if (totalElement) {
            totalElement.textContent = `Total: $${total.toFixed(2)}`;
        }
        return total;
    }

    seoExpertCheckbox.addEventListener('change', calculateTotal);

    function initializeFormNavigation() {
        nextBtn.addEventListener('click', () => {
            if (validateCurrentStep()) {
                currentStep++;
                updateStepDisplay();
            }
        });

        prevBtn.addEventListener('click', () => {
            currentStep--;
            updateStepDisplay();
        });

        form.addEventListener('submit', handleFormSubmission);
    }

    function updateStepDisplay() {
        sections.forEach((section, index) => {
            section.style.display = index === currentStep ? 'block' : 'none';
            section.classList.toggle('active', index === currentStep);
        });

        progressSteps.forEach((step, index) => {
            step.classList.toggle('active', index <= currentStep);
        });

        prevBtn.style.display = currentStep === 0 ? 'none' : 'block';
        nextBtn.style.display = currentStep === sections.length - 1 ? 'none' : 'block';

        if (currentStep === sections.length - 1) {
            updatePreview();
        }
    }

    async function handleFormSubmission(e) {
        e.preventDefault();
        debugLog('Form submission started');

        if (!validateCurrentStep()) {
            return;
        }

        const submitButton = document.getElementById('pay_now');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        try {
            const success = await handlePayment();
            if (success) {
                const formData = new FormData(form);
                
                const response = await fetch(ajax_object.ajax_url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('success', result.data.message);
                    setTimeout(() => {
                        window.location.href = result.data.redirect_url;
                    }, 2000);
                } else {
                    throw new Error(result.data.message);
                }
            }
        } catch (error) {
            debugLog('Submission error:', error);
            showMessage('error', error.message || 'An error occurred. Please try again.');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Pay Now';
        }
    }

    async function handlePayment() {
        try {
            const { error, paymentMethod } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
                billing_details: {
                    name: `${document.getElementById('first_name').value} ${document.getElementById('last_name').value}`,
                    email: document.getElementById('email').value,
                }
            });

            if (error) {
                jQuery('#payment_error').html(error.message);
                return false;
            }

            const response = await jQuery.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'process_stripe_payment',
                    payment_method_id: paymentMethod.id,
                    total: calculateTotal(),
                    seo_expert_service: seoExpertCheckbox.checked,
                    name: jQuery('#first_name').val() + ' ' + jQuery('#last_name').val(),
                    email: jQuery('#email').val(),
                }
            });

            if (response.success && response.data.clientSecret) {
                const result = await stripe.confirmCardPayment(response.data.clientSecret, {
                    payment_method: paymentMethod.id,
                });

                if (result.error) {
                    jQuery('#payment_error').html(result.error.message);
                    return false;
                } else if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
                    return true;
                }
            } else {
                jQuery('#payment_error').html(response.data.message || 'Payment failed. Please try again.');
                return false;
            }
        } catch (err) {
            jQuery('#payment_error').html('An error occurred while processing the payment.');
            return false;
        }
    }

    function validateCurrentStep() {
        debugLog('Validating step:', currentStep);
        let isValid = true;
        const currentSection = sections[currentStep];
        const inputs = currentSection.querySelectorAll('input[required], select[required], textarea[required]');

        currentSection.querySelectorAll('.error-message').forEach(el => el.remove());
        currentSection.querySelectorAll('.error').forEach(el => el.classList.remove('error'));

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                markError(input, 'This field is required');
            }
        });

        return isValid;
    }

    function markError(element, message) {
        element.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        element.parentNode.appendChild(errorDiv);
    }

    function initializeFileUploads() {
        const logoInput = document.getElementById('logo');
        const galleryInput = document.getElementById('gallery');
        
        setupDropZone('logo-dropzone', logoInput, 1, 2);
        setupDropZone('gallery-dropzone', galleryInput, 5, 5);
        
        logoInput.addEventListener('change', () => handleFileUpload(logoInput, 'logo-preview', 1, 2));
        galleryInput.addEventListener('change', () => handleFileUpload(galleryInput, 'gallery-preview', 5, 5));
    }

    function setupDropZone(dropzoneId, input, maxFiles, maxSize) {
        const dropzone = document.getElementById(dropzoneId);
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                dropzone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                dropzone.classList.remove('dragover');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            input.files = files;
            handleFileUpload(input, `${input.id}-preview`, maxFiles, maxSize);
        });
    }

    function handleFileUpload(input, previewId, maxFiles, maxSize) {
        const previewContainer = document.getElementById(previewId);
        const files = Array.from(input.files).slice(0, maxFiles);
        previewContainer.innerHTML = '';
        
        files.forEach(file => {
if (file.size > maxSize * 1024 * 1024) {
                showMessage('error', `File ${file.name} exceeds ${maxSize}MB limit`);
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'file-preview-item';
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <span class="filename">${file.name}</span>
                    <button type="button" class="remove-file" aria-label="Remove file">×</button>
                `;
                previewContainer.appendChild(preview);

                preview.querySelector('.remove-file').addEventListener('click', function() {
                    preview.remove();
                    const dt = new DataTransfer();
                    const remainingFiles = Array.from(input.files).filter(f => f !== file);
                    remainingFiles.forEach(f => dt.items.add(f));
                    input.files = dt.files;
                });
            };
            reader.readAsDataURL(file);
        });
    }

    function initializeBusinessHours() {
        const autoCopyCheckbox = document.getElementById('autoCopyHours');
        const presetButtons = document.querySelectorAll('.preset-btn');

        presetButtons.forEach(btn => {
            btn.addEventListener('click', () => applyHoursPreset(btn.dataset.preset));
        });

        autoCopyCheckbox.addEventListener('change', function() {
            if (this.checked) {
                copyMondayHours();
            }
        });
    }

    function applyHoursPreset(preset) {
        const days = document.querySelectorAll('.business-hours-day');
        
        days.forEach(day => {
            const dayName = day.dataset.day;
            const checkbox = day.querySelector('input[type="checkbox"]');
            const openTime = day.querySelector('input[name*="[open_time]"]');
            const closeTime = day.querySelector('input[name*="[close_time]"]');
            
            switch(preset) {
                case '24-7':
                    checkbox.checked = true;
                    openTime.value = '00:00';
                    closeTime.value = '23:59';
                    break;
                case 'weekdays':
                    const isWeekday = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'].includes(dayName);
                    checkbox.checked = isWeekday;
                    if (isWeekday) {
                        openTime.value = '09:00';
                        closeTime.value = '17:00';
                    } else {
                        openTime.value = '';
                        closeTime.value = '';
                    }
                    break;
            }
        });
    }

    function copyMondayHours() {
        const mondayHours = document.querySelector('[data-day="monday"]');
        const mondayOpen = mondayHours.querySelector('input[name*="[open_time]"]').value;
        const mondayClose = mondayHours.querySelector('input[name*="[close_time]"]').value;
        const mondayChecked = mondayHours.querySelector('input[type="checkbox"]').checked;

        document.querySelectorAll('.business-hours-day').forEach(day => {
            const dayName = day.dataset.day;
            if (dayName !== 'monday' && dayName !== 'saturday' && dayName !== 'sunday') {
                day.querySelector('input[type="checkbox"]').checked = mondayChecked;
                day.querySelector('input[name*="[open_time]"]').value = mondayOpen;
                day.querySelector('input[name*="[close_time]"]').value = mondayClose;
            }
        });
    }

    function updatePreview() {
        const preview = document.getElementById('listing-preview');
        preview.innerHTML = generatePreviewHTML();
    }

    function generatePreviewHTML() {
        const category = document.getElementById('category');
        let previewHtml = `
            <div class="preview-section">
                <h3>Basic Information</h3>
                <div class="preview-row">
                    <label>Business Name:</label>
                    <span>${document.getElementById('business-name').value || 'Not provided'}</span>
                </div>
                <div class="preview-row">
                    <label>Contact Name:</label>
                    <span>${document.getElementById('first_name').value} ${document.getElementById('last_name').value}</span>
                </div>
                <div class="preview-row">
                    <label>Address:</label>
                    <span>${document.getElementById('address').value || 'Not provided'}</span>
                </div>
                <div class="preview-row">
                    <label>Category:</label>
                    <span>${category.options[category.selectedIndex]?.text || 'Not selected'}</span>
                </div>
            </div>
            
            <div class="preview-section">
                <h3>Contact Information</h3>
                <div class="preview-row">
                    <label>Phone:</label>
                    <span>${document.getElementById('phone').value || 'Not provided'}</span>
                </div>
                <div class="preview-row">
                    <label>Email:</label>
                    <span>${document.getElementById('email').value || 'Not provided'}</span>
                </div>
                <div class="preview-row">
                    <label>Website:</label>
                    <span>${document.getElementById('website').value || 'Not provided'}</span>
                </div>
            </div>
            
            <div class="preview-section">
                <h3>Description</h3>
                <div class="preview-row">
                    <p>${document.getElementById('description').value || 'Not provided'}</p>
                </div>
            </div>
            
            <div class="preview-section">
                <h3>Business Hours</h3>
                ${generateHoursPreviewHTML()}
            </div>
            
            <div class="preview-section">
                <h3>Media</h3>
                <div class="preview-row">
                    <label>Logo:</label>
                    <span>${document.getElementById('logo').files.length ? document.getElementById('logo').files[0].name : 'No logo uploaded'}</span>
                </div>
                <div class="preview-row">
                    <label>Gallery:</label>
                    <span>${document.getElementById('gallery').files.length ? `${document.getElementById('gallery').files.length} images uploaded` : 'No images uploaded'}</span>
                </div>
            </div>`;

        if (seoExpertCheckbox.checked) {
            previewHtml += `
                <div class="preview-section">
                    <h3>Additional Services</h3>
                    <div class="preview-row">
                        <label>SEO Expert Setup:</label>
                        <span>Yes (+$60)</span>
                    </div>
                </div>`;
        }

        return previewHtml;
    }

    function generateHoursPreviewHTML() {
        let hoursHtml = '';
        document.querySelectorAll('.business-hours-day').forEach(day => {
            const dayName = day.dataset.day;
            const isOpen = day.querySelector('input[type="checkbox"]').checked;
            const openTime = day.querySelector('input[name*="[open_time]"]').value;
            const closeTime = day.querySelector('input[name*="[close_time]"]').value;

            hoursHtml += `
                <div class="preview-row">
                    <label>${dayName.charAt(0).toUpperCase() + dayName.slice(1)}:</label>
                    <span>${isOpen ? `${formatTime(openTime)} - ${formatTime(closeTime)}` : 'Closed'}</span>
                </div>
            `;
        });
        return hoursHtml;
    }

    function formatTime(time) {
        if (!time) return '';
        const [hours, minutes] = time.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const formattedHour = hour % 12 || 12;
        return `${formattedHour}:${minutes} ${ampm}`;
    }

    function showMessage(type, message) {
        const messageContainer = document.createElement('div');
        messageContainer.className = `form-message ${type}-message`;
        messageContainer.textContent = message;
        form.insertBefore(messageContainer, form.firstChild);
        setTimeout(() => messageContainer.remove(), 5000);
    }

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
    });

    // Initialize form
    updateStepDisplay();
    debugLog('Form initialization complete');
});
</script>

<?php get_footer(); ?>