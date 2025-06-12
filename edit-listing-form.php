<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) exit;

// Get the listing ID from the URL
$listing_id = isset($_GET['listing_id']) ? intval($_GET['listing_id']) : 0;

// Fetch the listing data
$listing = get_post($listing_id);

// Check if the current user is the author of the listing
if (!$listing || $listing->post_author != get_current_user_id()) {
    wp_die('You do not have permission to edit this listing.');
}

// Fetch custom field data
$first_name = get_field('first_name', $listing_id);
$last_name = get_field('last_name', $listing_id);
$business_name = get_the_title($listing_id);
$street_address = get_field('street_address', $listing_id);
$city = get_field('city', $listing_id);
$state = get_field('state', $listing_id);
$zip = get_field('zip_code', $listing_id);
$description = get_the_content(null, false, $listing_id);
$phone = get_field('phone_number', $listing_id);
$email = get_field('email', $listing_id);
$category = wp_get_post_terms($listing_id, 'business_category', array('fields' => 'ids'));
$subcategory = wp_get_post_terms($listing_id, 'business_subcategory', array('fields' => 'ids'));
$cuisine_type = wp_get_post_terms($listing_id, 'cuisine_type', array('fields' => 'ids'));
$listing_type = get_field('listing_type', $listing_id);
$website = get_field('website_link', $listing_id);
$logo = get_field('logo', $listing_id);
$gallery = get_field('photo_gallery', $listing_id);
$social_links = get_field('social_media_links', $listing_id);
$business_hours = get_field('business_hours', $listing_id);
$service_area = get_field('service_area', $listing_id);
$services_offered = get_field('services_offered', $listing_id);

// Call wp_head() to ensure styles are loaded
wp_head();

$nonce = wp_create_nonce('directory_listing_nonce');

// Get states from taxonomy
$states = get_terms(array(
    'taxonomy' => 'state',
    'hide_empty' => false,
));

// Get categories from taxonomy
$categories = get_terms(array(
    'taxonomy' => 'business_category',
    'hide_empty' => false,
));

// Get subcategories from taxonomy
$subcategories = get_terms(array(
    'taxonomy' => 'business_subcategory',
    'hide_empty' => false,
));

// Get cuisine types from taxonomy
$cuisine_types = get_terms(array(
    'taxonomy' => 'cuisine_type',
    'hide_empty' => false,
));
?>

<div class="directory-listing-form-container" aria-live="polite">
    <h2 id="form-title">Edit Your Business Listing</h2>

    <form id="edit-directory-listing-form" enctype="multipart/form-data" aria-labelledby="form-title">
        <input type="hidden" name="action" value="edit_directory_listing">
        <input type="hidden" name="directory_listing_nonce" value="<?php echo $nonce; ?>">
        <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>">
        <input type="hidden" name="listing_type" id="listing_type" value="<?php echo esc_attr($listing_type); ?>">

        <div class="form-progress-container">
            <div class="form-progress">
                <div class="progress-step active" data-step="1"><span>Basic Info</span></div>
                <div class="progress-step" data-step="2"><span>Categories</span></div>
                <div class="progress-step paid-only" data-step="3"><span>Services</span></div>
                <div class="progress-step paid-only" data-step="4"><span>Additional Info</span></div>
                <div class="progress-step" data-step="5"><span>Review</span></div>
                <div class="progress-bar">
                    <div class="progress-bar-fill"></div>
                </div>
            </div>
        </div>
        
        <!-- Step 1: Basic Information -->
        <fieldset class="form-section" data-step="1">
            <legend>Basic Information</legend>
            <div class="form-group name-fields">
                <label for="first-name">First Name: <span class="required">*</span></label>
                <input type="text" id="first-name" name="first_name" required aria-required="true" value="<?php echo esc_attr($first_name); ?>">
                <label for="last-name">Last Name: <span class="required">*</span></label>
                <input type="text" id="last-name" name="last_name" required aria-required="true" value="<?php echo esc_attr($last_name); ?>">
            </div>
            <div class="form-group">
                <label for="business-name">Business Name: <span class="required">*</span></label>
                <input type="text" id="business-name" name="business_name" required aria-required="true" value="<?php echo esc_attr($business_name); ?>">
            </div>
            <div class="form-group">
                <label for="street-address">Street Address: <span class="required">*</span></label>
                <input type="text" id="street-address" name="street_address" required aria-required="true" value="<?php echo esc_attr($street_address); ?>">
            </div>
            <div class="form-group">
                <label for="city">City: <span class="required">*</span></label>
                <input type="text" id="city" name="city" required aria-required="true" value="<?php echo esc_attr($city); ?>" list="city-list">
                <datalist id="city-list">
                    <!-- Options will be populated dynamically -->
                </datalist>
            </div>
            <div class="form-group">
                <label for="state">State: <span class="required">*</span></label>
                <select id="state" name="state" required aria-required="true">
                    <option value="">Select State</option>
                    <?php foreach ($states as $state_term) : ?>
                        <option value="<?php echo esc_attr($state_term->term_id); ?>" <?php selected($state_term->name, $state); ?>>
                            <?php echo esc_html($state_term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="zip">ZIP Code: <span class="required">*</span></label>
                <input type="text" id="zip" name="zip" required aria-required="true" pattern="[0-9]{5}" title="Please enter a valid 5-digit ZIP code" value="<?php echo esc_attr($zip); ?>">
            </div>
            <div class="form-group">
                <label for="description">Description: <span class="required">*</span></label>
                <textarea id="description" name="description" required aria-required="true"><?php echo esc_textarea($description); ?></textarea>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number: <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" required aria-required="true" value="<?php echo esc_attr($phone); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email Address: <span class="required">*</span></label>
                <input type="email" id="email" name="email" required aria-required="true" value="<?php echo esc_attr($email); ?>">
            </div>
        </fieldset>

        <!-- Step 2: Categories -->
        <fieldset class="form-section" data-step="2">
            <legend>Categories</legend>
            <div class="form-group">
                <label for="category">Category: <span class="required">*</span></label>
                <select id="category" name="category" required aria-required="true">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat) : ?>
                        <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected(in_array($cat->term_id, $category), true); ?>>
                            <?php echo esc_html($cat->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="subcategory">Subcategory:</label>
                <select id="subcategory" name="subcategory">
                    <option value="">Select Subcategory</option>
                    <?php foreach ($subcategories as $subcat) : ?>
                        <option value="<?php echo esc_attr($subcat->term_id); ?>" <?php selected(in_array($subcat->term_id, $subcategory), true); ?>>
                            <?php echo esc_html($subcat->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" id="cuisine-type-group" style="display: <?php echo in_array('restaurant', $category) ? 'block' : 'none'; ?>;">
                <label for="cuisine-type">Restaurant Type:</label>
                <select id="cuisine-type" name="cuisine_type">
                    <option value="">Select Restaurant Type</option>
                    <?php foreach ($cuisine_types as $cuisine) : ?>
                        <option value="<?php echo esc_attr($cuisine->term_id); ?>" <?php selected(in_array($cuisine->term_id, $cuisine_type), true); ?>>
                            <?php echo esc_html($cuisine->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </fieldset>

        <!-- Step 3: Services (Paid Only) -->
        <fieldset class="form-section paid-only" data-step="3">
            <legend>Services</legend>
            <div class="form-group">
                <label for="is-service-area-business">Is this a service business?</label>
                <select id="is-service-area-business" name="is_service_area_business">
                    <option value="">Please select</option>
                    <option value="yes" <?php selected(!empty($service_area), true); ?>>Yes</option>
                    <option value="no" <?php selected(empty($service_area), true); ?>>No</option>
                </select>
            </div>
            <div id="service-area-section" style="display: <?php echo !empty($service_area) ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label for="service-area-type">Service Area Type:</label>
                    <select id="service-area-type" name="service_area_type">
                        <option value="">Select service area type</option>
                        <option value="zip-codes" <?php selected($service_area['type'], 'zip-codes'); ?>>ZIP Codes</option>
                        <option value="cities" <?php selected($service_area['type'], 'cities'); ?>>Cities</option>
                        <option value="counties" <?php selected($service_area['type'], 'counties'); ?>>Counties</option>
                    </select>
                </div>
                <div id="service-area-input" class="form-group" style="display: <?php echo !empty($service_area['type']) ? 'block' : 'none'; ?>;">
                    <label for="service-area-list">Service Areas (comma-separated):</label>
                    <input type="text" id="service-area-list" name="service_area_list" value="<?php echo esc_attr($service_area['areas']); ?>">
                </div>
            </div>
            <div id="services-section">
                <h4>Services Offered</h4>
                <div id="services-container">
                    <?php if (!empty($services_offered)) : ?>
                        <?php foreach ($services_offered as $index => $service) : ?>
                            <div class="service-item">
                                <input type="text" name="services[]" value="<?php echo esc_attr($service['service_name']); ?>" placeholder="Service Name" required>
                                <textarea name="service_descriptions[]" placeholder="Service Description"><?php echo esc_textarea($service['service_description']); ?></textarea>
                                <button type="button" class="remove-service">Remove</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" id="add-service">Add Service</button>
            </div>
        </fieldset>

        <!-- Step 4: Additional Information (Paid Only) -->
        <fieldset class="form-section paid-only" data-step="4">
            <legend>Additional Information</legend>
            <div class="form-group">
                <label for="website">Website URL:</label>
                <input type="url" id="website" name="website" value="<?php echo esc_url($website); ?>">
            </div>
            <div class="form-group">
                <label for="logo">Logo (max 2MB):</label>
                <?php if ($logo) : ?>
                    <img src="<?php echo esc_url($logo['url']); ?>" alt="Current Logo" style="max-width: 200px;">
                    <br>
                    <label>
                        <input type="checkbox" name="remove_logo" value="1"> Remove current logo
                    </label>
                <?php endif; ?>
                <input type="file" id="logo" name="logo" accept="image/*">
                <div id="logo-dropzone" class="dropzone">Drag and drop your logo here</div>
            </div>
            <div class="form-group">
                <label for="gallery">Photo Gallery (max 5 images, 5MB each):</label>
                <?php if ($gallery) : ?>
                    <div class="current-gallery">
                        <?php foreach ($gallery as $image) : ?>
                            <img src="<?php echo esc_url($image['sizes']['thumbnail']); ?>" alt="Gallery Image">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple>
                <div id="gallery-dropzone" class="dropzone">Drag and drop your gallery images here</div>
            </div>
            <div class="form-group">
                <label for="social-links">Social Media and Directory Links:</label>
                <div id="links-container">
                    <?php if (!empty($social_links)) : ?>
                        <?php foreach ($social_links as $platform => $url) : ?>
                            <?php if ($platform !== 'other_urls') : ?>
                                <div class="link-inputs">
  <input type="url" name="links[<?php echo esc_attr($platform); ?>][url]" value="<?php echo esc_url($url); ?>" placeholder="Link URL">
                                    <input type="text" name="links[<?php echo esc_attr($platform); ?>][label]" value="<?php echo esc_attr(ucfirst($platform)); ?>" placeholder="Link Label">
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (!empty($social_links['other_urls'])) : ?>
                            <?php foreach ($social_links['other_urls'] as $index => $other_url) : ?>
                                <div class="link-inputs">
                                    <input type="url" name="links[other][<?php echo $index; ?>][url]" value="<?php echo esc_url($other_url['url']); ?>" placeholder="Link URL">
                                    <input type="text" name="links[other][<?php echo $index; ?>][label]" value="<?php echo esc_attr($other_url['label']); ?>" placeholder="Link Label">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <button type="button" id="add-link">Add Another Link</button>
            </div>
            <div class="form-group">
                <h4>Business Hours</h4>
                <div id="business-hours-container">
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach ($days as $day) :
                        $day_lower = strtolower($day);
                        $day_hours = null;
                        if ($business_hours) {
                            foreach ($business_hours as $hours) {
                                if ($hours['day_of_week'] === $day_lower) {
                                    $day_hours = $hours;
                                    break;
                                }
                            }
                        }
                    ?>
                        <div class="business-hours-day">
                            <div class="day-select">
                                <label>
                                    <input type="checkbox" name="business_hours[<?php echo $day_lower; ?>][open]" value="1" <?php checked(!empty($day_hours) && $day_hours['is_open']); ?>>
                                    <span><?php echo $day; ?></span>
                                </label>
                            </div>
                            <div class="hours-inputs">
                                <input type="time" name="business_hours[<?php echo $day_lower; ?>][open_time]" value="<?php echo esc_attr($day_hours['opening_time'] ?? ''); ?>">
                                <span class="time-separator">to</span>
                                <input type="time" name="business_hours[<?php echo $day_lower; ?>][close_time]" value="<?php echo esc_attr($day_hours['closing_time'] ?? ''); ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </fieldset>

        <!-- Step 5: Review -->
        <fieldset class="form-section" data-step="5">
            <legend>Review Your Listing</legend>
            <div id="listing-preview">
                <!-- Preview content will be dynamically inserted here -->
            </div>
        </fieldset>

        <!-- Terms and Conditions (outside of fieldsets, always visible) -->
        <div class="form-group terms-conditions">
            <label for="terms-and-conditions">
                <input type="checkbox" id="terms-and-conditions" name="terms_and_conditions" required aria-required="true" checked>
                I agree to the <a href="#" target="_blank">Terms and Conditions</a>
            </label>
        </div>

        <div class="form-navigation">
            <button type="button" id="prev-step">Previous</button>
            <button type="button" id="next-step">Next</button>
            <button type="submit" id="update-listing">Update Listing</button>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    var currentStep = 1;
    var totalSteps = $('#listing_type').val() === 'paid' ? 5 : 3;

    function showStep(step) {
        $('.form-section').hide();
        $('.form-section[data-step="' + step + '"]').show();
        updateProgressBar();
    }

    function updateProgressBar() {
        var progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        $('.progress-bar-fill').css('width', progress + '%');
    }

    function togglePaidSections() {
        var listingType = $('#listing_type').val();
        $('.paid-only').toggle(listingType === 'paid');
        totalSteps = listingType === 'paid' ? 5 : 3;
        updateProgressBar();
    }

    togglePaidSections();

    $('#next-step').click(function() {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    });

    $('#prev-step').click(function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    $('#category').on('change', function() {
        var isRestaurant = $(this).find(':selected').text().toLowerCase().includes('restaurant');
        $('#cuisine-type-group').toggle(isRestaurant);
    });

    $('#is-service-area-business').on('change', function() {
        $('#service-area-section').toggle($(this).val() === 'yes');
    });

    $('#service-area-type').on('change', function() {
        $('#service-area-input').toggle($(this).val() !== '');
    });

    $('#add-service').on('click', function() {
        var serviceHtml = '<div class="service-item">' +
            '<input type="text" name="services[]" placeholder="Service Name" required>' +
            '<textarea name="service_descriptions[]" placeholder="Service Description"></textarea>' +
            '<button type="button" class="remove-service">Remove</button>' +
            '</div>';
        $('#services-container').append(serviceHtml);
    });

    $(document).on('click', '.remove-service', function() {
        $(this).closest('.service-item').remove();
    });

    $('#add-link').on('click', function() {
        var linkCount = $('#links-container .link-inputs').length;
        var newLinkHtml = '<div class="link-inputs">' +
            '<input type="url" name="links[other][' + linkCount + '][url]" placeholder="Link URL">' +
            '<input type="text" name="links[other][' + linkCount + '][label]" placeholder="Link Label">' +
            '<button type="button" class="remove-link">Remove</button>' +
            '</div>';
        $('#links-container').append(newLinkHtml);
    });

    $(document).on('click', '.remove-link', function() {
        $(this).closest('.link-inputs').remove();
    });

    $('#edit-directory-listing-form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: directory_listings_form.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    if (response.data.redirect_url) {
                        window.location.href = response.data.redirect_url;
                    } else {
                        alert(response.data.message);
                        // Optionally reload the page
                        // window.location.reload();
                    }
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Initialize the form
    showStep(currentStep);
});
</script>

<?php
// Call wp_footer() to ensure scripts are loaded
wp_footer();
?>                         
                                    