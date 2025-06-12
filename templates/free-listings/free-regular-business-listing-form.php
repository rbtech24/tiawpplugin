<?php
/*
Template Name: Free Regular Business Listing Form
*/

get_header();
?>

<!-- Google Places API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAK1WEBfkwgWn6y2rid33ZDWHXOCPcuuOw&libraries=places&fields=formatted_address,geometry,name,address_components,formatted_phone_number"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- JavaScript Variables -->
<script>
    var ajax_object = {
        ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>'
    };
</script>

<div class="directory-listing-form-container">
<!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Add Your Free Business Listing Details</h1>
            <p class="hero-text"></p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <form id="directory-listing-form" method="post" data-form-type="free-business" enctype="multipart/form-data">
            <?php wp_nonce_field('directory_listing_nonce', 'directory_listing_nonce'); ?>
            <input type="hidden" name="listing_type" value="free">
            <input type="hidden" name="action" value="submit_directory_listing">

            <!-- Hidden address components fields -->
            <input type="hidden" id="street_number" name="street_number">
            <input type="hidden" id="route" name="route">
            <input type="hidden" id="locality" name="locality">
            <input type="hidden" id="administrative_area_level_1" name="administrative_area_level_1">
            <input type="hidden" id="postal_code" name="postal_code">
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            <input type="hidden" id="place_id" name="place_id">

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
                    <span>Review</span>
                </div>
            </div>

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
                        <label for="description">Business Description <span class="required">*</span></label>
                        <textarea id="description" name="description" rows="5" required></textarea>
                        <span class="character-count">0/500 characters</span>
                    </div>
                </div>
            </div>

            <!-- Step 3: Review -->
            <div class="form-section" data-step="3">
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
                    Submit Free Listing <i class="fas fa-check"></i>
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

.hero-content {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-shadow: none;
    letter-spacing: -1px;
}

.hero-text {
    font-size: 1.25rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
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
    font-size: 18px;
    color: var(--gray-600);
    position: relative;
    z-index: 1;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: var(--primary);
    color: var(--white);
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2);
}

.step span {
    font-size: 14px;
    color: var(--gray-600);
    font-weight: 500;
}

.step.active span {
    color: var(--primary);
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

.form-section h2 {
    font-size: 24px;
    color: var(--navy);
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--gray-200);
    font-weight: 600;
}

/* Form Fields */
.form-grid {
    display: grid;
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
    font-size: 14px;
}

.required {
    color: var(--red);
    margin-left: 4px;
}

.field-hint {
    display: block;
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 4px;
}

input[type="text"],
input[type="email"],
input[type="tel"],
input[type="url"],
input[type="time"],
select,
textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--gray-300);
    border-radius: var(--rounded);
    font-size: 16px;
    transition: all 0.3s ease;
    color: var(--gray-800);
}

input:focus,
select:focus,
textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* Business Hours */
.business-hours-section {
    background: var(--gray-50);
    padding: 30px;
    border-radius: var(--rounded-lg);
    margin-top: 20px;
}

.hours-header {
    margin-bottom: 24px;
}

.business-hours-day {
    background: var(--white);
    padding: 20px;
    border-radius: var(--rounded);
    margin-bottom: 8px;
    border: 1px solid var(--gray-200);
    transition: all 0.3s ease;
}

.business-hours-day:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-md);
}

.day-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.hours-inputs {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 16px;
    align-items: center;
}

/* Buttons */
.btn-primary,
.btn-secondary {
    padding: 12px 24px;
    border-radius: var(--rounded);
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
}

.btn-primary {
    background: var(--red);
    color: var(--white);
    border: none;
    box-shadow: 0 2px 4px rgba(205, 19, 57, 0.2);
}

.btn-primary:hover {
    background: #b11131;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(205, 19, 57, 0.3);
}

.btn-secondary {
    background: var(--gray-100);
    color: var(--navy);
    border: 1px solid var(--gray-300);
}

.btn-secondary:hover {
    background: var(--gray-200);
    transform: translateY(-1px);
}

/* File Uploads */
.file-upload-container {
    border: 2px dashed var(--gray-300);
    padding: 30px;
    text-align: center;
    border-radius: var(--rounded-lg);
    background: var(--gray-50);
    transition: all 0.3s ease;
}

.file-upload-container:hover {
    border-color: var(--primary);
    background: var(--gray-100);
}

.image-preview {
    margin-top: 16px;
    padding: 16px;
    background: var(--white);
    border-radius: var(--rounded);
    border: 1px solid var(--gray-200);
}

/* Form Navigation */
.form-navigation {
    display: flex;
    justify-content: space-between;
    margin-top: 40px;
    gap: 20px;
}

/* Error States */
.error {
    border-color: var(--danger) !important;
}

.error-message {
    color: var(--danger);
    font-size: 12px;
    margin-top: 4px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-section {
        padding: 8rem 1rem 4rem;  /* Increased top padding */
        margin-top: -80px;  /* Adjusted margin-top */
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

    .hours-inputs {
        grid-template-columns: 1fr;
        gap: 8px;
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

/* Additional Components */
.social-link-group {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 12px;
    margin-bottom: 12px;
    align-items: start;
}

.place-not-found-message {
    margin-top: 8px;
    padding: 8px;
    background: var(--gray-50);
    border-radius: var(--rounded);
    font-size: 14px;
    color: var(--gray-600);
}

.payment-section {
    margin-top: 30px;
    padding: 20px;
    background: var(--gray-50);
    border-radius: var(--rounded-lg);
    border: 1px solid var(--gray-200);
}

/* Preview Section */
.listing-preview {
    background: var(--gray-50);
    padding: 24px;
    border-radius: var(--rounded-lg);
    border: 1px solid var(--gray-200);
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

    // Initialize form elements
    const form = document.getElementById('directory-listing-form');
    const sections = document.querySelectorAll('.form-section');
    const progressSteps = document.querySelectorAll('.step');
    const prevBtn = document.getElementById('prev-step');
    const nextBtn = document.getElementById('next-step');
    const submitBtn = document.getElementById('submit-listing');
    let currentStep = 0;

    debugLog('Form initialization started');

    // Initialize Google Places for business name
    const businessNameInput = document.getElementById('business-name');
    const businessAutocomplete = new google.maps.places.Autocomplete(businessNameInput, {
        types: ['establishment'],
        componentRestrictions: { country: ['us'] },
        fields: ['formatted_address', 'geometry', 'name', 'address_components', 'formatted_phone_number', 'place_id']
    });

    businessAutocomplete.addListener('place_changed', function() {
        const place = businessAutocomplete.getPlace();
        debugLog('Place selected:', place);
        
        if (!place.geometry) {
            debugLog('Place details not found');
            return;
        }

        fillFormFromPlace(place);
    });

    // Initialize address autocomplete
    const addressInput = document.getElementById('address');
    const addressAutocomplete = new google.maps.places.Autocomplete(addressInput, {
        types: ['address'],
        componentRestrictions: { country: ['us'] },
        fields: ['formatted_address', 'geometry', 'address_components', 'place_id']
    });

    addressAutocomplete.addListener('place_changed', function() {
        const place = addressAutocomplete.getPlace();
        debugLog('Address selected:', place);
        
        if (!place.geometry) {
            debugLog('Address details not found');
            return;
        }

        fillAddressFields(place);
    });

    function fillFormFromPlace(place) {
        debugLog('Filling form from place:', place);

        // Set business name
        if (place.name) {
            businessNameInput.value = place.name;
        }

        // Set address
        if (place.formatted_address) {
            addressInput.value = place.formatted_address;
        }

        // Fill address components
        fillAddressFields(place);

        // Set phone if available
        if (place.formatted_phone_number) {
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.value = place.formatted_phone_number;
            }
        }

        // Store place ID
        const placeIdInput = document.getElementById('place_id');
        if (placeIdInput && place.place_id) {
            placeIdInput.value = place.place_id;
        }
    }

    function fillAddressFields(place) {
        debugLog('Filling address fields:', place);

        // Reset fields
        const fields = {
            street_number: '',
            route: '',
            locality: '',
            administrative_area_level_1: '',
            postal_code: '',
            latitude: '',
            longitude: ''
        };

        // Fill in address components
        place.address_components.forEach(component => {
            const type = component.types[0];
            if (fields.hasOwnProperty(type)) {
                fields[type] = type === 'administrative_area_level_1' ? 
                    component.short_name : component.long_name;
                debugLog(`Setting ${type} to:`, fields[type]);
            }
        });

        // Set coordinates
        if (place.geometry && place.geometry.location) {
            fields.latitude = place.geometry.location.lat();
            fields.longitude = place.geometry.location.lng();
        }

        // Update form fields
        Object.keys(fields).forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                element.value = fields[field];
            }
        });
    }

    // Form Step Navigation
    function updateStepDisplay() {
        debugLog('Updating step display to:', currentStep);
        
        sections.forEach((section, index) => {
            section.style.display = index === currentStep ? 'block' : 'none';
            section.classList.toggle('active', index === currentStep);
        });

        progressSteps.forEach((step, index) => {
            step.classList.toggle('active', index <= currentStep);
        });

        prevBtn.style.display = currentStep === 0 ? 'none' : 'block';
        nextBtn.style.display = currentStep === sections.length - 1 ? 'none' : 'block';
        submitBtn.style.display = currentStep === sections.length - 1 ? 'block' : 'none';

        if (currentStep === sections.length - 1) {
            updatePreview();
        }
    }

    function validateCurrentStep() {
        debugLog('Validating step:', currentStep);
        let isValid = true;
        const currentSection = sections[currentStep];
        const inputs = currentSection.querySelectorAll('input[required], select[required], textarea[required]');

        // Clear previous errors
        currentSection.querySelectorAll('.error-message').forEach(el => el.remove());
        currentSection.querySelectorAll('.error').forEach(el => el.classList.remove('error'));

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                markError(input, 'This field is required');
            } else if (input.id === 'description') {
                // Check for URLs in the description
                const urlRegex = /(https?:\/\/|www\.|ftp:\/\/|mailto:)?[^\s]+(\.[^\s]+)+/gi;
                if (urlRegex.test(input.value)) {
                    isValid = false;
                    markError(input, 'URLs are not allowed in the description.');
                }
            }

            if (input.type === 'email' && input.value) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(input.value)) {
                    isValid = false;
                    markError(input, 'Please enter a valid email address');
                }
            }

            if (input.id === 'phone' && input.value) {
                const phonePattern = /^\(\d{3}\) \d{3}-\d{4}$/;
                if (!phonePattern.test(input.value)) {
                    isValid = false;
                    markError(input, 'Please enter a valid phone number');
                }
            }
        });

        debugLog('Validation result:', isValid);
        return isValid;
    }

    function markError(element, message) {
        element.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        element.parentNode.appendChild(errorDiv);
    }

    // Event Listeners
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

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    }

    // Form Submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        debugLog('Form submission started');

        if (!validateCurrentStep()) {
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        const formData = new FormData(this);
        formData.append('action', 'submit_directory_listing');
        formData.append('listing_type', 'free');

        // Add Google Places data
        const fieldsToAdd = [
            'administrative_area_level_1',
            'locality',
            'postal_code',
            'street_number',
            'route',
            'latitude',
            'longitude',
            'place_id'
        ];

        fieldsToAdd.forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                formData.append(field, element.value);
            }
        });

        try {
            const response = await fetch(ajax_object.ajax_url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            debugLog('Server response:', result);

            if (result.success) {
                showMessage('success', result.data.message);
                setTimeout(() => {
                    window.location.href = result.data.redirect_url;
                }, 2000);
            } else {
                showMessage('error', result.data.message);
            }
        } catch (error) {
            debugLog('Submission error:', error);
            showMessage('error', 'An error occurred. Please try again later.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Free Listing <i class="fas fa-check"></i>';
        }
    });

    function showMessage(type, message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `form-message ${type}-message`;
        messageDiv.textContent = message;
        form.insertBefore(messageDiv, form.firstChild);
        setTimeout(() => messageDiv.remove(), 5000);
    }

    function updatePreview() {
        const preview = document.getElementById('listing-preview');
        const category = document.getElementById('category');
        
        preview.innerHTML = `
            <div class="preview-section">
                <h3>Basic Information</h3>
                <div class="preview-row">
                    <label>Business Name:</label>
                    <span>${document.getElementById('business-name').value || 'Not provided'}</span>
                </div>
                <div class="preview-row">
                    <label>Address:</label>
                    <span>${document.getElementById('address').value || 'Not provided'}</span>
                </div>
                <div class="preview-row">
                    <label>Category:</label>
                    <span>${category.options[category.selectedIndex]?.text || 'Not selected'}</span>
                </div>
                <div class="preview-row">
                    <label>Phone:</label>
                    <span>${document.getElementById('phone').value || 'Not provided'}</span>
                </div>
                <div class="preview-row">
                    <label>Email:</label>
                    <span>${document.getElementById('email').value || 'Not provided'}</span>
                </div>
                <div class="preview-row">
                    <label>Description:</label>
                    <span>${document.getElementById('description').value || 'Not provided'}</span>
                </div>
            </div>
        `;
    }

    // Initialize form
    updateStepDisplay();
    debugLog('Form initialization complete');
});
</script>
<?php get_footer(); ?>
