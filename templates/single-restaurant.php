<?php
/*
 * Template Name: Awesome Restaurant Listing
 */

get_header();

// Ensure we have a post to work with
if (have_posts()) : 
    while (have_posts()) : the_post();

    // Get custom field values
    $logo = get_field('restaurant_logo');
    $phone = get_field('phone_number');
    $email = get_field('email');
    $website = get_field('website');
    $street_address = get_field('street_address');
    $city = get_field('city');
    $state = get_field('state');
    $zip_code = get_field('zip_code');
    $address = $street_address . ', ' . $city . ', ' . $state . ' ' . $zip_code;
    $rating = get_field('overall_rating');
    $rating = is_numeric($rating) ? floatval($rating) : 0;
    $review_count = get_field('review_count');
    $review_count = is_numeric($review_count) ? intval($review_count) : 0;
    $cuisine_types = get_field('cuisine_types') ?: array();
    $menu_url = get_field('menu_url') ?: '';
    $gallery = get_field('restaurant_gallery') ?: array();
    $opening_hours = get_field('opening_hours') ?: array();
    $delivery_options = get_field('delivery_options') ?: array();
    $reservations_accepted = get_field('reservation_required') ?: false;
    $signature_dishes = get_field('signature_dishes') ?: array();
    $alcohol_served = get_field('alcohol_served') ?: '';
    $parking_options = get_field('parking_options') ?: array();
    $wheelchair_accessible = get_field('wheelchair_accessible') ?: false;
    $noise_level = get_field('noise_level') ?: '';
    $price_range = get_field('price_range') ?: '';
    $social_links = get_field('social_media_links') ?: array();
    $is_verified = get_field('is_verified') ?: false;
    $is_top_rated = get_field('top_rated') ?: false;
    $hero_images = array_slice($gallery, 0, 5); // 5 images for the hero slideshow

    // Function to check if restaurant is currently open
    function is_restaurant_open($opening_hours) {
        $current_day = strtolower(date('l'));
        $current_time = date('H:i');
        
        foreach ($opening_hours as $day) {
            if ($day['day'] === $current_day) {
                $open_time = date('H:i', strtotime($day['open_time']));
                $close_time = date('H:i', strtotime($day['close_time']));
                
                if ($current_time >= $open_time && $current_time <= $close_time) {
                    return true;
                }
            }
        }
        return false;
    }

    $is_open = is_restaurant_open($opening_hours);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html(get_the_title()); ?> | Restaurant Listing</title>
    <?php wp_head(); ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --primary-color: #ff6b6b;
        --secondary-color: #4ecdc4;
        --text-color: #2d3436;
        --background-color: #f9f9f9;
        --card-background: #ffffff;
        --accent-color: #feca57;
        --dark-overlay: rgba(0, 0, 0, 0.7);
    }

    body {
        font-family: 'Poppins', sans-serif;
        line-height: 1.6;
        color: var(--text-color);
        background-color: var(--background-color);
        margin: 0;
        padding: 0;
    }

    .restaurant-hero {
        position: relative;
        height: calc(100vh - 80px); /* Adjust 80px to match your header height */
        min-height: 500px;
        overflow: hidden;
        margin-top: -1px;
    }

    .hero-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 1s ease-in-out;
        background-size: cover;
        background-position: center;
    }

    .hero-slide.active {
        opacity: 1;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0.3), var(--dark-overlay));
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding: 20px;
    }

    .hero-buttons {
        display: flex;
        justify-content: center;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        padding: 10px 0;
        margin-bottom: 20px;
    }

    .hero-content {
        text-align: center;
        color: white;
        z-index: 1;
        max-width: 800px;
        width: 100%;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .restaurant-logo {
        width: 180px;
        height: 180px;
        object-fit: cover;
        border-radius: 50%;
        border: 6px solid var(--accent-color);
        margin-bottom: 30px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        transition: transform 0.3s ease;
    }

    .restaurant-logo:hover {
        transform: scale(1.05);
    }

    .restaurant-name {
        font-family: 'Playfair Display', serif;
        font-size: 4em;
        margin: 0 0 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }

    .restaurant-cuisine {
        font-size: 1.4em;
        margin-bottom: 30px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }

    .restaurant-rating {
        font-size: 1.8em;
        margin-bottom: 30px;
    }

    .star {
        color: var(--accent-color);
        transition: transform 0.2s ease;
    }

    .star:hover {
        transform: scale(1.2);
    }

    .restaurant-status {
        font-size: 1.2em;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .status-open {
        color: #4CAF50;
    }

    .status-closed {
        color: #F44336;
    }

    .contact-info {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .contact-item {
        margin: 10px 20px;
        display: flex;
        align-items: center;
        transition: transform 0.3s ease;
    }

    .contact-item:hover {
        transform: translateY(-5px);
    }

    .contact-item i {
        margin-right: 10px;
        font-size: 1.4em;
    }

    .cta-button, .menu-link {
        display: inline-block;
        background-color: var(--accent-color);
        color: var(--text-color);
        padding: 15px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: bold;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin: 0 10px;
    }

    .cta-button:hover, .menu-link:hover {
        background-color: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.2);
    }

    .cta-button.secondary {
        background-color: transparent;
        border: 2px solid var(--accent-color);
        color: var(--accent-color);
    }

    .cta-button.secondary:hover {
        background-color: var(--accent-color);
        color: var(--text-color);
    }

    .restaurant-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        position: relative;
        z-index: 2;
    }

    .restaurant-info {
        background-color: var(--card-background);
        border-radius: 20px;
        padding: 60px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        margin-top: -100px;
    }

    h2 {
        font-family: 'Playfair Display', serif;
        color: var(--primary-color);
        margin-top: 0;
        font-size: 2.5em;
        position: relative;
        padding-bottom: 15px;
    }

    h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 4px;
        background-color: var(--accent-color);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 40px;
        margin-top: 40px;
    }

    .info-card {
        background-color: var(--background-color);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .info-card h3 {
        color: var(--secondary-color);
        margin-top: 0;
        font-size: 1.6em;
        margin-bottom: 20px;
        border-bottom: 2px solid var(--accent-color);
        padding-bottom: 10px;
    }

    .hours-list, .info-list {
        list-style-type: none;
        padding: 0;
    }

    .hours-list li, .info-list li {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .hours-list li:last-child, .info-list li:last-child {
        border-bottom: none;
    }

    .day-name {
        font-weight: bold;
        text-transform: capitalize;
    }

    .closed {
        color: var(--primary-color);
    }

    .info-label {
        font-weight: bold;
    }

    .info-value {
        text-align: right;
    }

    .signature-dishes {
        list-style-type: none;
        padding: 0;
    }

    .signature-dishes li {
        margin-bottom: 15px;
    }

    .dish-name {
        font-weight: bold;
        color: var(--secondary-color);
    }

    .dish-description {
        font-style: italic;
        color: #666;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .gallery-item img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        transition: all 0.5s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.1);
    }

    .gallery-item::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0), rgba(0,0,0,0.7));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .gallery-item:hover::after {
        opacity: 1;
    }

    .social-links {
        margin-top: 50px;
        text-align: center;
    }

    .social-links h3 {
        font-size: 1.8em;
        margin-bottom: 20px;
    }

    .social-links a {
        display: inline-block;
        margin: 0 15px;
        color: var(--secondary-color);
        font-size: 28px;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        color: var(--primary-color);
        transform: translateY(-5px);
    }

    .badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        margin-right: 10px;
    }

    .badge-verified {
        background-color: #4CAF50;
        color: white;
    }

    .badge-top-rated {
        background-color: #FFD700;
        color: black;
    }

    .review-section {
        margin-top: 60px;
        background-color: var(--card-background);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .review-section h2 {
        margin-bottom: 30px;
    }

    .single-review {
        border-bottom: 1px solid #eee;
        padding: 20px 0;
    }

    .single-review:last-child {
        border-bottom: none;
    }

    .review-photos {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }

    .review-photo {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
    }

    .restaurant-review-form-container {
        margin-top: 40px;
    }

    .restaurant-review-form-container h3 {
        margin-bottom: 20px;
    }

    #submit-restaurant-review-form .form-group {
        margin-bottom: 20px;
    }

    #submit-restaurant-review-form label {
        display: block;
        margin-bottom: 5px;
    }

    #submit-restaurant-review-form input[type="text"],
    #submit-restaurant-review-form input[type="email"],
    #submit-restaurant-review-form textarea,
    #submit-restaurant-review-form select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    #submit-restaurant-review-form button {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #submit-restaurant-review-form button:hover {
        background-color: var(--secondary-color);
    }

    .rating-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }

    .rating-input input {
        display: none;
    }

    .rating-input label {
        cursor: pointer;
        font-size: 30px;
        color: #ddd;
    }

    .rating-input label:before {
        content: '★';
    }

    .rating-input input:checked ~ label {
        color: #ffd700;
    }

    #photo-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    #photo-preview img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
    }

    @media (max-width: 768px) {
        .restaurant-hero {
            height: auto;
            min-height: calc(100vh - 60px);
        }

        .hero-overlay {
            position: relative;
            height: auto;
        }

        .hero-buttons {
            flex-direction: column;
            background-color: transparent;
            padding: 0;
            margin-bottom: 30px;
        }

        .cta-button, .menu-link {
            width: 100%;
            max-width: 300px;
            margin: 10px 0;
        }

        .hero-content {
            padding: 40px 20px;
        }

        .restaurant-container {
            padding: 20px;
        }

        .restaurant-info {
            margin-top: -60px;
            padding: 40px;
        }

        .contact-info {
            flex-direction: column;
            align-items: center;
        }

        .contact-item {
            margin: 15px 0;
        }
    }
    </style>
</head>
<body>
    <div class="restaurant-hero">
        <?php foreach ($hero_images as $index => $image) : ?>
            <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" style="background-image: url('<?php echo esc_url($image['url']); ?>');">
            </div>
        <?php endforeach; ?>
        <div class="hero-overlay">
            <div class="hero-buttons">
                <?php if ($address) : ?>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($address); ?>" class="cta-button" target="_blank">
                        Get Directions
                    </a>
                <?php endif; ?>
                <?php if ($reservations_accepted) : ?>
                    <a href="#reservation" class="cta-button secondary">
                        Make a Reservation
                    </a>
                <?php endif; ?>
                <?php if ($menu_url) : ?>
                    <a href="<?php echo esc_url($menu_url); ?>" class="menu-link" target="_blank">View Menu</a>
                <?php endif; ?>
            </div>
            <div class="hero-content">
                <?php if ($logo) : ?>
                    <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr(get_the_title()); ?> Logo" class="restaurant-logo">
                <?php endif; ?>
                <h1 class="restaurant-name"><?php the_title(); ?></h1>
                <?php if ($is_verified) : ?>
                    <span class="badge badge-verified">Verified</span>
                <?php endif; ?>
                <?php if ($is_top_rated) : ?>
                    <span class="badge badge-top-rated">Top Rated</span>
                <?php endif; ?>
                <p class="restaurant-cuisine"><?php echo esc_html(implode(' • ', $cuisine_types)); ?></p>
                <div class="restaurant-rating">
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $rating ? '<span class="star" data-rating="' . $i . '">★</span>' : '<span class="star" data-rating="' . $i . '">☆</span>';
                    }
                    ?>
                    <span>(<?php echo $review_count; ?> reviews)</span>
                </div>
                <div class="restaurant-status">
                    <?php if ($is_open) : ?>
                        <span class="status-open">Open Now</span>
                    <?php else : ?>
                        <span class="status-closed">Closed</span>
                    <?php endif; ?>
                </div>
                <div class="contact-info">
                    <?php if ($address) : ?>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo esc_html($address); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($phone) : ?>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span><?php echo esc_html($phone); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($email) : ?>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span><?php echo esc_html($email); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="restaurant-container">
        <div class="restaurant-info">
            <h2>About <?php the_title(); ?></h2>
            <?php the_content(); ?>

            <div class="info-grid">
                <div class="info-card">
                    <h3>Opening Hours</h3>
                    <ul class="hours-list">
                        <?php
                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        foreach ($opening_hours as $day_hours) :
                            $day = $day_hours['day'];
                            $open_time = $day_hours['open_time'];
                            $close_time = $day_hours['close_time'];
                        ?>
                            <li>
                                <span class="day-name"><?php echo esc_html(ucfirst($day)); ?></span>
                                <span class="hours">
                                    <?php
                                    if ($open_time && $close_time) {
                                        echo esc_html("$open_time - $close_time");
                                    } else {
                                        echo '<span class="closed">Closed</span>';
                                    }
                                    ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="info-card">
                    <h3>Additional Information</h3>
                    <ul class="info-list">
                        <li>
                            <span class="info-label">Price Range:</span>
                            <span class="info-value"><?php echo esc_html($price_range); ?></span>
                        </li>
                        <li>
                            <span class="info-label">Delivery Options:</span>
                            <span class="info-value"><?php echo esc_html(implode(', ', array_map('ucfirst', $delivery_options))); ?></span>
                        </li>
                        <li>
                            <span class="info-label">Reservations:</span>
                            <span class="info-value"><?php echo $reservations_accepted ? 'Accepted' : 'Not required'; ?></span>
                        </li>
                        <li>
                            <span class="info-label">Alcohol:</span>
                            <span class="info-value"><?php echo esc_html(str_replace('_', ' ', ucfirst($alcohol_served))); ?></span>
                        </li>
                        <li>
                            <span class="info-label">Parking:</span>
                            <span class="info-value"><?php echo esc_html(implode(', ', array_map('ucfirst', $parking_options))); ?></span>
                        </li>
                        <li>
                            <span class="info-label">Wheelchair Accessible:</span>
                            <span class="info-value"><?php echo $wheelchair_accessible ? 'Yes' : 'No'; ?></span>
                        </li>
                        <li>
                            <span class="info-label">Noise Level:</span>
                            <span class="info-value"><?php echo esc_html(ucfirst($noise_level)); ?></span>
                        </li>
                    </ul>
                </div>

                <?php if ($signature_dishes) : ?>
                    <div class="info-card">
                        <h3>Signature Dishes</h3>
                        <ul class="signature-dishes">
                            <?php foreach ($signature_dishes as $dish) : ?>
                                <li>
                                    <span class="dish-name"><?php echo esc_html($dish['dish_name']); ?></span>
                                    <?php if (!empty($dish['description'])) : ?>
                                        <p class="dish-description"><?php echo esc_html($dish['description']); ?></p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($gallery) : ?>
                <h2>Photo Gallery</h2>
                <div class="gallery-grid">
                    <?php foreach ($gallery as $image) : ?>
                        <div class="gallery-item">
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" loading="lazy">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($social_links) : ?>
                <div class="social-links">
                    <h3>Follow Us</h3>
                    <?php foreach ($social_links as $platform => $url) : ?>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-<?php echo esc_attr(strtolower($platform)); ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="review-section">
            <h2>Customer Reviews</h2>
            <?php
            // Display existing reviews
            if (function_exists('display_restaurant_reviews')) {
                display_restaurant_reviews(get_the_ID());
            } else {
                echo '<p>Reviews are currently unavailable.</p>';
            }
            ?>

            <div class="restaurant-review-form-container">
                <h3>Leave a Review</h3>
                <form id="submit-restaurant-review-form" enctype="multipart/form-data">
                    <?php wp_nonce_field('submit_restaurant_review', 'restaurant_review_nonce'); ?>
                    <input type="hidden" name="action" value="submit_restaurant_review">
                    <input type="hidden" name="restaurant_id" value="<?php echo get_the_ID(); ?>">
                    
                    <div class="form-group">
                        <label for="reviewer_name">Your Name</label>
                        <input type="text" id="reviewer_name" name="reviewer_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reviewer_email">Your Email</label>
                        <input type="email" id="reviewer_email" name="reviewer_email" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Overall Rating</label>
                        <div class="rating-input">
                            <input type="radio" id="star5" name="rating" value="5" required><label for="star5"></label>
                            <input type="radio" id="star4" name="rating" value="4"><label for="star4"></label>
                            <input type="radio" id="star3" name="rating" value="3"><label for="star3"></label>
                            <input type="radio" id="star2" name="rating" value="2"><label for="star2"></label>
                            <input type="radio" id="star1" name="rating" value="1"><label for="star1"></label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="review_text">Your Review</label>
                        <textarea id="review_text" name="review_text" rows="5" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="review_photos">Upload Photos (Max 5)</label>
                        <input type="file" name="review_photos[]" id="review_photos" multiple accept="image/*" max="5">
                        <div id="photo-preview"></div>
                    </div>

                    <button type="submit">Submit Review</button>
                </form>
            </div>
        </div>
    </div>

    <?php wp_footer(); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script>
        // Hero Slideshow
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        
        function showSlide(index) {
           gsap.to(slides[currentSlide], { opacity: 0, duration: 1 });
            gsap.to(slides[index], { opacity: 1, duration: 1 });
            currentSlide = index;
        }

        function nextSlide() {
            let next = (currentSlide + 1) % slides.length;
            showSlide(next);
        }

        setInterval(nextSlide, 5000); // Change slide every 5 seconds

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Lazy load images
        if ("IntersectionObserver" in window) {
            let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        let lazyImage = entry.target;
                        lazyImage.src = lazyImage.dataset.src;
                        lazyImage.classList.remove("lazy");
                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
            });

            document.querySelectorAll("img.lazy").forEach(function(lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
        }

        // Animate elements on scroll
        gsap.registerPlugin(ScrollTrigger);

        gsap.utils.toArray('.info-card, .gallery-item').forEach((element) => {
            gsap.from(element, {
                y: 50,
                opacity: 0,
                duration: 1,
                scrollTrigger: {
                    trigger: element,
                    start: 'top 80%',
                    end: 'top 20%',
                    scrub: 1,
                }
            });
        });

        // Interactive star rating
        const stars = document.querySelectorAll('.star');
        stars.forEach(star => {
            star.addEventListener('mouseover', () => {
                const rating = star.dataset.rating;
                stars.forEach(s => {
                    if (s.dataset.rating <= rating) {
                        s.textContent = '★';
                    } else {
                        s.textContent = '☆';
                    }
                });
            });
        });

        const ratingContainer = document.querySelector('.restaurant-rating');
        ratingContainer.addEventListener('mouseout', () => {
            stars.forEach((star, index) => {
                if (index < <?php echo $rating; ?>) {
                    star.textContent = '★';
                } else {
                    star.textContent = '☆';
                }
            });
        });

        // Photo preview for review form
        document.getElementById('review_photos').addEventListener('change', function(event) {
            const preview = document.getElementById('photo-preview');
            preview.innerHTML = '';
            const files = event.target.files;

            for (let i = 0; i < files.length && i < 5; i++) {
                const file = files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }

                reader.readAsDataURL(file);
            }
        });

        // Handle review form submission
        document.getElementById('submit-restaurant-review-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);

            fetch(ajaxurl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Review submitted successfully!');
                    this.reset();
                    document.getElementById('photo-preview').innerHTML = '';
                    // Optionally, refresh the reviews section here
                } else {
                    alert('Error submitting review: ' + data.data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    </script>
</body>
</html>

<?php
    endwhile;
endif;
get_footer();
?>