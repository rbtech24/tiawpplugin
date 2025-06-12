<?php
/*
Template Name: Paid Restaurant Listing Form
*/

get_header();
?>

<!-- Include Google Places API and Font Awesome -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAK1WEBfkwgWn6y2rid33ZDWHXOCPcuuOw&libraries=places&fields=formatted_address,geometry,name,address_components,formatted_phone_number"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="directory-listing-form-container">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Add Your Premium Restaurant Listing</h1>
            <p class="hero-text"></p>
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
                <span>Details</span>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <span>Menu & Photos</span>
            </div>
            <div class="step" data-step="4">
                <div class="step-number">4</div>
                <span>Hours & Features</span>
            </div>
            <div class="step" data-step="5">
                <div class="step-number">5</div>
                <span>Review</span>
            </div>
        </div>

        <!-- Form -->
        <form id="directory-listing-form" method="post" enctype="multipart/form-data">
            <!-- Step 1: Basic Info -->
            <div class="form-section active" data-step="1">
                <h2>Basic Information</h2>
                <div class="form-grid">
                    <div class="form-field">
                        <label for="restaurant-name">Restaurant Name <span class="required">*</span></label>
                        <input type="text" id="restaurant-name" name="restaurant_name" required>
                    </div>

                    <div class="form-field">
                        <label for="address">Restaurant Address <span class="required">*</span></label>
                        <input type="text" id="address" name="address" required>
                        <!-- Hidden address components -->
                        <input type="hidden" id="street_number" name="street_number">
                        <input type="hidden" id="route" name="route">
                        <input type="hidden" id="locality" name="locality">
                        <input type="hidden" id="administrative_area_level_1" name="state">
                        <input type="hidden" id="postal_code" name="postal_code">
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                    </div>

                    <div class="form-field">
                        <label for="phone">Phone Number <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" required>
                        <span class="field-hint">Format: (XXX) XXX-XXXX</span>
                    </div>

                    <div class="form-field">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required>
                        <span class="field-hint">For account verification and notifications</span>
                    </div>

                    <div class="form-field">
                        <label for="website">Website URL</label>
                        <input type="url" id="website" name="website" placeholder="https://">
                    </div>
                </div>
            </div>

            <!-- Step 2: Restaurant Details -->
            <div class="form-section" data-step="2">
                <h2>Restaurant Details</h2>
                <div class="form-grid">
                    <div class="form-field">
                        <label for="cuisine-type">Cuisine Type <span class="required">*</span></label>
                        <select id="cuisine-type" name="cuisine_type" required>
                            <option value="">Select Cuisine Type</option>
                            <?php
                            $cuisine_types = get_terms(array(
                                'taxonomy' => 'cuisine',
                                'hide_empty' => false,
                            ));
                            foreach ($cuisine_types as $cuisine) : ?>
                                <option value="<?php echo esc_attr($cuisine->term_id); ?>">
                                    <?php echo esc_html($cuisine->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-field">
                        <label for="price-range">Price Range <span class="required">*</span></label>
                        <select id="price-range" name="price_range" required>
                            <option value="">Select Price Range</option>
                            <option value="$">$ (Under $10)</option>
                            <option value="$$">$$ ($11-$30)</option>
                            <option value="$$$">$$$ ($31-$60)</option>
                            <option value="$$$$">$$$$ (Over $60)</option>
                        </select>
                    </div>

                    <div class="form-field full-width">
                        <label for="description">Restaurant Description <span class="required">*</span></label>
                        <textarea id="description" name="description" rows="5" required></textarea>
                        <span class="character-count">0/500 characters</span>
                    </div>

                    <div class="form-field">
                        <label>Service Options</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="service_options[]" value="dine_in"> Dine-in</label>
                            <label><input type="checkbox" name="service_options[]" value="takeout"> Takeout</label>
                            <label><input type="checkbox" name="service_options[]" value="delivery"> Delivery</label>
                            <label><input type="checkbox" name="service_options[]" value="catering"> Catering</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Menu & Photos -->
            <div class="form-section" data-step="3">
                <h2>Menu & Photos</h2>
                <div class="form-grid">
                    <div class="form-field">
                        <label for="menu-pdf">Menu PDF</label>
                        <div class="file-upload-container" id="menu-pdf-dropzone">
                            <i class="fas fa-file-pdf"></i>
                            <p>Upload your menu (PDF format)</p>
                            <input type="file" id="menu-pdf" name="menu_pdf" accept=".pdf">
                        </div>
                        <span class="field-hint">Maximum file size: 5MB</span>
                    </div>

                    <div class="form-field">
                        <label for="menu-url">Online Menu URL</label>
                        <input type="url" id="menu-url" name="menu_url" placeholder="https://">
                    </div>

                    <div class="form-field">
                        <label for="logo">Restaurant Logo</label>
                        <div class="file-upload-container" id="logo-dropzone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag & drop logo or click to browse</p>
                            <input type="file" id="logo" name="logo" accept="image/*">
                        </div>
                        <div id="logo-preview" class="file-preview"></div>
                        <span class="field-hint">Maximum file size: 2MB</span>
                    </div>

                    <div class="form-field">
                        <label for="gallery">Photo Gallery</label>
                        <div class="file-upload-container" id="gallery-dropzone">
                            <i class="fas fa-images"></i>
                            <p>Upload up to 5 restaurant photos</p>
                            <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple>
                        </div>
                        <div id="gallery-preview" class="file-preview"></div>
                        <span class="field-hint">Maximum 5 images, 5MB each</span>
                    </div>
                </div>
            </div>

            <!-- Step 4: Hours & Features -->
            <div class="form-section" data-step="4">
                <h2>Hours & Additional Features</h2>
                
                <!-- Business Hours Section -->
                <div class="business-hours-section">
                    <h3>Business Hours</h3>
                    <div class="hours-header">
                        <div class="hours-presets">
                            <button type="button" class="btn-secondary preset-btn" data-preset="24-7">
                                <i class="fas fa-clock"></i> Set as 24/7
                            </button>
                            <button type="button" class="btn-secondary preset-btn" data-preset="weekdays">
                                <i class="fas fa-business-time"></i> Standard Hours
                            </button>
                        </div>
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

                <!-- Additional Features -->
                <div class="form-grid">
                    <div class="form-field">
                        <label for="reservation-url">Reservation URL</label>
                        <input type="url" id="reservation-url" name="reservation_url" placeholder="https://">
                    </div>

                    <div class="form-field">
                        <label for="ordering-url">Online Ordering URL</label>
                        <input type="url" id="ordering-url" name="ordering_url" placeholder="https://">
                    </div>

                    <div class="form-field">
                        <label>Features</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="features[]" value="wifi"> Free WiFi</label>
                            <label><input type="checkbox" name="features[]" value="parking"> Parking Available</label>
                            <label><input type="checkbox" name="features[]" value="outdoor"> Outdoor Seating</label>
                            <label><input type="checkbox" name="features[]" value="tv"> TV</label>
                            <label><input type="checkbox" name="features[]" value="wheelchair"> Wheelchair Accessible</label>
                            <label><input type="checkbox" name="features[]" value="bar"> Full Bar</label>
                        </div>
                    </div>

                    <div class="form-field">
                    
                    <div class="form-field">
                        <label for="card-element">Credit or Debit Card <span class="required">*</span></label>
                        <div id="card-element" class="stripe-input"></div>
                        <div id="card-errors" class="error-message" role="alert"></div>
                    </div>
                        <!--label>Payment Options</label>
                        
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="payment[]" value="credit"> Credit Cards</label>
                            <label><input type="checkbox" name="payment[]" value="debit"> Debit Cards</label>
                            <label><input type="checkbox" name="payment[]" value="cash"> Cash Only</label>
                            <label><input type="checkbox" name="payment[]" value="digital"> Digital Payments</label>
                        </div-->
                    </div>
                </div>
            </div>

            <!-- Step 5: Review -->
            <div class="form-section" data-step="5">
                <h2>Review Your Listing</h2>
                <div id="listing-preview" class="listing-preview"></div>
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
                    Submit Premium Listing <i class="fas fa-check"></i>
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
<!-- Stripe -->
<script src="https://js.stripe.com/v3/"></script>

 <script>
document.addEventListener('DOMContentLoaded', function() {
    
    
    // Initialize Stripe
     const stripe = Stripe('pk_test_gaeFXMw78Ee7fDWbXhwUJfbY');  // Replace with your actual publishable key
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
    let currentStep = 0;

    // Initialize Google Places Autocomplete
    const businessNameInput = document.getElementById('business-name');
    const businessAutocomplete = new google.maps.places.Autocomplete(businessNameInput, {
        types: ['establishment'],
        componentRestrictions: { country: ['us'] },
        fields: ['address_components', 'formatted_address', 'geometry', 'name', 'website', 'formatted_phone_number']
    });

    businessAutocomplete.addListener('place_changed', function() {
        const place = businessAutocomplete.getPlace();
        if (!place.geometry) {
            return;
        }

        let formattedAddress = formatAddress(place);
        document.getElementById('address').value = formattedAddress;
        fillAddressFields(place);
        
        if (place.website) {
            document.getElementById('website').value = place.website;
        }
        
        if (place.formatted_phone_number) {
            document.getElementById('phone').value = place.formatted_phone_number;
        }
    });

    // Address autocomplete
    const addressInput = document.getElementById('address');
    const addressAutocomplete = new google.maps.places.Autocomplete(addressInput, {
        componentRestrictions: { country: ['us'] },
        fields: ['address_components', 'formatted_address', 'geometry']
    });

    addressAutocomplete.addListener('place_changed', function() {
        const place = addressAutocomplete.getPlace();
        if (!place.geometry) {
            return;
        }
        
        let formattedAddress = formatAddress(place);
        addressInput.value = formattedAddress;
        fillAddressFields(place);
    });

    // Format address without USA
    function formatAddress(place) {
        let addressComponents = {
            street_number: '',
            route: '',
            locality: '',
            administrative_area_level_1: '',
            postal_code: ''
        };

        place.address_components.forEach(component => {
            const type = component.types[0];
            if (addressComponents.hasOwnProperty(type)) {
                addressComponents[type] = component.long_name;
            }
        });

        let addressParts = [];
        
        // Build street address
        if (addressComponents.street_number && addressComponents.route) {
            addressParts.push(`${addressComponents.street_number} ${addressComponents.route}`);
        }
        
        // Add city
        if (addressComponents.locality) {
            addressParts.push(addressComponents.locality);
        }
        
        // Add state
        if (addressComponents.administrative_area_level_1) {
            addressParts.push(addressComponents.administrative_area_level_1);
        }
        
        // Add zip code
        if (addressComponents.postal_code) {
            addressParts.push(addressComponents.postal_code);
        }

        return addressParts.join(', ');
    }

    function fillAddressFields(place) {
        // Clear existing values
        document.getElementById('street_number').value = '';
        document.getElementById('route').value = '';
        document.getElementById('locality').value = '';
        document.getElementById('administrative_area_level_1').value = '';
        document.getElementById('postal_code').value = '';
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        
        // Fill address components
        place.address_components.forEach(component => {
            const type = component.types[0];
            if (document.getElementById(type)) {
                document.getElementById(type).value = 
                    type === 'administrative_area_level_1' ? component.short_name : component.long_name;
            }
        });

        // Save coordinates
        if (place.geometry) {
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();
        }
    }

    // Navigation Functions
    function showStep(stepIndex) {
        sections.forEach(section => {
            section.classList.remove('active');
        });
        
        sections[stepIndex].classList.add('active');

        progressSteps.forEach((step, index) => {
            if (index <= stepIndex) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        prevBtn.style.display = stepIndex === 0 ? 'none' : 'block';
        nextBtn.style.display = stepIndex === sections.length - 1 ? 'none' : 'block';
        submitBtn.style.display = stepIndex === sections.length - 1 ? 'block' : 'none';

        if (stepIndex === sections.length - 1) {
            updatePreview();
        }
    }

    function validateStep(stepIndex) {
        const currentSection = sections[stepIndex];
        const requiredInputs = currentSection.querySelectorAll('[required]');
        let isValid = true;

        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('error');
                
                if (!input.nextElementSibling?.classList.contains('error-message')) {
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'error-message';
                    errorMsg.textContent = 'This field is required';
                    input.parentNode.insertBefore(errorMsg, input.nextSibling);
                }
            } else {
                input.classList.remove('error');
                const errorMsg = input.nextElementSibling;
                if (errorMsg?.classList.contains('error-message')) {
                    errorMsg.remove();
                }
            }
        });

        return isValid;
    }

    // Navigation Event Listeners
    nextBtn.addEventListener('click', function() {
         
          
             // Additional validation for specific steps
        if (currentStep === 3) { // Payment step
           var ajaxUrl = '<?php echo admin_url("admin-ajax.php"); ?>';
               jQuery.ajax({
    url: ajaxUrl,
    type: 'POST',
    data: {
        action: 'process_stripe_payment',
      //  security: my_ajax_object.nonce, // Include nonce for security
        total: 99.99
    },
    success: function(response) {
         //console.log('Client Secret:', response.data.clientSecret); // Handle success
         
         if(response.data.clientSecret){
            return isValid;   
         }
    },
    error: function(error) {
       const cardError = document.getElementById('card-errors');
            if (cardError.textContent) {
                isValid = false;
            }
    }
});

}
           
           
        if (validateStep(currentStep)) {
            currentStep = Math.min(currentStep + 1, sections.length - 1);
            showStep(currentStep);
        }
        
           
     
    });

    prevBtn.addEventListener('click', function() {
        currentStep = Math.max(currentStep - 1, 0);
        showStep(currentStep);
    });

    // Business Hours Presets
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

    // Initialize hours presets
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            applyHoursPreset(this.dataset.preset);
        });
    });

    // Auto-copy Monday's hours
 /*   const autoCopyCheckbox = document.getElementById('autoCopyHours');
    autoCopyCheckbox.addEventListener('change', function() {
        if (this.checked) {
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
    });
*/
    // File Upload Handling
    function handleFileUpload(input, previewContainer, maxFiles = 1, maxSize = 2) {
        const files = Array.from(input.files).slice(0, maxFiles);
        previewContainer.innerHTML = '';
        
        files.forEach(file => {
            if (file.size > maxSize * 1024 * 1024) {
                alert(`File ${file.name} exceeds ${maxSize}MB limit`);
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'file-preview-item';
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <span>${file.name}</span>
                    <button type="button" class="remove-file">&times;</button>
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
    
    // Review Preview Functions
function updatePreview() {
    const preview = document.getElementById('listing-preview');
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
                <span>${document.getElementById('category').options[document.getElementById('category').selectedIndex]?.text || 'Not selected'}</span>
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
            <div class="preview-row">
                <label>Description:</label>
                <span class="description-preview">${document.getElementById('description').value || 'Not provided'}</span>
            </div>
        </div>
        
        <div class="preview-section">
            <h3>Business Hours</h3>
            <div class="hours-preview">
                ${generateHoursPreview()}
            </div>
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
        </div>
    `;
}

function generateHoursPreview() {
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
    
    

    // Initialize file uploads
    const logoInput = document.getElementById('logo');
    const galleryInput = document.getElementById('gallery');
    
    logoInput.addEventListener('change', function() {
        handleFileUpload(this, document.getElementById('logo-preview'), 1, 2);
    });
    
    galleryInput.addEventListener('change', function() {
        handleFileUpload(this, document.getElementById('gallery-preview'), 5, 5);
    });

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
    });

    // Initialize form
    showStep(0);
});
</script> 

<?php get_footer(); ?>