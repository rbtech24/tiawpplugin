<?php
/*
 * Template Name: Single Directory Listing
 */
get_header();

// Ensure we have a post to work with
if (have_posts()) : 
    while (have_posts()) : the_post();


    
    
    // Get custom field values with error handling
$listingtype = get_field('listing_type');
$logo = get_field('logo');
$phone = get_field('phone_number') ?: '';
$email = get_field('email') ?: '';
$website = get_field('website_link') ?: '';
$service_area = get_field('service_area') ?: '';
$state = get_field('state') ?: '';
$rating = get_field('overall_rating');
$rating = is_numeric($rating) ? floatval($rating) : 0;
$review_count = get_field('review_count');
$review_count = is_numeric($review_count) ? intval($review_count) : 0;
$is_verified = get_field('is_verified') ?: false;
$is_top_rated = get_field('top_rated') ?: false;
$services = get_field('services_offered') ?: array();
$gallery = get_field('photo_gallery') ?: array();
$business_hours = get_field('business_hours') ?: array();
$social_links = get_field('social_media_links') ?: array();
$licenses = get_field('licenses_certifications') ?: array(); 
    
    
    

    // Get business categories
    $business_categories = get_the_terms(get_the_ID(), 'business_category');
    $category_names = array();
    if ($business_categories && !is_wp_error($business_categories)) {
        foreach ($business_categories as $category) {
            $category_names[] = esc_html($category->name);
        }
    }

    // Generate schema.org structured data
    $schema = array(
        "@context" => "http://schema.org",
        "@type" => "LocalBusiness",
        "name" => get_the_title(),
        "image" => $logo ? $logo['url'] : '',
        "telephone" => $phone,
        "email" => $email,
        "url" => $website,
        "address" => array(
            "@type" => "PostalAddress",
            "addressRegion" => $state,
            "addressCountry" => "US"
        )
    );

    if ($rating > 0) {
        $schema['aggregateRating'] = array(
            "@type" => "AggregateRating",
            "ratingValue" => $rating,
            "reviewCount" => $review_count
        );
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html(get_the_title()); ?> | <?php bloginfo('name'); ?></title>
    
    <?php // Add meta tags and schema ?>
    <script type="application/ld+json">
    <?php echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <style>
    /* Add CSS styles here - copying from new design */
    :root {
        --primary: #2c3e50;
        --primary-light: #34495e;
        --secondary: #e74c3c;
        --secondary-light: #ff6b6b;
        --accent: #3498db;
        --accent-light: #5dade2;
        --success: #27ae60;
        --warning: #f1c40f;
        --danger: #c0392b;
        --light: #ecf0f1;
        --dark: #2c3e50;
        --gray: #95a5a6;
        --white: #ffffff;
        --shadow: rgba(0, 0, 0, 0.1);
        --border-radius: 16px;
        --border-radius-sm: 8px;
        --transition: all 0.3s ease;
    }

    :root {
        --primary: #2c3e50;
        --primary-light: #34495e;
        --secondary: #e74c3c;
        --secondary-light: #ff6b6b;
        --accent: #3498db;
        --accent-light: #5dade2;
        --success: #27ae60;
        --warning: #f1c40f;
        --danger: #c0392b;
        --light: #ecf0f1;
        --dark: #2c3e50;
        --gray: #95a5a6;
        --white: #ffffff;
        --shadow: rgba(0, 0, 0, 0.1);
        --border-radius: 16px;
        --border-radius-sm: 8px;
        --transition: all 0.3s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: #f5f6fa;
        line-height: 1.6;
        color: var(--dark);
    }

    /* Layout */
    .page-wrapper {
        display: flex;
        max-width: 1400px;
        margin: 2rem auto;
        gap: 2rem;
        padding: 0 1rem;
    }

    .main-content {
        flex: 1;
        min-width: 0;
    }

    .sidebar {
        width: 300px;
        flex-shrink: 0;
    }

/* Ensure the header stays above the map */
header {
    position: relative;
    z-index: 1000; /* Higher than map's z-index */
}

/* Adjust map container to not overlap */
.map-container {
    position: relative; /* or absolute if needed */
    z-index: 1; /* Lower than header */
    width: 100%; /* Adjust as needed */
    height: 300px; /* Example height, adjust according to your layout */
    margin-top: 20px; /* Add space below header */
    border-radius: var(--border-radius);
    overflow: hidden;
    border: 2px solid var(--accent);
    box-shadow: 0 2px 4px var(--shadow);
}

/* If the map is within a section */
.service-area-section {
    margin-top: 2rem; /* Space from the header or previous section */
    padding: 2rem;
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: 0 2px 4px var(--shadow);
}

/* Adjust the layout for responsiveness */
@media (max-width: 1024px) {
    .page-wrapper {
        flex-direction: column;
    }

    .map-container {
        height: 250px; /* Reduce size on smaller screens */
    }
}



    /* Section Headers */
    .section-title {
        font-size: 1.5rem;
        color: var(--primary);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--light);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Business Header */
    .listing-header {
        background: var(--primary);
        color: var(--white);
        padding: 2rem;
        border-radius: var(--border-radius);
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .header-content {
        display: flex;
        gap: 2rem;
        position: relative;
        z-index: 1;
        align-items: center;
    }

    .business-logo {
        width: 200px;
        height: 200px;
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px var(--shadow);
    }

    .business-info {
        flex: 1;
    }

    .business-info h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--white);
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }

    /* Badges */
    .business-meta {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: var(--border-radius-sm);
        font-size: 0.875rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge-verified {
        background: var(--success);
        color: var(--white);
    }

    .badge-premium {
        background: var(--warning);
        color: var(--dark);
    }

    .rating-stars {
        color: var(--warning);
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .category-tags {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.category-tag {
    background: rgba(255,255,255,0.2);
    color: var(--white);
    padding: 0.25rem 0.75rem;
    border-radius: var(--border-radius-sm);
    font-size: 0.875rem;
}
    .category-tags {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.category-tag {
    background: rgba(255,255,255,0.2);
    color: var(--white);
    padding: 0.25rem 0.75rem;
    border-radius: var(--border-radius-sm);
    font-size: 0.875rem;
}
    


/* Services Section */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .service-card {
        background: var(--light);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        text-align: center;
        transition: var(--transition);
    }

    .service-card:hover {
        transform: translateY(-5px);
        background: var(--primary);
        color: var(--white);
    }

    .service-card i {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: var(--accent);
    }

    .service-card:hover i {
        color: var(--warning);
    }

    /* Service Area */
    .service-area-section {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 4px var(--shadow);
    }

    .service-area-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .service-area-list {
        background: var(--light);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        max-height: 400px;
        overflow-y: auto;
    }

    .service-area-list ul {
        list-style: none;
    }

    .service-area-list li {
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--gray);
    }

    .service-area-list li:last-child {
        border-bottom: none;
    }

    .map-container {
        height: 400px;
        border-radius: var(--border-radius);
        overflow: hidden;
        border: 2px solid var(--accent);
        box-shadow: 0 2px 4px var(--shadow);
    }
/* Gallery Section Styles */
.gallery-section {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 4px var(--shadow);
}

/* Gallery Categories */
.gallery-categories {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.category-btn {
    padding: 0.5rem 1rem;
    border: 2px solid var(--light);
    border-radius: var(--border-radius-sm);
    background: none;
    color: var(--gray);
    cursor: pointer;
    transition: var(--transition);
    font-weight: 600;
}

.category-btn:hover,
.category-btn.active {
    background: var(--primary);
    color: var(--white);
    border-color: var(--primary);
}

/* Gallery Grid */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.gallery-item {
    position: relative;
    border-radius: var(--border-radius);
    overflow: hidden;
    aspect-ratio: 4/3;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 1.5rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-item:hover img {
    transform: scale(1.1);
}

.overlay-content {
    color: var(--white);
}

.overlay-content h4 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.overlay-content p {
    font-size: 0.9rem;
    opacity: 0.9;
}

.gallery-zoom {
    align-self: flex-end;
    background: var(--white);
    color: var(--primary);
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
}

.gallery-zoom:hover {
    background: var(--primary);
    color: var(--white);
}

/* Modal Styles */
.photo-modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.9);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.photo-modal.active {
    display: flex;
}

.modal-content {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
}

.modal-content img {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
}

.modal-close,
.modal-prev,
.modal-next {
    position: absolute;
    background: var(--white);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    z-index: 1;
}

.modal-close {
    top: -20px;
    right: -20px;
}

.modal-prev {
    left: -20px;
    top: 50%;
    transform: translateY(-50%);
}

.modal-next {
    right: -20px;
    top: 50%;
    transform: translateY(-50%);
}

.modal-caption {
    color: var(--white);
    text-align: center;
    margin-top: 1rem;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }

    .category-btn {
        font-size: 0.9rem;
        padding: 0.4rem 0.8rem;
    }
}
    /* Reviews Section */
    .reviews-section {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 4px var(--shadow);
    }

    .review-stats {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 2rem;
        background: var(--light);
        padding: 2rem;
        border-radius: var(--border-radius);
        margin-bottom: 2rem;
    }

    .overall-rating {
        text-align: center;
        padding-right: 2rem;
        border-right: 2px solid var(--gray);
    }

    .rating-number {
        font-size: 3.5rem;
        font-weight: bold;
        color: var(--primary);
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .rating-distribution {
        flex: 1;
    }

    .rating-bar {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }

    .rating-progress {
        flex: 1;
        height: 12px;
        background: var(--gray);
        border-radius: 6px;
        overflow: hidden;
    }

    .rating-progress-fill {
        height: 100%;
        background: var(--warning);
        transition: width 0.3s ease;
    }

    /* Review Cards */
    .review-cards {
        display: grid;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .review-card {
        background: var(--light);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        transition: var(--transition);
    }

    .review-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px var(--shadow);
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .reviewer-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .reviewer-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
    }

    .review-content {
        margin-bottom: 1rem;
        line-height: 1.8;
    }

    .review-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--gray);
        font-size: 0.875rem;
    }

    .verified-badge {
        color: var(--success);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .helpful-button {
        background: none;
        border: none;
        color: var(--gray);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition);
    }

    .helpful-button:hover {
        color: var(--accent);
    }

    /* Add Review Form */
    .add-review-form {
        background: var(--white);
        padding: 2rem;
        border-radius: var(--border-radius);
        margin-top: 2rem;
    }

    .star-rating-input {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .star-button {
        background: none;
        border: none;
        font-size: 2rem;
        color: var(--gray);
        cursor: pointer;
        transition: var(--transition);
    }

    .star-button:hover,
    .star-button.active {
        color: var(--warning);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--dark);
    }
/* About Section Styles */
.about-section {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 4px var(--shadow);
}

.about-content {
    line-height: 1.8;
}

.about-content p {
    margin-bottom: 1rem;
}

.about-content p:last-child {
    margin-bottom: 0;
}

.company-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--light);
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary);
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--gray);
    font-size: 0.875rem;
}
/* Responsive Rating Stars */
.rating-stars {
    font-size: clamp(1rem, 2vw, 1.5rem); /* Adjusts between 1rem and 1.5rem based on viewport width */
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--warning); /* Star color */
}

.rating-stars span {
    font-size: 0.875rem; /* Text size for review count */
    color: var(--gray); /* Text color */
}



/* Sidebar Styles */
    .sidebar-widget {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px var(--shadow);
    }

    .widget-title {
        font-size: 1.25rem;
        color: var(--primary);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--light);
    }

    /* Voting Widget */
    .voting-widget {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: var(--white);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .voting-title {
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .vote-buttons {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .vote-button {
        padding: 1rem;
        border-radius: var(--border-radius-sm);
        border: none;
        cursor: pointer;
        transition: var(--transition);
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .vote-up {
        background: var(--success);
        color: var(--white);
    }

    .vote-down {
        background: var(--danger);
        color: var(--white);
    }

    .vote-button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .vote-count {
        font-size: 2rem;
        font-weight: bold;
        color: var(--white);
        margin: 1rem 0;
    }

    /* Related Businesses */
/* Related Businesses */
.sidebar-widget .related-listings {
    list-style: none;
    padding: 0;
}

.related-listing-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 10px 0;
    border-bottom: 1px solid var(--light);
    transition: transform 0.3s ease;
}

.related-listing-item:hover {
    transform: translateX(5px);
    background-color: var(--light);
}

.related-listing-logo {
    width: 50px;
    height: 50px;
    overflow: hidden;
    border-radius: var(--border-radius-sm);
}

.related-listing-info {
    flex-grow: 1;
}

.related-listing-info h4 {
    margin: 0;
    font-size: 1rem;
    color: var(--primary);
}

.related-listing-category {
    font-size: 0.875rem;
    color: var(--gray);
    margin-top: 0.25rem;
}

.related-listing-rating {
    font-size: 0.875rem;
    color: var(--warning);
}

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        order: 2; /* Push sidebar below main content on smaller screens */
    }
}

/* License & Certification Styles */
.certification-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.certification-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--light);
    border-radius: var(--border-radius-sm);
    transition: var(--transition);
}

.certification-item:hover {
    background: var(--white);
    box-shadow: 0 2px 4px var(--shadow);
    transform: translateX(5px);
}

.certification-icon {
    width: 40px;
    height: 40px;
    background: var(--primary);
    color: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.certification-info {
    flex-grow: 1;
}

.certification-info h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    color: var(--primary);
}

.certification-info p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray);
}

.license-number {
    font-family: monospace;
}

.license-expiry {
    color: var(--accent);
    font-weight: 500;
}




/* Hours Widget */
.hours-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.hours-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--light);
    font-size: 0.9rem;
}

.hours-item:last-child {
    border-bottom: none;
}

.current-day {
    font-weight: 600;
    color: var(--primary);
}

.open-status {
    margin-top: 1rem;
    text-align: center;
    background: var(--light);
    padding: 0.75rem;
    border-radius: var(--border-radius-sm);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
}

.status-badge.open {
    color: var(--success);
}

.next-hours {
    display: block;
    font-size: 0.875rem;
    color: var(--gray);
    margin-top: 0.25rem;
}

/* Promo Widget */
.promo-widget {
    background: linear-gradient(135deg, var(--accent), var(--accent-light));
    color: var(--white);
}

.promo-content {
    text-align: center;
}

.promo-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.promo-header i {
    font-size: 1.5rem;
}

.promo-content p {
    margin: 1rem 0;
    font-size: 1.1rem;
}

/* Certification List */
.certification-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.certification-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: var(--light);
    border-radius: var(--border-radius-sm);
    transition: var(--transition);
}

.certification-item:hover {
    background: var(--white);
    box-shadow: 0 2px 4px var(--shadow);
}

.certification-info h4 {
    font-size: 0.9rem;
    margin: 0 0 0.25rem 0;
}

.certification-info p {
    font-size: 0.8rem;
    color: var(--gray);
    margin: 0;
}

/* Payment Methods */
.payment-methods {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.payment-method {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: var(--light);
    border-radius: var(--border-radius-sm);
    font-size: 0.9rem;
    transition: var(--transition);
}

.payment-method:hover {
    background: var(--white);
    box-shadow: 0 2px 4px var(--shadow);
}

.payment-method i {
    color: var(--primary);
}

/* Responsive Adjustments */
@media (max-width: 1024px) {
    .payment-methods {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .payment-methods {
        grid-template-columns: repeat(2, 1fr);
    }
}
    /* CTA Buttons */
    .cta-button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: var(--secondary);
        color: var(--white);
        padding: 1rem;
        border-radius: var(--border-radius);
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 1rem;
        transition: var(--transition);
    }

    .cta-button:hover {
        background: var(--secondary-light);
        transform: translateY(-2px);
    }
     /* Social Media Links */
    .social-links {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 1rem;
    }

    .social-link {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        transition: var(--transition);
    }

    .social-link:hover {
        transform: translateY(-2px);
        background: var(--accent);
    }

    /* Media Queries */
    @media (max-width: 1024px) {
        .page-wrapper {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .service-area-content {
            grid-template-columns: 1fr;
        }

        .map-container {
            height: 300px;
        }
    }
    
    /* Listing Management Styles */
.listing-management {
    border: 2px dashed var(--gray);
    background: var(--light);
    margin-top: 2rem;
}

.listing-management.claimed {
    border-style: solid;
    border-color: var(--success);
    background: var(--white);
}

.listing-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.listing-management.unclaimed .listing-status {
    color: var(--warning);
}

.listing-management.claimed .listing-status {
    color: var(--success);
}

.listing-info {
    color: var(--gray);
    font-size: 0.9rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.claim-button {
    background: var(--warning);
    border-color: var(--warning);
}

.claim-button:hover {
    background: var(--warning);
    filter: brightness(1.1);
}

.manage-button {
    background: var(--success);
    border-color: var(--success);
}

.manage-button:hover {
    background: var(--success);
    filter: brightness(1.1);
}

.owner-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: var(--light);
    border-radius: var(--border-radius-sm);
}

.owner-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.owner-details {
    display: flex;
    flex-direction: column;
}

.owner-name {
    font-weight: 600;
    color: var(--primary);
}

.owner-role {
    font-size: 0.8rem;
    color: var(--gray);
}

/* Add smooth transition for status changes */
.listing-management {
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .listing-management {
        margin-top: 1rem;
    }
}
    

    /* Quick Contact Form */
    .quick-contact-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .quick-contact-form input,
    .quick-contact-form textarea,
    .quick-contact-form select {
        padding: 0.75rem;
        border: 1px solid var(--gray);
        border-radius: var(--border-radius-sm);
        background: var(--white);
        transition: var(--transition);
    }

    .submit-button {
        background: var(--accent);
        color: var(--white);
        padding: 0.75rem;
        border: none;
        border-radius: var(--border-radius-sm);
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
    }

    .submit-button:hover {
        background: var(--accent-light);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .page-wrapper {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
        }

        .service-area-content {
            grid-template-columns: 1fr;
        }

        .map-container {
            height: 300px;
        }
    }

    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
        }

        .business-meta {
            justify-content: center;
        }

        .business-logo {
            width: 150px;
            height: 150px;
        }

        .review-stats {
            grid-template-columns: 1fr;
        }

        .overall-rating {
            border-right: none;
            border-bottom: 2px solid var(--gray);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .vote-buttons {
            flex-direction: row;
            justify-content: center;
        }
        
        
    }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <main class="main-content">
            <!-- Listing Header -->
            <div class="listing-header">
                <div class="header-content">
                    <div class="business-logo">
                        <?php if ($logo && isset($logo['url'])) : ?>
                            <img src="<?php echo esc_url($logo['url']); ?>" 
                                 alt="<?php echo esc_attr(get_the_title()); ?>">
                        <?php else : ?>
                            <img src="/wp-content/plugins/directory-listings/assets/images/placeholder.png" 
                                 alt="<?php echo esc_attr(get_the_title()); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="business-info">
                        <h1><?php the_title(); ?></h1>
                        <?php if (!empty($category_names)) : ?>
                            <div class="category-tags">
                                <?php foreach ($category_names as $category) : ?>
                                    <span class="category-tag"><?php echo esc_html($category); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="business-meta">
                            <?php if ($is_verified) : ?>
                                <span class="badge badge-verified">
                                    <i class="fas fa-check-circle"></i> Verified
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($listingtype === 'paid') : ?>
                                <span class="badge badge-premium">
                                    <i class="fas fa-crown"></i> Top Rated Listing
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if ($rating > 0) : ?>
                            <div class="rating-stars">
                                <?php echo str_repeat('★', round($rating)); ?>
                                <span>(<?php echo number_format($rating, 1); ?> from <?php echo $review_count; ?> reviews)</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

<!-- Key Info Section -->
<div class="key-info-bar">
    <div class="key-info-grid">
        <?php if ($phone) : ?>
        <div class="key-info-item">
            <div class="key-info-icon">
                <i class="fas fa-phone"></i>
            </div>
            <div>
                <strong>Contact</strong>
                <div><?php echo esc_html($phone); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($website) : ?>
        <div class="key-info-item">
            <div class="key-info-icon">
                <i class="fas fa-globe"></i>
            </div>
            <div>
                <strong>Website</strong>
                <div>
                    <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html($website); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($business_hours)) : ?>
        <div class="key-info-item">
            <div class="business-hours-info">
                <div class="open-status">
                    <?php 
                    // Check for 24-hour business
                    $is_24h = true;
                    foreach ($business_hours as $hours) {
                        if (
                            !isset($hours['opening_time'], $hours['closing_time']) ||
                            ($hours['opening_time'] !== '12:00 am' || $hours['closing_time'] !== '12:00 am')
                        ) {
                            $is_24h = false;
                            break;
                        }
                    }

                    if ($is_24h) {
                        // Display 24 HOURS status
                        echo '<div class="business-sign sign-24h">
                                <div class="sign-content">
                                    <span class="sign-text">24 HOURS</span>
                                    <span class="sign-subtext">Always Open</span>
                                </div>
                              </div>';
                    } else {
                        // Logic for determining open/close state
                        $current_day = strtolower(date('l'));
                        $current_time = strtotime(current_time('H:i'));
                        $is_open = false;
                        $closes_at = '';
                        $next_opening = '';

                        foreach ($business_hours as $hours) {
                            if (strtolower($hours['day_of_week']) === $current_day) {
                                if ($hours['opening_time'] === 'Closed') {
                                    $is_open = false;
                                } elseif (!empty($hours['opening_time']) && !empty($hours['closing_time'])) {
                                    $opening_time = strtotime($hours['opening_time']);
                                    $closing_time = strtotime($hours['closing_time']);
                                    if ($current_time >= $opening_time && $current_time < $closing_time) {
                                        $is_open = true;
                                        $closes_at = date('g:i A', $closing_time);
                                    }
                                }
                                break;
                            }
                        }

                        if (!$is_open) {
                            // Find next opening day/time
                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            $current_index = array_search($current_day, $days);

                            for ($i = 1; $i <= 7; $i++) {
                                $next_index = ($current_index + $i) % 7;
                                $next_day = $days[$next_index];

                                foreach ($business_hours as $hours) {
                                    if (
                                        strtolower($hours['day_of_week']) === $next_day &&
                                        $hours['opening_time'] !== 'Closed' &&
                                        !empty($hours['opening_time'])
                                    ) {
                                        $next_opening = ucfirst($next_day) . ' at ' . date('g:i A', strtotime($hours['opening_time']));
                                        break 2;
                                    }
                                }
                            }
                        }

                        // Display Open/Closed status
                        echo '<div class="business-sign ' . ($is_open ? 'sign-open' : 'sign-closed') . '">
                                <div class="sign-content">
                                    <span class="sign-text">' . ($is_open ? 'OPEN' : 'CLOSED') . '</span>';
                        if ($is_open && $closes_at) {
                            echo '<span class="sign-subtext">Until ' . esc_html($closes_at) . '</span>';
                        } elseif (!$is_open && $next_opening) {
                            echo '<span class="sign-subtext">Opens ' . esc_html($next_opening) . '</span>';
                        }
                        echo '</div>
                              </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* General Styling */
.hours-info {
    text-align: center;
}

.key-info-icon.hours-icon {
    background: var(--primary);
    color: var(--white);
    margin: 0 auto 0.5rem;
}

/* Business Sign Styling */
.business-sign {
    margin-top: 0.5rem;
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
    border: 3px solid;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.sign-24h, .sign-open {
    background: rgba(39, 174, 96, 0.1);
    border-color: #27ae60;
}

.sign-closed {
    background: rgba(192, 57, 43, 0.1);
    border-color: #c0392b;
}

.sign-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.sign-text {
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: 2px;
}

.sign-24h .sign-text, .sign-open .sign-text {
    color: #27ae60;
}

.sign-closed .sign-text {
    color: #c0392b;
}

.sign-subtext {
    font-size: 0.875rem;
    color: #666;
}

.business-sign:hover {
    transform: translateY(-2px);
}

.sign-24h:hover, .sign-open:hover {
    box-shadow: 0 6px 12px rgba(39, 174, 96, 0.2), 0 0 8px rgba(39, 174, 96, 0.3);
}

.sign-closed:hover {
    box-shadow: 0 6px 12px rgba(192, 57, 43, 0.2), 0 0 8px rgba(192, 57, 43, 0.3);
}

/* Key Info Bar Styling */
.key-info-bar {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Key Info Grid */
.key-info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3 columns for desktop */
    gap: 1.5rem;
    justify-items: center; /* Center items in grid */
}

.key-info-item {
    display: flex;
    flex-direction: column; /* Stack icon and text vertically */
    align-items: center;
    gap: 1rem;
    text-align: center;
}

.key-info-icon {
    width: 50px;
    height: 50px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2c3e50;
    font-size: 1.25rem;
}

/* Responsive Design for Smaller Screens */
@media (max-width: 768px) {
    .key-info-grid {
        grid-template-columns: 1fr; /* Single column on smaller screens */
        gap: 1rem; /* Reduced gap for mobile view */
    }

    .key-info-item {
        align-items: center; /* Center-align items */
    }

    .business-sign {
        margin: 1rem auto; /* Center sign */
    }
}
</style>


<!-- About Section -->
<section class="about-section" id="about">
    <h2 class="section-title">About Us</h2>
    <div class="about-content">
        <?php the_content(); ?>
        
        <div class="company-stats">
            <?php if ($rating > 0) : ?>
            <div class="stat-item">
                <div class="stat-number"><?php echo number_format($rating, 1); ?></div>
                <div class="stat-label">Average Rating</div>
            </div>
            <?php endif; ?>

            <?php if ($review_count > 0) : ?>
            <div class="stat-item">
                <div class="stat-number"><?php echo $review_count; ?></div>
                <div class="stat-label">Customer Reviews</div>
            </div>
            <?php endif; ?>

            <?php if (get_post_meta(get_the_ID(), 'years_in_business', true)) : ?>
            <div class="stat-item">
                <div class="stat-number"><?php echo esc_html(get_post_meta(get_the_ID(), 'years_in_business', true)); ?></div>
                <div class="stat-label">Years in Business</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

            <!-- Services Section -->
            <?php if (!empty($services)) : ?>
            <section class="section" id="services">
                <h2 class="section-title">Our Services</h2>
                <div class="services-grid">
                    <?php foreach ($services as $service) : ?>
                        <div class="service-card">
                            <h3><?php echo esc_html($service['service_name']); ?></h3>
                            <?php if (!empty($service['service_description'])) : ?>
                                <p><?php echo esc_html($service['service_description']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            
            <?php endif; ?>
            <section class="service-area-section" id="service-area">
    <h2 class="section-title">Service Area</h2>
    <div class="service-area-content">
        <div class="service-area-list">
            <h3>Areas We Serve</h3>
            <ul>
                <?php if (!empty($service_area)) : ?>
                    <?php foreach ($service_area as $area) : ?>
                        <li><?php echo esc_html($area); ?></li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li>No service areas listed.</li>
                <?php endif; ?>
            </ul>
        </div>
        <div id="service-area-map" class="map-container"></div>
    </div>
</section>
            
            <section class="gallery-section" id="gallery">
    <h2 class="section-title">
        <i class="fas fa-images"></i> Photo Gallery
    </h2>
    <div class="gallery-grid">
        <?php if (!empty($gallery)) : ?>
            <?php foreach ($gallery as $image) : ?>
                <div class="gallery-item">
                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No images available in the gallery.</p>
        <?php endif; ?>
    </div>
</section>
            
<?php display_listing_reviews(); ?>

        </main>

        <aside class="sidebar">
           <!-- Business Hours Widget -->
<div class="sidebar-widget">
    <h3 class="widget-title">Business Hours</h3>
    <?php if (!empty($business_hours)) : ?>
        <ul class="hours-list">
            <?php 
            // Set timezone based on the business location
            $business_timezone = get_field('business_timezone') ?: 'America/New_York'; // Default to Eastern Time
            date_default_timezone_set($business_timezone);

            $days_of_week = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
            $current_day = strtolower(date('l'));

            $is_24hr = true;
            foreach ($business_hours as $hours) {
                if (!isset($hours['opening_time']) || !isset($hours['closing_time']) ||
                    strtolower($hours['opening_time']) !== '12:00 am' || 
                    strtolower($hours['closing_time']) !== '12:00 am') {
                    $is_24hr = false;
                    break;
                }
            }

            foreach ($days_of_week as $day) :
                $day_hours = array_values(array_filter($business_hours, function($hours) use ($day) {
                    return isset($hours['day_of_week']) && strtolower($hours['day_of_week']) === $day;
                }));
                $day_hours = !empty($day_hours) ? $day_hours[0] : null;
            ?>
                <li class="hours-item <?php echo ($current_day === $day) ? 'current-day' : ''; ?>">
                    <span class="day"><?php echo ucfirst($day); ?></span>
                    <span class="hours">
                        <?php
                        if ($is_24hr) {
                            echo 'Open 24 Hours';
                        } elseif (!empty($day_hours)) {
                            $opening_time = isset($day_hours['opening_time']) ? esc_html($day_hours['opening_time']) : '';
                            $closing_time = isset($day_hours['closing_time']) ? esc_html($day_hours['closing_time']) : '';
                            if (!empty($opening_time) && !empty($closing_time)) {
                                echo $opening_time . ' - ' . $closing_time;
                            } else {
                                echo 'Closed';
                            }
                        } else {
                            echo 'Closed';
                        }
                        ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<style>
/* Sidebar Widget Styles */
.sidebar-widget {
    padding: 1rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.widget-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-transform: uppercase;
    color: var(--primary);
}

.hours-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.hours-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e5e5e5;
    color: var(--dark);
    transition: background 0.3s ease;
}

.hours-item:last-child {
    border-bottom: none;
}

.hours-item .day {
    font-weight: 600;
    text-transform: capitalize;
}

.hours-item .hours {
    font-style: italic;
    color: var(--gray);
}

.hours-item.current-day {
    background: rgba(39, 174, 96, 0.1); /* Light green background for current day */
    font-weight: bold;
    color: var(--primary);
}

.hours-item.current-day .hours {
    color: var(--primary);
}

.hours-item:hover {
    background: rgba(52, 152, 219, 0.1); /* Light blue hover effect */
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .hours-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .hours-item .hours {
        margin-top: 0.25rem;
    }
}
</style>


            <!-- Voting Widget -->
            <div class="voting-widget">
                <h3 class="voting-title">Support This Business</h3>
                <?php 
                // Get current vote count
                $upvotes = get_post_meta(get_the_ID(), 'upvotes', true) ?: 0;
                $downvotes = get_post_meta(get_the_ID(), 'downvotes', true) ?: 0;
                $total_votes = $upvotes - $downvotes;
                ?>
                <div class="vote-buttons">
                    <button class="vote-button vote-up" id="upvoteBtn" data-post-id="<?php echo get_the_ID(); ?>">
                        <i class="fas fa-thumbs-up"></i>
                        <span>Upvote</span>
                    </button>
                    <div class="vote-count" id="voteCount"><?php echo $total_votes; ?></div>
                    <button class="vote-button vote-down" id="downvoteBtn" data-post-id="<?php echo get_the_ID(); ?>">
                        <i class="fas fa-thumbs-down"></i>
                        <span>Downvote</span>
                    </button>
                </div>
            </div>

            <!-- Contact Buttons -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Get in Touch</h3>
                <?php if ($phone) : ?>
                    <a href="tel:<?php echo esc_attr($phone); ?>" class="cta-button">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                <?php endif; ?>

                <?php if ($website) : ?>
                    <a href="<?php echo esc_url($website); ?>" class="cta-button" target="_blank" rel="noopener">
                        <i class="fas fa-globe"></i> Visit Website
                    </a>
                <?php endif; ?>
            </div>

            <!-- Social Media Links -->
            <?php if (!empty($social_links)) : ?>
            <div class="sidebar-widget">
                <h3 class="widget-title">Follow Us</h3>
                <div class="social-links">
                    <?php if (!empty($social_links['facebook'])) : ?>
                        <a href="<?php echo esc_url($social_links['facebook']); ?>" class="social-link" target="_blank" rel="noopener">
                            <i class="fab fa-facebook"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($social_links['google'])) : ?>
                        <a href="<?php echo esc_url($social_links['google']); ?>" class="social-link" target="_blank" rel="noopener">
                            <i class="fab fa-google"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($social_links['yelp'])) : ?>
                        <a href="<?php echo esc_url($social_links['yelp']); ?>" class="social-link" target="_blank" rel="noopener">
                            <i class="fab fa-yelp"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($social_links['thumbtack'])) : ?>
                        <a href="<?php echo esc_url($social_links['thumbtack']); ?>" class="social-link" target="_blank" rel="noopener">
                            <i class="fab fa-thumbtack"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($social_links['other_urls'])) : 
                        foreach ($social_links['other_urls'] as $other_url) : ?>
                            <a href="<?php echo esc_url($other_url['url']); ?>" class="social-link" target="_blank" rel="noopener">
                                <i class="fas fa-link"></i>
                            </a>
                        <?php endforeach;
                    endif; ?>
                </div>
            </div>
            <?php endif; ?>

<!-- Quick Contact Form -->
<div class="sidebar-widget">
    <h3 class="widget-title">
        <i class="fas fa-envelope"></i> Request Quote
    </h3>
    <form class="quick-contact-form" id="quick-contact-form">
        <?php wp_nonce_field('quick_contact_nonce', 'quick_contact_nonce'); ?>
        <input type="hidden" name="listing_id" value="<?php echo get_the_ID(); ?>">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="tel" name="phone" placeholder="Phone Number">
        <select name="service" required>
            <option value="">Select Service</option>
            <?php 
            // Get business categories
            $categories = get_the_terms(get_the_ID(), 'business_category');
            if ($categories && !is_wp_error($categories)) {
                foreach ($categories as $category) {
                    echo '<option value="' . esc_attr($category->name) . '">' 
                         . esc_html($category->name) . '</option>';
                }
            }
            
            // Add any custom services if available
            if (!empty($services)) {
                foreach ($services as $service) {
                    if (!empty($service['service_name'])) {
                        echo '<option value="' . esc_attr($service['service_name']) . '">'
                             . esc_html($service['service_name']) . '</option>';
                    }
                }
            }
            ?>
        </select>
        <textarea name="message" placeholder="Describe your needs..." rows="4"></textarea>
        <button type="submit" class="submit-button">
            <i class="fas fa-paper-plane"></i> Get Quote
        </button>
    </form>
    <div id="form-response" class="form-response"></div>
</div>

           <!-- Related Businesses - Only for free listings -->
<?php if ($listingtype === 'free') : ?>
<div class="sidebar-widget">
    <h3 class="widget-title">Similar Businesses Nearby</h3>
    <ul class="related-listings">
        <?php 
        // Fetch 6 related businesses
        $related_posts = get_related_businesses(get_the_ID(), 6); 
        if ($related_posts && $related_posts->have_posts()) :
            while ($related_posts->have_posts()) : $related_posts->the_post();
                $related_logo = get_field('logo', get_the_ID());
                $related_rating = get_field('overall_rating', get_the_ID());
                $related_categories = get_the_terms(get_the_ID(), 'business_category');
        ?>
            <li class="related-listing-item">
                <div class="related-listing-logo">
                    <?php if ($related_logo) : ?>
                        <img src="<?php echo esc_url($related_logo['url']); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <?php else : ?>
                        <img src="/path/to/default/logo.jpg" alt="Default Business Logo">
                    <?php endif; ?>
                </div>
                <div class="related-listing-info">
                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <?php if ($related_categories) : ?>
                        <div class="related-listing-category">
                            <?php echo esc_html($related_categories[0]->name); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($related_rating) : ?>
                        <div class="related-listing-rating">
                            <?php echo str_repeat('★', floor($related_rating)); ?>
                            <?php if ($related_rating - floor($related_rating) > 0) echo '½'; ?> 
                            <span>(<?php echo number_format($related_rating, 1); ?>)</span>
                        </div>
                    <?php endif; ?>
                </div>
            </li>
        <?php 
            endwhile;
            wp_reset_postdata(); 
        else : ?>
            <li>No similar businesses found.</li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Licenses & Certifications Section - For all listing types -->

<div class="sidebar-widget">
    <h3 class="widget-title">
        <i class="fas fa-certificate"></i> Licenses & Certifications
    </h3>
    <?php 
    $licenses = get_field('licenses_certifications');
    if (!empty($licenses)) : ?>
        <div class="certification-list">
            <?php foreach ($licenses as $license) : ?>
                <div class="certification-item">
                    <div class="certification-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="certification-info">
                        <h4><?php echo esc_html($license['title']); ?></h4>
                        <?php if (!empty($license['number'])) : ?>
                            <p class="license-number">License #: <?php echo esc_html($license['number']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($license['issuer'])) : ?>
                            <p class="license-issuer">Issued by: <?php echo esc_html($license['issuer']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($license['expiration'])) : ?>
                            <p class="license-expiry">Expires: <?php echo esc_html($license['expiration']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($license['verified'])) : ?>
                            <p class="license-verified">
                                <i class="fas fa-check-circle"></i> Verified
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>No licenses or certifications listed.</p>
    <?php endif; ?>
</div>

            <!-- Listing Management Section -->
<?php 
// Check if listing is claimed or top rated
$listing_claimed = get_post_meta(get_the_ID(), 'listing_claimed', true);
$owner_id = get_post_meta(get_the_ID(), 'listing_owner', true);
$is_top_rated = get_field('top_rated');
$listing_type = get_field('listing_type');

// A listing is considered claimed if it's either manually claimed or is a top rated/paid listing
$is_claimed = $listing_claimed || $is_top_rated || $listing_type === 'paid';
?>
<div class="sidebar-widget listing-management <?php echo $is_claimed ? 'claimed' : 'unclaimed'; ?>">
    <?php if (!$is_claimed) : ?>
        <div class="listing-status">
            <i class="fas fa-exclamation-circle"></i>
            <span>Unclaimed Listing<?php 
                global $post;
        
        // Check if listing is already claimed
     
        
        // Generate claim URL
         $claim_url = generate_claim_url($post->ID);
            ?></span>
        </div>
        <p class="listing-info">Are you the owner or manager of <?php the_title(); ?>? Claim this listing to update information, respond to reviews, and more.</p>
        <a href="<?php echo $claim_url;
        // echo esc_url(add_query_arg('claim', get_the_ID(), home_url('/claim-listing/'))); ?>" 
           class="cta-button claim-button">
            <i class="fas fa-flag"></i> Claim This Listing
        </a>
    <?php else : ?>
        <div class="listing-status">
            <i class="fas fa-check-circle"></i>
            <span><?php echo $is_top_rated ? 'Top Rated Business' : 'Verified Owner'; ?></span>
        </div>
        <?php 
        // Show owner info if there's a specific owner assigned
        if ($owner_id && ($owner = get_userdata($owner_id))) : ?>
            <div class="owner-info">
                <?php echo get_avatar($owner_id, 40, '', '', array('class' => 'owner-avatar')); ?>
                <div class="owner-details">
                    <span class="owner-name"><?php echo esc_html($owner->display_name); ?></span>
                    <span class="owner-role"><?php echo $is_top_rated ? 'Top Rated Business Owner' : 'Business Owner'; ?></span>
                </div>
            </div>
        <?php endif; ?>
        
        <a href="<?php echo get_edit_post_link(); ?>" class="cta-button manage-button">
            <i class="fas fa-cog"></i> Manage Listing
        </a>
    <?php endif; ?>
</div>

<style>
/*CSS rules to ensure buttons display correctly on mobile */
@media (max-width: 768px) {
    .listing-management {
        padding: 1rem;
    }
    
    .listing-management .cta-button {
        display: flex;
        width: 100%;
        justify-content: center;
        margin-top: 1rem;
        padding: 0.75rem;
    }

    .listing-management .manage-button,
    .listing-management .claim-button {
        display: flex !important; /* Force display on mobile */
        visibility: visible !important;
    }

    .listing-management .owner-info {
        margin-bottom: 1rem;
    }
}
</style>
    
    
</div>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Leaflet map if service area exists
    if (document.getElementById('service-area-map')) {
        const map = L.map('service-area-map').setView([47.6062, -122.3321], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add service area markers
        const serviceAreaList = document.querySelector('.service-area-list');
        if (serviceAreaList) {
            const areas = serviceAreaList.textContent.split(',').map(area => area.trim());
            const state = '<?php echo esc_js($state); ?>';
            
            const bounds = L.latLngBounds();
            areas.forEach(async (area) => {
                const query = encodeURIComponent(`${area}, ${state}, USA`);
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&countrycodes=us`);
                    const data = await response.json();
                    if (data.length > 0) {
                        const latLng = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                        L.marker(latLng).addTo(map).bindPopup(area);
                        bounds.extend(latLng);
                        map.fitBounds(bounds);
                    }
                } catch (error) {
                    console.error('Error geocoding:', error);
                }
            });
        }

        // Enable scroll zoom only when map is focused
        map.scrollWheelZoom.disable();
        map.on('focus', () => map.scrollWheelZoom.enable());
        map.on('blur', () => map.scrollWheelZoom.disable());
    }

    // Voting System
    const setupVoting = () => {
        const upvoteBtn = document.getElementById('upvoteBtn');
        const downvoteBtn = document.getElementById('downvoteBtn');
        const voteCount = document.getElementById('voteCount');
        
        if (!upvoteBtn || !downvoteBtn || !voteCount) return;

        const handleVote = async (type) => {
            if (!upvoteBtn.dataset.postId) return;

            // Disable buttons while processing
            upvoteBtn.disabled = true;
            downvoteBtn.disabled = true;

            try {
                const response = await fetch(directory_voting_ajax.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'directory_vote',
                        nonce: directory_voting_ajax.nonce,
                        listing_id: upvoteBtn.dataset.postId,
                        vote_type: type
                    })
                });

                const data = await response.json();
                if (data.success) {
                    // Update vote count
                    voteCount.textContent = data.data.score;
                    
                    // Update button states
                    if (type === 'Upvote') {
                        upvoteBtn.classList.add('active');
                        downvoteBtn.classList.remove('active');
                    } else {
                        downvoteBtn.classList.add('active');
                        upvoteBtn.classList.remove('active');
                    }
                } else {
                    alert(data.data || 'Error processing vote');
                }
            } catch (error) {
                console.error('Voting error:', error);
                alert('There was an error processing your vote. Please try again.');
            } finally {
                // Re-enable buttons
                upvoteBtn.disabled = false;
                downvoteBtn.disabled = false;
            }
        };

        upvoteBtn.addEventListener('click', () => handleVote('Upvote'));
        downvoteBtn.addEventListener('click', () => handleVote('Downvote'));
    };

    // Enhanced Quick Contact Form with Lead Management
    const setupQuickContactForm = () => {
        const form = document.getElementById('quick-contact-form');
        const response = document.getElementById('form-response');
        if (!form) return;

        const showResponse = (message, type) => {
            const responseDiv = document.createElement('div');
            responseDiv.className = `form-response ${type}`;
            responseDiv.textContent = message;
            form.appendChild(responseDiv);

            setTimeout(() => {
                responseDiv.remove();
            }, 5000);
        };

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            try {
                // Get listing type from data attribute
                const listingType = form.getAttribute('data-listing-type');
                const formData = new FormData(form);
                formData.append('action', 'handle_quick_contact');
                formData.append('listing_type', listingType);

                const response = await fetch(directory_listings_ajax.ajax_url, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    form.reset();
                    showResponse(data.data.message, 'success');
                    
                    // Additional actions for free listings
                    if (listingType === 'free') {
                        const leadTrackingDiv = document.createElement('div');
                        leadTrackingDiv.className = 'lead-tracking';
                        leadTrackingDiv.innerHTML = `
                            <p>Your inquiry reference number: ${data.data.lead_id}</p>
                            <p>Use this number when following up on your inquiry.</p>
                        `;
                        form.appendChild(leadTrackingDiv);
                        
                        setTimeout(() => {
                            leadTrackingDiv.remove();
                        }, 10000);
                    }
                } else {
                    throw new Error(data.data.message || 'Failed to send message');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                showResponse('There was an error sending your message. Please try again later.', 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    };

    // Business Hours
    const updateBusinessHours = () => {
        const hoursItems = document.querySelectorAll('.hours-item');
        const now = new Date();
        const currentDay = now.toLocaleDateString('en-US', { weekday: 'lowercase' });
        
        hoursItems.forEach(item => {
            const daySpan = item.querySelector('.day');
            if (daySpan && daySpan.textContent.toLowerCase() === currentDay) {
                item.classList.add('current-day');
            } else {
                item.classList.remove('current-day');
            }
        });
    };

    // Gallery
    const setupGallery = () => {
        const galleryItems = document.querySelectorAll('.gallery-item');
        const modal = document.querySelector('.photo-modal');
        if (!galleryItems.length || !modal) return;

        let currentIndex = 0;

        const showImage = (index) => {
            const items = Array.from(galleryItems);
            currentIndex = (index + items.length) % items.length;
            const item = items[currentIndex];
            const img = item.querySelector('img');
            const modalImg = modal.querySelector('img');
            modalImg.src = img.src;
            modalImg.alt = img.alt;

            // Update caption if exists
            const caption = modal.querySelector('.modal-caption');
            if (caption) {
                const title = item.querySelector('h4');
                const desc = item.querySelector('p');
                if (title) caption.querySelector('h3').textContent = title.textContent;
                if (desc) caption.querySelector('p').textContent = desc.textContent;
            }
        };

        galleryItems.forEach((item, index) => {
            item.querySelector('.gallery-zoom')?.addEventListener('click', (e) => {
                e.preventDefault();
                currentIndex = index;
                showImage(currentIndex);
                modal.classList.add('active');
            });
        });

        // Modal controls
        modal.querySelector('.modal-close')?.addEventListener('click', () => {
            modal.classList.remove('active');
        });

        modal.querySelector('.modal-prev')?.addEventListener('click', () => {
            showImage(currentIndex - 1);
        });

        modal.querySelector('.modal-next')?.addEventListener('click', () => {
            showImage(currentIndex + 1);
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (!modal.classList.contains('active')) return;
            
            if (e.key === 'Escape') modal.classList.remove('active');
            else if (e.key === 'ArrowLeft') showImage(currentIndex - 1);
            else if (e.key === 'ArrowRight') showImage(currentIndex + 1);
        });

        // Close on outside click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.remove('active');
        });
    };

    // Initialize all features
    setupVoting();
    setupQuickContactForm();
    updateBusinessHours();
    setupGallery();

    // Update business hours every minute
    setInterval(updateBusinessHours, 60000);
});
    </script>
</body>
</html>
<?php
    endwhile;
endif;
get_footer();
?>