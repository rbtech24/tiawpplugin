<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) exit;

// Call wp_head() to ensure styles are loaded
wp_head();

$nonce = wp_create_nonce('directory_listing_nonce');

// Get categories from taxonomy
$categories = get_terms(array(
    'taxonomy' => 'business_category',
    'hide_empty' => false,
    'parent' => 0
));
?>
<style>input[type="text"],
input[type="email"],
input[type="tel"],
input[type="url"],
input[type="time"],
select,
textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #007bff;
    border-radius: 15px; /* Add this line to create rounded corners */
    /* Form Container Styles */
.directory-listing-form-container {
   max-width: 1400px;
   width: 90%;
   margin: 125px auto 2rem;
   padding: 0 1rem;
}

/* Hero Section */
.form-hero {
   position: relative;
   height: 300px;
   display: flex;
   align-items: center;
   text-align: center;
   overflow: hidden;
   background-color: var(--navy);
   border-radius: var(--border-radius);
   margin-bottom: 2rem;
}

.form-hero-content {
   position: relative;
   z-index: 3;
   width: 100%;
   color: var(--white);
   padding: 2rem;
}

.form-hero-overlay {
   position: absolute;
   inset: 0;
   background: linear-gradient(rgba(38,56,126,0.95), rgba(38,56,126,0.98));
   z-index: 2;
}

/* Listing Options */
.listing-options {
   display: grid;
   grid-template-columns: repeat(2, 1fr);
   gap: 2rem;
   margin-bottom: 2rem;
}

.option-card {
   background: var(--white);
   padding: 2rem;
   border-radius: var(--border-radius);
   box-shadow: var(--shadow);
   cursor: pointer;
   transition: var(--transition);
}

.option-card:hover,
.option-card[aria-checked="true"] {
   transform: translateY(-5px);
   box-shadow: 0 5px 15px rgba(0,0,0,0.2);
   border: 2px solid var(--navy);
}

/* Form Progress */
.form-progress {
   background: var(--white);
   padding: 1.5rem;
   border-radius: var(--border-radius);
   margin-bottom: 2rem;
   box-shadow: var(--shadow);
}

.progress-step {
   color: var(--dark-gray);
   padding: 0.5rem 1rem;
   border-radius: 20px;
}

.progress-step.active {
   background: var(--navy);
   color: var(--white);
}

/* Form Sections */
.form-section {
   background: var(--white);
   padding: 2rem;
   border-radius: var(--border-radius);
   box-shadow: var(--shadow);
   margin-bottom: 2rem;
}

/* Business Hours Section */
.business-hours-container {
   display: grid;
   gap: 1rem;
}

.copy-hours-btn {
   background: var(--navy);
   color: var(--white);
   padding: 0.75rem 1.5rem;
   border-radius: var(--border-radius);
   border: none;
   cursor: pointer;
   margin-bottom: 1rem;
}

.business-hours-day {
   display: grid;
   grid-template-columns: 200px 1fr;
   align-items: center;
   gap: 1rem;
}

/* Form Navigation */
.form-navigation {
   display: flex;
   justify-content: space-between;
   gap: 1rem;
   margin-top: 2rem;
}

.form-navigation button {
   padding: 0.75rem 2rem;
   border-radius: var(--border-radius);
   border: none;
   cursor: pointer;
   font-weight: 600;
}

#prev-step {
   background: var(--light-gray);
   color: var(--navy);
}

#next-step,
#submit-listing {
   background: var(--red);
   color: var(--white);
}
    
    
    
}</style>
<div class="directory-listing-form-container" aria-live="polite">
    <h2 id="form-title">Submit Your Business Listing</h2>
    
    <div class="listing-options" role="radiogroup" aria-labelledby="form-title">
        <div class="option-card" id="free-option" tabindex="0" role="radio" aria-checked="true">
            <h3>Free Listing</h3>
            <ul>
                <li>Business Name</li>
                <li>Address</li>
                <li>Description</li>
                <li>Phone Number</li>
                <li>Email Address</li>
            </ul>
            <p>Perfect for small businesses just getting started!</p>
        </div>
        
        <div class="option-card" id="paid-option" tabindex="0" role="radio" aria-checked="false">
            <h3>Paid Listing - $25/year</h3>
            <ul>
                <li>All Free Listing Features</li>
                <li>Logo Upload</li>
                <li>Photo Gallery</li>
                <li>Website Link</li>
                <li>Service Area Option</li>
                <li>Business Hours</li>
                <li>Social Media and Directory Links</li>
            </ul>
            <p>Boost your online presence with our premium listing!</p>
        </div>
    </div>

    <form id="directory-listing-form" enctype="multipart/form-data" aria-labelledby="form-title">
        <input type="hidden" name="action" value="submit_directory_listing">
        <input type="hidden" name="directory_listing_nonce" value="<?php echo $nonce; ?>">
        <input type="hidden" name="listing_type" id="listing_type" value="free">

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
                <input type="text" id="first-name" name="first_name" required aria-required="true">
                <label for="last-name">Last Name: <span class="required">*</span></label>
                <input type="text" id="last-name" name="last_name" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="business-name">Business Name: <span class="required">*</span></label>
                <input type="text" id="business-name" name="business_name" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="street-address">Street Address: <span class="required">*</span></label>
                <input type="text" id="street-address" name="street_address" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="city">City and State: <span class="required">*</span></label>
                <input type="text" id="city" name="city" required aria-required="true" placeholder="Start typing a city name...">
                <input type="hidden" id="state" name="state">
            </div>
            <div class="form-group">
                <label for="zip">ZIP Code: <span class="required">*</span></label>
                <input type="text" id="zip" name="zip" required aria-required="true" pattern="[0-9]{5}" title="Please enter a valid 5-digit ZIP code">
            </div>
            <div class="form-group">
                <label for="description">Description: <span class="required">*</span></label>
                <textarea id="description" name="description" required aria-required="true"></textarea>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number: <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="email">Email Address: <span class="required">*</span></label>
                <input type="email" id="email" name="email" required aria-required="true">
            </div>
        </fieldset>

        <!-- Step 2: Categories -->
        <fieldset class="form-section" data-step="2">
            <legend>Categories</legend>
            <div class="form-group">
                <label for="category">Category: <span class="required">*</span></label>
                <select id="category" name="category" required aria-required="true">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo esc_attr($category->term_id); ?>">
                            <?php echo esc_html($category->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="subcategory">Subcategory:</label>
                <select id="subcategory" name="subcategory">
                    <option value="">Select Subcategory</option>
                    <!-- Options will be populated dynamically based on selected category -->
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
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <div id="service-area-section" style="display: none;">
                <div class="form-group">
                    <label for="service-area-type">Service Area Type:</label>
                    <select id="service-area-type" name="service_area_type">
                        <option value="">Select service area type</option>
                        <option value="zip-codes">ZIP Codes</option>
                        <option value="cities">Cities</option>
                        <option value="counties">Counties</option>
                    </select>
                </div>
                <div id="service-area-input" class="form-group" style="display: none;">
                    <label for="service-area-list">Service Areas (comma-separated):</label>
                    <input type="text" id="service-area-list" name="service_area_list">
                </div>
            </div>
            <div id="services-section">
                <h4>Services Offered</h4>
                <div id="services-container">
                    <!-- Service input fields will be added here -->
                </div>
                <button type="button" id="add-service">Add Service</button>
            </div>
        </fieldset>

        <!-- Step 4: Additional Information (Paid Only) -->
        <fieldset class="form-section paid-only" data-step="4">
            <legend>Additional Information</legend>
            <div class="form-group">
                <label for="website">Website URL:</label>
                <input type="url" id="website" name="website">
            </div>
            <div class="form-group">
                <label for="logo">Logo (max 2MB):</label>
                <input type="file" id="logo" name="logo" accept="image/*">
                <div id="logo-dropzone" class="dropzone">Drag and drop your logo here</div>
            </div>
            <div class="form-group">
                <label for="gallery">Photo Gallery (max 5 images, 5MB each):</label>
                <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple>
                <div id="gallery-dropzone" class="dropzone">Drag and drop your gallery images here</div>
            </div>
            <div class="form-group">
                <label for="social-links">Social Media and Directory Links:</label>
                <div id="links-container">
                    <div class="link-inputs">
                        <input type="url" name="links[0][url]" placeholder="Link URL (e.g., https://yelp.com/mybusiness)">
                        <input type="text" name="links[0][label]" placeholder="Link Label">
                    </div>
                </div>
                <button type="button" id="add-link">Add Another Link</button>
            </div>
            <div class="form-group">
                <h4>Business Hours</h4>
                <div id="business-hours-container">
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach ($days as $day) :
                    ?>
                        <div class="business-hours-day">
                            <div class="day-select">
                                <label>
                                    <input type="checkbox" name="business_hours[<?php echo strtolower($day); ?>][open]" value="1">
                                    <span><?php echo $day; ?></span>
                                </label>
                            </div>
                            <div class="hours-inputs">
                                <input type="time" name="business_hours[<?php echo strtolower($day); ?>][open_time]">
                                <span class="time-separator">to</span>
                                <input type="time" name="business_hours[<?php echo strtolower($day); ?>][close_time]">
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
            
            <div  id="payment" >
        

  <div id="card-element"></div> <!-- Stripe.js will inject the card form here -->
    </div>
    
        </fieldset>

        <!-- Terms and Conditions (outside of fieldsets, always visible) -->
        <div class="form-group terms-conditions">
            <label for="terms-and-conditions">
                <input type="checkbox" id="terms-and-conditions" name="terms_and_conditions" required aria-required="true">
               I agree to the <a href="#" target="_blank">Terms and Conditions</a>
            </label>
        </div>
        
  <script src="https://js.stripe.com/v3/"></script>

<script>
// Initialize Stripe and Elements
const stripe = Stripe('pk_live_51Q1IJKABx6OzSP6kA2eNndSD5luY9WJPP6HSuQ9QFZOFGIlTQaT0YeHAQCIuTlHXEZ0eV04wBl3WdjBtCf4gXi2W00jdezk2mo'); // Set your Stripe publishable key
const elements = stripe.elements();
const card = elements.create('card');
card.mount('#card-element'); // Mount the card element to the DOM

/*
  const form = document.getElementById('payment-form');
  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const amount = document.getElementById('amount').value;

    // Send form data to the server for payment processing
    const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=process_stripe_payment&name=${name}&email=${email}&amount=${amount}`
    }).then(r => r.json());

    if (response.success) {
      const { clientSecret } = response.data;

      const result = await stripe.confirmCardPayment(clientSecret, {
        payment_method: { card: card, billing_details: { name: name, email: email } }
      });

      if (result.error) {
        console.error(result.error.message);
      } else if (result.paymentIntent.status === 'succeeded') {
        alert('Payment successful!');
      }
    } else {
      console.error(response.data.error);
    }
  });*/
</script>

        

        <div class="form-navigation">
            <button type="button" id="prev-step">Previous</button>
            <button type="button" id="next-step">Next</button>
            <button type="submit" id="submit-listing" style="display: none;">Submit Listing</button>
        </div>
    </form>
</div>

<script>
    var directory_listings_form = { 
        ajax_url: "<?php echo admin_url('admin-ajax.php'); ?>", 
        nonce: "<?php echo wp_create_nonce('directory_listing_nonce'); ?>",
        is_user_logged_in: <?php echo is_user_logged_in() ? 'true' : 'false'; ?>
    };
</script>

<?php
// Call wp_footer() to ensure scripts are loaded
wp_footer();
?>
                
                